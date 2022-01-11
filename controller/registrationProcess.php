<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/OAuth.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/POP3.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';

//    include('../config.php');

    $customerURL = 'http://lumintu-tiket.tamiaindah.xyz:8055/items/customer';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phoneNum = $_POST['phoneNum'];
    $cusCode = hash('sha512', $email.$phoneNum);
    $loginLink = 'http://localhost/intern/ticketing/controller/verificationProcess.php?m=' . $cusCode;

    $curl = curl_init();

    # check if customer exist
    curl_setopt($curl, CURLOPT_URL, $customerURL . '?filter[customer_email]=' . $email);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    $result = json_decode($response, true);
    $dataLength = $result["data"];

    curl_close($curl);

    if (sizeof($dataLength) > 0) {
        header('Location: ../view/registration/registration.php?mailExist');
    }else {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $customerURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                        "customer_email": "' . $email . '",
                        "customer_name": "' . $name . '",
                        "customer_phone": "' . $phoneNum . '",
                        "customer_code": "' . $cusCode . '"
                    }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $execCurl = curl_exec($curl);
        $getResponse = json_decode($execCurl, true);

        curl_close($curl);

        if (isset($getResponse['errors'][0]['extensions']['code'])){
            echo $getResponse['errors'][0]['extensions']['code'];
        }else {

    //    $query = "INSERT INTO `customer`(`customer_email`, `customer_name`, `customer_phone`, `customer_code`)
    //                SELECT '$email', '$name', '$phoneNum', '$cusCode'
    //                FROM (SELECT 1)a WHERE NOT EXISTS (SELECT `customer_id` FROM `customer` WHERE `customer_email` = '$email')";
    //    $runQuery = mysqli_query($conn, $query) or die(mysqli_error($conn));
    //    $affRow = $conn->affected_rows;

            $mail = new PHPMailer();
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->SMTPSecure = 'tls';
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mintuticketing@gmail.com';
            $mail->Password = 'Mintu123';
            $mail->Port = 587;

            $mail->setFrom('mintuticketing@gmail.com', 'Lumintu Events');
            $mail->addAddress($email);
            $mail->Subject = "[Lumintu Events] Verifikasi Email";
            $mail->isHTML(true);
    //            $mail->Body = 'Hai ' . $name . ', silahkan klik link berikut untuk verifikasi email anda. Link ini juga digunakan untuk akses landing page<br/><br/>
    //                        <a href="' . $loginLink . '?m=' . $cusCode .'">Verifikasi Email</a>';

            $mailLocation = '../view/email/emailVerification.html';
            $message = file_get_contents($mailLocation);
            $message = str_replace('%name%', $name, $message);
            $message = str_replace('%link%', $loginLink, $message);

            $mail->msgHTML($message);

            if ($mail->send()) {
                header('Location: ../view/registration/registration.php?scs');
            } else {
    //            echo $mail->ErrorInfo;
                header('Location: ../view/registration/registration.php?mailErr');
            }
        }
    }

?>
