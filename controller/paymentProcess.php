<?php

    $rootIP = 'lumintu-tiket.tamiaindah.xyz:8055';


    $paymentURL = 'http://' . $rootIP . '/items/payment';
    $invoiceURL = 'http://' . $rootIP . '/items/invoice';

    if ($_SERVER["REQUEST_METHOD"] = "POST") {
        $payloadData = json_decode(file_get_contents('php://input'), true);

        $transaction_id = $payloadData['transaction_id'];
        $transaction_time = strtotime($payloadData['transaction_item']);
        $transaction_status = $payloadData['transaction_status'];
        $gross_amount = (int)$payloadData['gross_amount'];
        $payment_id = $payloadData['payment_id'];
        $payment_type = $payloadData['payment_type'];
        $invoice_id = $payloadData['order_id'];

        $signatureKey = $payloadData['signature_key'];
        $status_code = $payloadData['status_code'];

        $serverKey = 'SB-Mid-server-Mw-74IcQzJihhBv4MFX1N9fD';
        $tempKey = $invoice_id . $status_code . $gross_amount . $serverKey;
        $mySignatureKey = openssl_digest($tempKey, 'sha512');

        if ($signatureKey = $mySignatureKey) {

            $curl = curl_init();

            # check if payment exist
            curl_setopt($curl, CURLOPT_URL, $paymentURL . '?filter[invoice_id]=' . $invoice_id);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($curl);
            $result = json_decode($response, true);
            $dataLength = $result["data"];
            $paymentStatus = $result['data'][0]['status'];

            curl_close($curl);

            // echo sizeof($dataLength);

            if (sizeof($dataLength) > 0) {
                if ($paymentStatus = 'pending' || $paymentStatus = 'settlement' || $paymentStatus = 'capture') {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $invoiceURL . '/' . $invoice_id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'PATCH',
                    CURLOPT_POSTFIELDS =>'{
                        "status": "' . $transaction_status . '"
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                    ));

                    $response = curl_exec($curl);
                    $postStatus = json_decode($response, true);

                    curl_close($curl);

                    if (isset($postStatus['errors'][0]['extensions']['code'])) {
                        echo 'gak bisa update';
                    }else {
                        echo ' bsia update ';
                    }
                }
            }else {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $paymentURL,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
                    "invoice_id": " '. $invoice_id .' ",
                    "transaction_id": " ' . $transaction_id . ' ",
                    "payment_total": " ' . $gross_amount . ' ",
                    "payment_type": " ' . $payment_type . ' "
                }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));

                $getResponse = curl_exec($curl);
                $onCreateResponsePayment = json_decode($getResponse, true);

                curl_close($curl);

                if (!isset($onCreateResponsePayment['errors'][0]['extensions']['code'])) {
                    echo 'gak bisa ngepost';
                }else {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $invoiceURL . '/' . $invoice_id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'PATCH',
                    CURLOPT_POSTFIELDS =>'{
                        "status": "' . $transaction_status . '"
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                    ));

                    $response = curl_exec($curl);
                    $postStatus = json_decode($response, true);

                    curl_close($curl);

                    if (isset($postStatus['errors'][0]['extensions']['code'])) {
                        // echo $postStatus['errors'][0]['extensions']['code'];
                        // echo 'gak bisa update di post';
                    }else {
                        echo ' bsia update post ';
                    }
                }
            }

        }else {
            echo 'err signature key';
        }
    }

?>