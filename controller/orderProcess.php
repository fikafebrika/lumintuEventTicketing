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
            "invoice_status": 0
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

//
//        //ToPDF
//        $output = '
//            <html>
//            <style>
//                .imgEvent {
//                    margin-bottom: 32px;
//                }
//
//                .imgEvent img {
//                    height: 300px;
//                    background-size: cover;
//                    background-repeat: no-repeat;
//                    background-position: center center;
//                    margin: 10px 0;
//                }
//
//                .imgEvent p,
//                h4 {
//                    margin: 8px;
//                }
//
//                #detail {
//                    width: 100%;
//                    border-collapse: collapse;
//                    text-align: center;
//                    margin-bottom: 20px;
//                }
//
//                #detail td {
//                    border-bottom: 1pt solid grey;
//                    padding: 10px;
//                }
//
//                .leftSide {
//                    text-align: left;
//                }
//
//                .rightSide {
//                    text-align: end;
//                }
//
//                .textCenter {
//                    text-align: center;
//                }
//
//                .bank {
//                    padding: 10px 24px;
//                    margin: 10px 0;
//                }
//
//                @page { margin: 0 100px 0 100px; }
//            body { margin: 0px; }
//            </style>
//
//            <body style="margin:0;padding:0;">
//                <table role="presentation"
//                    style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
//                    <tr>
//                        <td align="center" style="padding:0;">
//                            <table role="presentation"
//                                style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
//                                <!--<tr>
//                                    <td align="center" style="padding:20px 0 0 0;background:#38435F;">
/*                                        <img src="data:image/png;base64, <?php echo $base64; ?>" alt="" width="20%" height="20%"*/
//                                            style="height:auto;display:block; padding-top: 30px;" />
//                                        <h2 style="color: #D4AF37; margin: 0 0 20px 0">Welcome to Symposium</h2>
//                                    </td>
//                                </tr>-->
//                                <tr>
//                                    <td style="padding:20px 40px;">
//                                        <table role="presentation"
//                                            style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
//                                            <tr>
//                                                <td style="color:#153643;">
//                                                    <div class="invoice">
//                                                        <div align="center" style="padding:20px 0 10px 0;background:#38435F;">
/*                                                            <img src="data:image/png;base64, <?php echo $base64; ?>" alt="" width="20%" height="20%"*/
//                                                                style="height:auto;display:block; padding-top: 30px;" />
//                                                            <h2 style="color: #D4AF37; margin: 0 0 20px 0">Welcome to Symposium</h2>
//                                                        </div>
//                                                        <div class="">
//                                                            <h2 class="textCenter">
//                                                                Thanks For You Order!
//                                                            </h2>
//                                                            <div>
//                                                                <h3 class="textCenter" style=" margin:0;line-height:24px;">
//                                                                    <u>Your Order</u>
//                                                                </h3>
//                                                                <p class="textCenter" style=" font-size: small; margin-bottom: 32px">Monday, Oct 20 2021
//                                                                    at 03.00pm</p>
//                                                                <!-- <div class="imgEvent" alt="" align="center">
//                                                                    <img src="./assets/event1.jpg" alt="">
//                                                                    <h4 style="color: #D4AF37;">Dream World Wide in Jogja</h4>
//                                                                    <p style="color: #38435F;">By Lumintu Logic</p>
//                                                                </div> -->
//                                                            </div>
//                                                            <table id="detail" align="center">
//                                                                <tr>
//                                                                    <td class="leftSide"><b>Name</b></td>
//                                                                    <td><b>Ticket</b></td>
//                                                                    <td class="rightSide"><b>Price</b></td>
//                                                                </tr>
//                    ';
//
//        for ($i = 0; $i < sizeof($length); $i++){
//            $output .= '
//                                                        <tr>
//                                                            <td class="leftSide">' . $result["data"][$i]["customer_id"]["customer_name"] . '</td>
//                                                            <td>' . $result["data"][$i]["ticket_id"]["ticket_type"] . '</td>
//                                                            <td class="rightSide">' . $result["data"][$i]["ticket_id"]["ticket_price"] . '</td>
//                                                        </tr>
//                        ';
//        }
//
//        $output .= '
//                                                        <tr>
//                                                            <td class="leftSide"><b>Total</b></td>
//                                                            <td></td>
//                                                            <td class="rightSide">' . $result["data"][0]["invoice_id"]["invoice_total"] . '</td>
//                                                        </tr>
//                        ';
//
//        $output .= '
//                                                    </table>
//                                                <div>
//                                                    <div class="bank" style="color: white; background-color: #38435F;">
//                                                        <p class="textCenter" style="font-size: 24px; margin: 0 0 20px 0;"><b>BRI</b>
//                                                        </p>
//                                                        <table align="center" class="textCenter" style="color: white;">
//                                                            <tr>
//                                                                <td style="padding-bottom: 10px; font-weight: 600;">Mohammad Arafat Maku
//                                                                </td>
//                                                            </tr>
//                                                            <tr>
//                                                                <td>720222190601002</td>
//                                                            </tr>
//                                                        </table>
//                                                    </div>
//
//                                                    <div class="bank"
//                                                        style="color: #38435F; background-color: #D4AF37;">
//                                                        <p class="textCenter" style="font-size: 24px; margin: 0 0 20px 0;"><b>BNI</b>
//                                                        </p>
//                                                        <table align="center" class="textCenter">
//                                                            <tr>
//                                                                <td style="padding-bottom: 10px; font-weight: 600;">Mohammad Arafat Maku
//                                                                </td>
//                                                            </tr>
//                                                            <tr>
//                                                                <td>720222190601002</td>
//                                                            </tr>
//                                                        </table>
//                                                    </div>
//                                                    <div class="bank" style="color: white; background-color: #38435F;">
//                                                        <p class="textCenter" style="font-size: 24px; margin: 0 0 20px 0;"><b>BCA</b>
//                                                        </p>
//                                                        <table align="center" class="textCenter" style="color: white;">
//                                                            <tr>
//                                                                <td style="padding-bottom: 10px; font-weight: 600;">Mohammad Arafat Maku
//                                                                </td>
//                                                            </tr>
//                                                            <tr>
//                                                                <td>720222190601002</td>
//                                                            </tr>
//                                                        </table>
//                                                    </div>
//                                                </div>
//                                            </div>
//                                        </div>
//                                    </td>
//                                </tr>
//                            </table>
//                        </td>
//                    </tr>
//                    <tr>
//                        <td class="textCenter" style="padding:20px 40px; background-color: #38435F;">
//                            <p
//                                style="margin:0;font-size:14px;line-height:16px;font-family:Arial,sans-serif;color:#ffffff;">
//                                &reg; Send by Lumintu Events<br />
//                            </p>
//                        </td>
//                    </tr>
//                </table>
//            </td>
//        </tr>
//    </table>
//</body>
//
//
//</html>';
//
//        $document->loadHtml($output);
//        $document->render();
//        $invoiceOutput = $document->output();
//        file_put_contents('../public/pdfFile/Invoice-' . $customerID . '.pdf', $invoiceOutput);
//    //                $document->stream('Invoice', array("Attachment"=>0));
//
//        $mail = new PHPMailer();
//        $mail->SMTPDebug = 0;
//        $mail->isSMTP();
//        $mail->SMTPSecure = 'tls';
//        $mail->Host = 'smtp.gmail.com';
//        $mail->SMTPAuth = true;
//        $mail->Username = 'mintuticketing@gmail.com';
//        $mail->Password = 'Mintu123';
//        $mail->Port = 587;
//
//        $mail->setFrom('mintuticketing@gmail.com', 'Lumintu Events');
//        $mail->addAddress($inviterEmail);
//        $mail->Subject = "[Lumintu Events] Thank you for your order.";
//        $mail->isHTML(true);
//
//        $mailLocation = '../view/email/emailInvoice.html';
//        $message = file_get_contents($mailLocation);
//        $message = str_replace('%name%', $resultID['data'][0]['customer_name'], $message);
//
//        $mail->msgHTML($message);
//        $mail->addAttachment('../public/pdfFile/Invoice-' . $customerID . '.pdf');
//
//        $mail->send();
//    }
?>
