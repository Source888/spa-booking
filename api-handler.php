<?php
require_once 'settings.php';
/** Merchant API Token */
function get_access_token() {
    $url = "https://api-staging.booker.com/v5/auth/connect/token";
    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    # Request headers
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Cache-Control: no-cache',
        'Ocp-Apim-Subscription-Key: '.SECONDARY_MERCHANT_API_KEY,
        
    );
    //var_dump($headers);
    $post_fields = "grant_type=client_credentials&client_id=".CLIENT_ID."&client_secret=".CLIENT_SECRET."&scope=merchant";
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($curl);
    curl_close($curl);

    if ($resp === false) {
        error_log("Curl error: " . curl_error($curl));
        return null;
    }

    $response = json_decode($resp, true);
    if (isset($response['error'])) {
        error_log("API error: " . $response['error_description']);
        return null;
    }
    var_dump($response);
    $access_token = $response['access_token'];
    $expires_in = $response['expires_in'];
    $expires_at = time() + $expires_in;

    save_token($access_token, $expires_at);

    return $access_token;
    
}
function save_token($token, $expires_at) {
    $data = array(
        'access_token' => $token,
        'expires_at' => $expires_at
    );
    file_put_contents('merch_token.json', json_encode($data));
}

function get_token() {
    $file = 'merch_token.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (time() < $data['expires_at']) {
            return $data['access_token'];
        }
    }
    return get_access_token();
}
function get_merchant_auth_header() {
    return 'Authorization: Bearer ' . get_token();
}

/** Customer API Token */

function get_customer_access_token(){
    $url = "https://api-staging.booker.com/v5/auth/connect/token";
    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    # Request headers
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Cache-Control: no-cache',
        'Ocp-Apim-Subscription-Key: '.SECONDARY_CUSTOMER_API_KEY,
        
    );
    var_dump($headers);
    $post_fields = "grant_type=client_credentials&client_id=".CLIENT_ID."&client_secret=".CLIENT_SECRET."&scope=customer";
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($curl);
    curl_close($curl);

    if ($resp === false) {
        error_log("Curl error: " . curl_error($curl));
        return null;
    }

    $response = json_decode($resp, true);
    if (isset($response['error'])) {
        error_log("API error: " . $response['error_description']);
        return null;
    }

    $access_token = $response['access_token'];
    $expires_in = $response['expires_in'];
    $expires_at = time() + $expires_in;

    save_customer_token($access_token, $expires_at);

    return $access_token;
    
}
function save_customer_token($token, $expires_at) {
    $data = array(
        'access_token' => $token,
        'expires_at' => $expires_at
    );
    file_put_contents('cust_token.json', json_encode($data));
}

function get_customer_token() {
    $file = 'cust_token.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (time() < $data['expires_at']) {
            return $data['access_token'];
        }
    }
    return get_customer_access_token();
}
function get_customer_auth_header() {
    return 'Authorization: Bearer ' . get_customer_token();
}

function find_customer($email = null) {
    $token = get_token();
    if (empty($email)) {
        $email = 'Alexia@hotmail.com';
        
    }
    $url = MERCHANT_API_URL . 'employees';
    $curl = curl_init($url);
    $post_fields = array(
        'FilterByExactLocationID' => true,
        'CustomerRecordType' => 1,
        'LocationID' => 3749,
        'Email' => $email,
        'access_token' => $token,
    );
    echo json_encode($post_fields);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_fields));
    $headers = array(
        'Content-Type: application/json',
        'Cache-Control: no-cache',
        //'Ocp-Apim-Subscription-Key: '.SECONDARY_MERCHANT_API_KEY,
        //get_merchant_auth_header(),
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($curl);
    curl_close($curl);
    var_dump($resp);
    if ($resp === false) {
        error_log("Curl error: " . curl_error($curl));
        return null;
    }

    $response = json_decode($resp, true);
    if (isset($response['error'])) {
        error_log("API error: " . $response['error_description']);
        return null;
    }

    return $response;
}