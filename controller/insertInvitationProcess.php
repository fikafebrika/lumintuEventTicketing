<?php

    session_start();
    $cred = $_SESSION['cred'];
    $buyTicketLink = 'http://localhost/intern/ticketing/view/statuspesanan.php';
    $bioLink = 'http://localhost/intern/ticketing/view/invitation.php';
    $customerURL = 'http://lumintu-tiket.tamiaindah.xyz:8055/items/customer';
    $invitationURL = 'http://lumintu-tiket.tamiaindah.xyz:8055/items/invitation';
    $voucherURL = 'http://lumintu-tiket.tamiaindah.xyz:8055/items/voucher';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/OAuth.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/POP3.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';

    $numberOfPost = count($_POST);

    echo $numberOfPost;

    $counter = 0;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $customerURL . '?&filter[customer_code]=' . $cred);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $responseID = curl_exec($curl);
    $resultID = json_decode($responseID, true);
    $inviterEmail = $resultID['data'][0]['customer_email'];
    $inviterID = $resultID['data'][0]['customer_id'];

    curl_close($curl);

    $voucherID = 0;

    if (!empty($_POST['voucher'])) {

        $voucherData = getVoucher($voucherURL, $_POST['voucher']);
        setInviterData($invitationURL, $inviterID, $voucherData['data'][0]['voucher_id']);
        $sendEmailStatus = sendInviterEmail($inviterEmail, $buyTicketLink, $cred, $voucherID);

        if ($sendEmailStatus == 'scs') {
            header('Location: ../view/details.php?' . $sendEmailStatus);
        }else {
            header('Location: ../view/details.php?' . $sendEmailStatus);
        }

    }
    elseif ($numberOfPost == 2) {

        if ($inviterEmail == $_POST['peserta1']) {

            setInviterData($invitationURL, $inviterID, $voucherID);
            $sendEmailStatus = sendInviterEmail($inviterEmail, $buyTicketLink, $cred, $voucherID);

            if ($sendEmailStatus == 'scs') {
                header('Location: ../view/details.php?' . $sendEmailStatus);
            }else {
                header('Location: ../view/details.php?' . $sendEmailStatus);
            }

        }
    }else{
        $x = 2;
        do {
            if ($_POST['peserta'.$x] != ''){
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $customerURL . '?&filter[customer_email]=' . $_POST['peserta'.$x]);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $responseID = curl_exec($curl);
                $resultID = json_decode($responseID, true);

                if (isset($resultID['data'][0]['customer_email'])){
                    $counter++;
                }

                curl_close($curl);

                $x++;
            }
        } while (isset($_POST['peserta'.$x]));

        if ($counter == 0){

            setInviterData($invitationURL, $inviterID, $voucherID);

            for ($x = 2; $x < $numberOfPost; $x++){
                $pesertaEmail = $_POST['peserta'.$x];
                
                $getCusStatus = setCustomer($customerURL, $pesertaEmail);

                if (!isset($getCusStatus['errors'][0]['extensions']['code'])){
                    $curl = curl_init();

                    curl_setopt($curl, CURLOPT_URL, $customerURL . '?&filter[customer_email]=' . $pesertaEmail);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    $responseID = curl_exec($curl);
                    $resultID = json_decode($responseID, true);

                    curl_close($curl);

                    $invitedStatus = setInvitedData($invitationURL, $resultID, $inviterID);

                    if (!isset($invitedStatus['errors'][0]['extensions']['code'])){

                    }else{
                        header('Location: ../view/details.php?errOnInv');
                    }
                }
                else{
                    header('Location: ../view/details.php?errCus');
                }
            }
            
            $sendEmailStatus = sendInviterEmail($inviterEmail, $buyTicketLink, $cred, $voucherID);

            if ($sendEmailStatus == 'scs') {
                for ($i=2; $i < $numberOfPost; $i++) { 
                    $mailStatus = sendInvitedEmail($pesertaEmail, $inviterEmail, $bioLink);

                    if ($mailStatus != 'scs'){
                        echo 'ajg';
                    }
                }

                echo 'done';
            }
            header('Location: ../view/details.php?' . $mailStatus);
        }else{
            header('Location: ../view/details.php?dupEm');
        }
    }

    function getVoucher($link, $voucher_code){
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $link . '?fields=voucher_id&filter[voucher_code]=' . $voucher_code);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        $result = json_decode($response, true);

        curl_close($curl);

        return $result;
    }

    function setInviterData($link, $customerID, $voucherID){

        if ($voucherID == 0) {
            $payload = '{
                "customer_id": "' . $customerID . '",
                "customer_inviter_id": " '. $customerID .' ",
                "invitation_status": "1"
            }';
        }else {
            $payload = '{
                "customer_id": "' . $customerID . '",
                "customer_inviter_id": " '. $customerID .' ",
                "invitation_status": "1",
                "voucher_id": "' . $voucherID . '"
            }';
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $getResponse = curl_exec($curl);
        $onCreateResponseInvitation = json_decode($getResponse, true);

        curl_close($curl);

        return $onCreateResponseInvitation;
    }

    function setInvitedData($link, $customerData, $inviterID){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "customer_id": "' . $customerData['data'][0]['customer_id'] . '",
            "customer_inviter_id": " '. $inviterID .' ",
            "invitation_status": "0"
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $getResponse = curl_exec($curl);
        $onCreateResponseInvitation = json_decode($getResponse, true);

        curl_close($curl);

        return $onCreateResponseInvitation;
    }

    function setCustomer($link, $customerEmail){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "customer_email": "' . $customerEmail . '"
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $getResponse = curl_exec($curl);
        $onCreateResponseCustomer = json_decode($getResponse, true);

        curl_close($curl);

        return $onCreateResponseCustomer;
    }

    function sendInviterEmail($receiverEmail, $redirectLink, $cred, $voucher_id){
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

        $mail->addAddress($receiverEmail);
        $mail->Subject = "[Lumintu Events] Link Pemesanan Tiket";
        $mail->isHTML(true);

        $mailLocation = '../view/email/emailToOrder.html';
        $message = file_get_contents($mailLocation);
        $message = str_replace('%inviterMail%', $receiverEmail, $message);
        if ($voucherID == 0) {
            $message = str_replace('%link%', $redirectLink . '?m=' . $cred, $message);
        }else {
            $message = str_replace('%link%', $redirectLink . '?m=' . $cred . '&voucher_id=' . $voucher_id, $message);
        }
        $mail->msgHTML($message);

        if ($mail->send()) {
            $mail->clearAddresses();
            return 'scs';
        }else {
            return $mail->ErrorInfo;
        }
    }

    function sendInvitedEmail($invitedEmail, $inviterEmail, $redirectLink){
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

        $mail->addAddress($invitedEmail);
        $mail->Subject = "[Lumintu Events] Link Pengisian Biodata Pemesanan Tiket";
        $mail->isHTML(true);
        
        $mailLocation = '../view/email/emailInvitation.html';
        $message = file_get_contents($mailLocation);
        $message = str_replace('%receiverMail%', $invitedEmail, $message);
        $message = str_replace('%inviterMail%', $inviterEmail, $message);
        $message = str_replace('%link%', $redirectLink . '?invm=' . base64_encode($invitedEmail), $message);
        $mail->msgHTML($message);

        if ($mail->send()) {
            $mail->clearAddresses();
            return 'scs';
        }else {
            return 'errInvited';
        }
    }

?>