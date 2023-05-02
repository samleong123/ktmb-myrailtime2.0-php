<?php

$station_code = $_REQUEST["station"];
if (empty($station_code)){
    http_response_code(503);
    exit;
}
$url = 'http://myrailtime.ktmb.com.my/timetable/?origin='.$station_code;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

if(curl_errno($ch)) {
  http_response_code(503);
} else {
    $redirectUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

}

curl_close($ch);

// Request new URL
$url = $redirectUrl;

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, 1);


$resp = curl_exec($curl);
$http_response = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$headersize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

curl_close($curl);
$header = substr($resp, 0, $headersize);
$response = substr($resp,$headersize);
if (preg_match('/X-CSRF-TOKEN-COOKIENAME=([^;]+)/', $header, $matches)) {
    $token = $matches[1];
    // Create a new DOMDocument object and load the HTML content
$doc = new DOMDocument();
$doc->loadHTML($response);

// Find the input element with name '__RequestVerificationToken'
$input = $doc->getElementsByTagName('input')->item(0);
if ($input->getAttribute('name') == '__RequestVerificationToken') {
    
    $token2 = $input->getAttribute('value');
    
$regex = '/var allTripDatas = \["(.*?)"\];/';
if (preg_match($regex, $response, $matches)) {
    $all = $matches[0];
$re = '/\[".*"\]/';
$str = $all;
if (preg_match($re, $str, $matches)){
      $tripdata = $matches[0];
    $tripdatadecode = json_decode($tripdata,true);
// loop trip 
$responses = array();

foreach ($tripdatadecode as $value){
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

    $data = '{"TripData":"'.$value.'"}';

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);

    $responses[] = $resp;
}

$merged_response = array_merge($responses);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
echo json_encode($merged_response);

}
}


}
    
    
    
    
    
    
    
    
}

?>