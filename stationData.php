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
    $tripsnumber = count($tripdatadecode);
    $finaldata = array("Status"=>"Success","TokenForm"=>$token2,"TokenCookie"=>$token,"TripsNumber"=>$tripsnumber,"TripsData"=>urlencode($tripdata),"Station"=>$station_code);
  header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
    echo json_encode($finaldata);
}
    
}
    
}
    
}