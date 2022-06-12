<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://www.recaptcha.net/recaptcha/api.js" async defer></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"  crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js"  crossorigin="anonymous"></script>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" type="image/x-icon" href="https://www.samsam123.name.my/images/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="https://www.samsam123.name.my/images/favicon.ico">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.samsam123.name.my/images/favicon.ico">
<title>KTM Station Journey Planner</title>
</head>
<body>
<body>
<body>
<div class="px-4 py-5 my-5 text-center" onload="myFunction()"> <h1 class="display-5 fw-bold">KTM Station Journey Planner</h1>  </br>
<h2 class="lead mb-4">Plan your journey with this service by choosing your designated origin & destination station.<br>This page requires JavaScript to work.<br>Powered by <a href="https://github.com/samleong123">Sam Sam</a></h2>
   <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
  
			
				  </div>
			<br> <p>Station ID last updated date : 9/6/2022 <br> Latest Station ID <a href="https://ktmb.samsam123.name.my/2api/stationlist.php" target="_blank" rel="noopener noreferrer">API</a></p>
	   <br>  
	  <p>Type something in the input field to search the Arrival or Destination Station details :</p>  
  <input class="form-control" id="myInput" type="text" placeholder="Search">
  <br>
		 <div id="content">
<?php
error_reporting(0);
$originwithid = $_GET["origin"];
$destinationwithid = $_GET["destination"];

if (empty($originwithid)) {
  
} else {
    if (empty($destinationwithid)) {
        
}


}
$originid = explode("-",$originwithid);
$destinationid = explode("-",$destinationwithid);

$origin = $originid["1"];
$destination = $destinationid["1"];
$originname = $originid["0"];
$destinationname = $destinationid["0"];

?>
<p><?php
if (empty($originwithid)) {
    echo "Please reselect the Origin Station!";
    exit;
} else {
    if (empty($destinationwithid)) {
        echo "Please reselect the Destination Station!";
        exit;
}
	 date_default_timezone_set('Asia/Kuala_Lumpur');
	     $date_MST = date("d F Y, h:i:s A");
	     $time = date("H:i");
	     $timeexplode = explode(":",$time);
	     $time = $timeexplode["0"].$timeexplode["1"];
    echo '<p>Last Updated Time (Malaysia Standard Time) : <br> '.$date_MST.'</p>';
echo "<p> From <strong>".$originname."</strong> to <strong>".$destinationname."</strong>";
echo '<br><p><a href="https://ktmb.samsam123.name.my/journey">Replan your journey</a></p>';
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
$url = "http://myrailtime.ktmb.com.my:8080/mobileApi/api/processor/journey?origin=$origin&destination=$destination&datetime=$time&limited=false";
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Origin: http://myrailtime.ktmb.com.my",
   "Pragma: no-cache",
   "Referer: http://myrailtime.ktmb.com.my/",
   "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.5005.63 Safari/537.36 Edg/102.0.1245.33",
   "Authorization: Bearer",
   "Content-Type: application/json",
   'X-Forwarded-For: '.$_SERVER["HTTP_X_FORWARDED_FOR"],
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$data = '{"clientId":"userMobile","secretKey":"s3cr3t"}';

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);

$stationschedule = json_decode($resp,true);
$jsondata = $stationschedule["data"];
$status = $stationschedule["statusCode"];
$jsondata2 = $stationschedule["data"];
$trainfreq = count($jsondata2);

if ($status != "200") {
    echo "Oops , seems like there are something wrong about it.";} 
    else {
    if ($trainfreq < 1) {
echo "No train available for this journey. Please try again later."; exit;} else {

      echo '<div class="table-responsive">';
    echo '<table class="table table-bordered center">
  <thead class="thead-light">
  <tr>
    <th>Origin Station Name</th>
      <th>Via</th>
    <th>Origin Train Number</th>
    <th>Origin Train Depart Time</th>
    <th>Via connections</th>
    <th>Destination Station</th>
    <th>Destination Train Arrival Time</th>
  

  </tr></thead>  <tbody id="myTable">';
 
foreach($jsondata as $value){
    $connections = $value["connections"];
    echo "<tr>";
 echo "<td>" . $value["originStationName"] . " </td>";
      echo "<td>" . $value["originStationService"] . " </td>";
  echo "<td>" . $value["originTrainNo"] . " </td>";
 echo "<td>" . $value["originTimeToDepart"] . " </td>";
 if (empty(count($connections))) { 
       echo "<td> N/A </td>";
 } else {
     echo "<td>";
     foreach($connections as $value2) {
          echo "Via : " . $value2["stationName"] . "<br> Train Number : ".$value2["trainNo"] ." <br> Train services : ".$value2["stationService"]."<br>Time to depart : ".$value2["timeToDepart"];
     } echo "</td>";
     
 }

  echo "<td>" . $value["destinationStationName"] . " </td>";
    echo "<td>" . $value["destinationTimeToArrival"] . " </td>";

   echo "</tr>";
}
echo "</table>";
    
}
echo '
<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

</script>
		 </div>
		 	 </div>
	
    </div>
	</body>
	</html>';
}}
?></p>
