<?php
  header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
// Set variable
$stationcode = $_REQUEST["Station"];
$token = $_REQUEST["TokenCookie"];
$token2 = $_REQUEST["TokenForm"];
$data = $_REQUEST["TripData"];

// check
$stationcode = isset($_REQUEST["Station"]) ? $_REQUEST["Station"] : null;
$token = isset($_REQUEST["TokenCookie"]) ? $_REQUEST["TokenCookie"] : null;
$token2 = isset($_REQUEST["TokenForm"]) ? $_REQUEST["TokenForm"] : null;
$data = isset($_REQUEST["TripData"]) ? $_REQUEST["TripData"] : null;

if (empty($stationcode) || empty($token) || empty($token2) || empty($data)) {
http_response_code(400);
exit;
}

 $url = "https://online.ktmb.com.my/TimeTable/StationTrip";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
        "RequestVerificationToken: ".$token2,
        "Cookie: X-CSRF-TOKEN-COOKIENAME=".$token,
        "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $data = '{"TripData":"'.$data.'"}';

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    echo $resp;
?>