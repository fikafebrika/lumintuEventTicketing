<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/OAuth.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/POP3.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';

    $email = $_POST['email'];
    $verificationLink = 'http://localhost/lumintuEventTicketing/view/registration/bioRegistration.php';

    echo $email;

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
    $mail->Body = 'Hai ' . $email . ', silahkan klik link berikut untuk melengkapi data anda. <br/><br/>
    <a href="' . $verificationLink . '?m=' . base64_encode($email) .'">Lengkapi Data</a>';

    if ($mail->send()){
        echo 'Email Verifikasi Dikirim';
    }
?>