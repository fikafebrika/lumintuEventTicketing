<?php

    $rootIP = 'lumintu-tiket.tamiaindah.xyz:8055';


    $invitationURL = 'http://' . $rootIP . '/items/invitation';

    $invitationData = getInvitationData($invitationURL);
    $invitationLength = $invitationData['data'];

    $currentTgl = new DateTime();
    
    // echo $tgl->format('c');
    // echo ' || ' . $currentTgl->format('c');
    for ($i=0; $i < sizeof($invitationLength); $i++) { 
        $tgl = new DateTime($invitationData['data'][$i]['invitation_date']);
        // echo $tgl->format('c') . ' || ';
        $intervalTgl = $tgl->add(new DateInterval("P1D"));

        echo $invitationData['data'][$i]['invitation_id'];
        
        if ($intervalTgl < $currentTgl) {
            setExpire($invitationURL, $invitationData['data'][$i]['invitation_id']);
            echo ' expired ';
        }else {
            echo ' not expire ';
        }
    }

    function getInvitationData($link){
        $curl = curl_init();
        //      get all invitation
        curl_setopt($curl, CURLOPT_URL, $link . '?filter[invitation_status]=0');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $responseInvID = curl_exec($curl);
        $result = json_decode($responseInvID, true);

        curl_close($curl);

        return $result;
    }

    function setExpire($link, $invitationID){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $link . '/' . $invitationID,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_POSTFIELDS =>'{
            "invitation_status": "2"
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