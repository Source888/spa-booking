<?php
require_once 'api-handler.php';
function findd_customer($data = null){

    $data = [
        'LocationID'=>3749,
        //'FirstOrLastOrFullNameStart'=>$data['FirstName'],
        //'FirstNameStart'=>$data['FirstName'],
        //'LastNameStart'=>$data['LastName'],
        'Email'=>'aliya.bhatti@gmail.com',//$data['CustomField_59567'],
        'access_token'=> get_token(),

    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api-staging.booker.com/v4.1/merchant/customers');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data) );
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Ocp-Apim-Subscription-Key: 9e524ec97c654583b85e561b60555819';
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($curl);
    $data = json_decode($response);
    

    
    var_dump($data);

    return $data->Customers[0]->CustomerID;

}
echo findd_customer();
