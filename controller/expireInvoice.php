<?php
    $rootIP = 'lumintu-tiket.tamiaindah.xyz:8055';

    $invoiceURL = 'http://' . $rootIP . '/items/invoice';
    
    $invoiceData = getInvoiceData($invoiceURL);
    $invoiceLength = $invoiceData['data'];

    $currentTgl = new DateTime();

    for ($i=0; $i < sizeof($invoiceLength); $i++) { 
        $tgl = new DateTime($invoiceData['data'][$i]['invoice_date']);
        // echo $tgl->format('c') . ' || ';
        $intervalTgl = $tgl->add(new DateInterval("P1D"));

        echo $invoiceData['data'][$i]['invoice_id'];

        if ($intervalTgl < $currentTgl) {
            setExpire($invoiceURL, $invoiceData['data'][$i]['invoice_id']);
            echo ' expired ';
        }else {
            echo ' not expire ';
        }
    }

    function getInvoiceData($link){
        $curl = curl_init();
        //      get all invoice
        curl_setopt($curl, CURLOPT_URL, $link . '?filter[status]=pending');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $responseInvID = curl_exec($curl);
        $result = json_decode($responseInvID, true);

        curl_close($curl);

        return $result;
    }

    function setExpire($link, $invoiceID){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $link . '/' . $invoiceID,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_POSTFIELDS =>'{
            "status": "expire"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        $postStatus = json_decode($response, true);

        curl_close($curl);

        if (isset($postStatus['errors'][0]['extensions']['code'])) {
            return 'err';
        }else {
            return 'bisa update';
        }
    }
?>