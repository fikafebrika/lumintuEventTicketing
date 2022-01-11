<?php
    session_start();

    use PHPMailer\PHPMailer\PHPMailer;
    use Dompdf\Dompdf;

    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/OAuth.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/POP3.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';
    require "../vendor/autoload.php";

    require_once '../vendor/dompdf/dompdf/src/Autoloader.php';

    $urlIP = 'lumintu-tiket.tamiaindah.xyz:8055';

    $imagedata = file_get_contents("https://raw.githubusercontent.com/ifetayo14/lumintuEventTicketing/master/public/img/kraton.png");
    // alternatively specify an URL, if PHP settings allow
    $base64 = base64_encode($imagedata);

    $url = 'http://' . $urlIP . '/items/invitation?fields=invitation_id,customer_id.customer_id,customer_id.customer_email,customer_id.customer_name,customer_inviter_id.customer_email,invitation_status,voucher_id&filter[customer_inviter_id][customer_code]=' . $_SESSION['cred'];
    $invoiceURL = 'http://' . $urlIP . '/items/invoice';
    $customerURL = 'http://' . $urlIP . '/items/customer';
    $orderURL = 'http://' . $urlIP . '/items/order';
    $voucherURL = 'http://' . $urlIP . '/items/voucher';
    $tiketURL = 'http://' . $urlIP . '/items/ticket';

    $document = new DOMPDF('P', 'A4', 'en', false, 'UTF-8');

    $price = $_POST['total-harga'];
    $totalPrice = $_POST['total-harga'];
    $numberOfPost = count($_POST);

    $curl = curl_init();

    //get customer ID
    curl_setopt($curl, CURLOPT_URL, $customerURL . '?&filter[customer_code]=' . $_SESSION['cred']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $responseID = curl_exec($curl);
    $resultID = json_decode($responseID, true);
    $dataLengthID = $resultID["data"];
    $customerID = $resultID['data'][0]['customer_id'];
    $inviterEmail = $resultID['data'][0]['customer_email'];

    curl_close($curl);

    $curl = curl_init();

    //get customer-invitation-data
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    $result = json_decode($response, true);
    $invitationDataLength = $result["data"];
    $voucherID = $result['data'][0]['voucher_id'];

    curl_close($curl);

    if ($voucherID != null) {
        //post to invoice
        if (!insertInvoice($invoiceURL, $customerID, $totalPrice)) {
            header('Location: ../view/statuspesanan.php?errInv');
        }else{
            $invoiceData = getInvoice($invoiceURL, $customerID);
            $invID = $invoiceData['data'][0]['invoice_id'];

            if (insertOrder($orderURL, $invID, $customerID, $_POST['tiket-peserta-1'])){
                header('Location: ../view/invoice.php?m=' . $_SESSION['cred']);
            }else{
                header('Location: ../view/statuspesanan.php?errOrder');
            }
        }
    }else{
        $counter = 1;
        if (!insertInvoice($invoiceURL, $customerID, $totalPrice)){
            header('Location: ../view/statuspesanan.php?errInv');
        }else {
            do {
                $invoiceData = getInvoice($invoiceURL, $customerID);
                $invID = $invoiceData['data'][0]['invoice_id'];

                if ($_POST['tiket-peserta-' . $counter] < 4) {
                    if (insertOrder($orderURL, $invID, $customerID, $_POST['tiket-peserta-' . $counter])) {
                        header('Location: ../view/invoice.php?m=' . $_SESSION['cred']);
                    } else {
                        header('Location: ../view/statuspesanan.php?errOrder');
                    }
                } else {
                    if ($_POST['tiket-peserta-' . $counter] == 5) {
                        insertOrder($orderURL, $invID, $customerID, 1);
                        insertOrder($orderURL, $invID, $customerID, 2);
                    } elseif ($_POST['tiket-peserta-' . $counter] == 6) {
                        insertOrder($orderURL, $invID, $customerID, 1);
                        insertOrder($orderURL, $invID, $customerID, 3);
                    } elseif ($_POST['tiket-peserta-' . $counter] == 7) {
                        insertOrder($orderURL, $invID, $customerID, 2);
                        insertOrder($orderURL, $invID, $customerID, 3);
                    }
                }
                $counter++;
            } while (isset($_POST['tiket-peserta-' . $counter]));
            if ($counter > 1) {
                header('Location: ../view/invoice.php?m=' . $_SESSION['cred']);
            }
        }
    }

    function getInvoice($link, $customerID){
        $curl = curl_init();
        //      get invoice
        curl_setopt($curl, CURLOPT_URL, $link . '?&filter[customer_id]=' . $customerID);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $responseInvID = curl_exec($curl);
        $resultInvID = json_decode($responseInvID, true);
        $invID = $resultInvID['data'][0]['invoice_id'];

        curl_close($curl);

        return $resultInvID;
    }

    function insertInvoice($link, $customerID, $price){
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
            CURLOPT_POSTFIELDS => '{
            "customer_id": "' . $customerID . '",
            "invoice_total": "' . $price . '",
            "invoice_status": "pending"
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $server_response = curl_exec($curl);
        $postResponse = json_decode($server_response, true);
        curl_close($curl);

        return $postResponse;
    }

    function insertOrder($link, $invoiceID, $customerID, $ticketID){
        //post to order
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
            CURLOPT_POSTFIELDS => '{
                "invoice_id": "' . $invoiceID . '",
                "customer_id": "' . $customerID . '",
                "ticket_id": "' . $ticketID . '",
                "order_quantity": "1"
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $exec = curl_exec($curl);
        $postResponse = json_decode($exec, true);

        curl_close($curl);

        return $postResponse;
    }
?>
