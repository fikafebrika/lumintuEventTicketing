<?php

    $rootIP = 'lumintu-tiket.tamiaindah.xyz:8055';


    $invitationURL = 'http://' . $rootIP . '/items/invitation';

    $curl = curl_init();
    //      get all invitation
    curl_setopt($curl, CURLOPT_URL, $invitationURL . '?&filter[customer_id]=' . $customerID);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $responseInvID = curl_exec($curl);
    $resultInvID = json_decode($responseInvID, true);
    $invID = $resultInvID['data'][0]['invoice_id'];

    curl_close($curl);

    function getInvitationData(){

    }

?>