<?php

    require_once dirname(__FILE__) . '../../vendor/autoload.php';
    require_once dirname(__FILE__) . '../../vendor/midtrans/midtrans-php/Midtrans.php';
    require_once dirname(__FILE__) . '../../vendor/midtrans/midtrans-php/tests/MT_Tests.php';
    require_once dirname(__FILE__) . '../../vendor/midtrans/midtrans-php/tests/utility/MtFixture.php';

    // Set your Merchant Server Key
    \Midtrans\Config::$serverKey = 'SB-Mid-server-Mw-74IcQzJihhBv4MFX1N9fD';
    // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
    \Midtrans\Config::$isProduction = false;
    // Set sanitization on (default)
    \Midtrans\Config::$isSanitized = true;
    // Set 3DS transaction for credit card to true
    \Midtrans\Config::$is3ds = true;

    $cred = $_GET['m'];

    $rootAPI = 'lumintu-tiket.tamiaindah.xyz:8055';

    $customerData = 'http://' . $rootAPI . '/items/invitation?fields=invitation_id,customer_id.customer_id,customer_id.customer_email,customer_id.customer_phone,customer_id.customer_name,customer_inviter_id.customer_email,invitation_status,voucher_id&filter[customer_inviter_id][customer_code]=' . $cred;
    $invoiceData = 'http://' . $rootAPI . '/items/invoice?filter[customer_id][customer_code]=' . $cred;
    $itemData = 'http://' . $rootAPI . '/items/order?fields=order_id,invoice_id,customer_id,ticket_id.ticket_price,ticket_id.ticket_type,voucher_id.voucher_discount&filter[customer_id][customer_code]=' . $cred;

    $curl = curl_init();

    //get customer-invitation-data
    curl_setopt($curl, CURLOPT_URL, $customerData);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $customerResponse = curl_exec($curl);
    $customerResult = json_decode($customerResponse, true);

    curl_close($curl);

    $curl = curl_init();

    //get invoice_data
    curl_setopt($curl, CURLOPT_URL, $invoiceData);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $invoiceResponse = curl_exec($curl);
    $invoiceResult = json_decode($invoiceResponse, true);

    curl_close($curl);

    $curl = curl_init();

    //get item_data
    curl_setopt($curl, CURLOPT_URL, $itemData);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $itemResponse = curl_exec($curl);
    $itemResult = json_decode($itemResponse, true);
    $itemLength = $itemResult["data"];

    curl_close($curl);

    $itemDetails = array();
    for ($i=0; $i < sizeof($itemLength); $i++) { 
        $itemDetails[] = array(
            "price" => $itemResult['data'][$i]['ticket_id']['ticket_price'],
            "quantity" => 1,
            "name" => $itemResult['data'][$i]['ticket_id']['ticket_type']
        );
    }

    $params = array(
        'transaction_details' => array(
            'order_id' => $invoiceResult['data'][0]['invoice_id'],
        ),
        'item_details' => $itemDetails,

        'customer_details' => array(
            "first_name" => $customerResult['data'][0]['customer_id']['customer_name'],
            "email" => $customerResult['data'][0]['customer_id']['customer_email'],
            "phone" => $customerResult['data'][0]['customer_id']['customer_phone']
        )
    );

    $snapToken = \Midtrans\Snap::createTransaction($params);

    $paymentLink = $snapToken->redirect_url;

    header('Location: ' . $paymentLink);

?>