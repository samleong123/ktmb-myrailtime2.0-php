<?php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);


$max_calls_limit  = 30;
$time_period      = 30;
$total_user_calls = 0;

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $user_ip_address = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $user_ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $user_ip_address = $_SERVER['REMOTE_ADDR'];
}

if (!$redis->exists($user_ip_address)) {
    $redis->set($user_ip_address, 1);
    $redis->expire($user_ip_address, $time_period);
    $total_user_calls = 1;
} else {
    $redis->INCR($user_ip_address);
    $total_user_calls = $redis->get($user_ip_address);
    if ($total_user_calls > $max_calls_limit) {
   
            $json = array("Status"=>"Fail","Message"=>"Rate limit exceeded!","IP Address"=>$user_ip_address,"Total_Calls"=>$total_user_calls,"Period"=>$time_period);
       header('Content-type: application/json');  header('Access-Control-Allow-Origin: *');
     header('HTTP/1.1 429 Too Many Requests');
   echo (json_encode($json));
        exit();
    }
}

    $url = "http://myrailtime.ktmb.com.my:8080/mobileApi/api/station/?isActive=true";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Host: myrailtime.ktmb.com.my:8080",
   "Origin: ionic://localhost",
   "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 15_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148",
   "Content-Type: application/json",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$data = <<<DATA
{
  "clientId": "userMobile",
  "secretKey": "s3cr3t"
}
DATA;

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);
header("Content-Type: application/json");
header("x-cache-status: Miss");
echo $resp;
?>