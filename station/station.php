<?php 
$stationname = $_GET["name"];
$stationid = $_GET["id"];
?>

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
<title><?php 
if (empty($stationname)) {echo "";} else { echo $stationname . " - ";}
?>KTM Station Schedule Checker</title>
</head>
<body>
<body>
<body>
<div class="px-4 py-5 my-5 text-center" onload="myFunction()"> <h1 class="display-5 fw-bold"><?php 
if (empty($stationname)) {echo "";} else { echo $stationname . " - ";}
?>KTM Station Schedule Checker</h1>  </br>
<h2 class="lead mb-4">Check any KTM Station Schedule in Malaysia.<br>This page requires JavaScript to work.<br>Powered by <a href="https://github.com/samleong123">Sam Sam</a></h2>
   <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
  
			
				  </div>
			<br> <p>Station ID last updated date : 9/6/2022 <br> <?php 
if (empty($stationname)) {echo "";} else { echo 'Selected Station : ' . $stationname . '<br><a href="https://ktmb.samsam123.name.my">Reselect station</a></p>';}
?> </p>
	<?php
	 date_default_timezone_set('Asia/Kuala_Lumpur');
	     $date_MST = date("d F Y, h:i:s A");
    echo '<p>Last Updated Time (Malaysia Standard Time) : <br> '.$date_MST.'</p>';
	 ?>
	  <p>Type something in the input field to search the Arrival or Destination Station details :</p>  
  <input class="form-control" id="myInput" type="text" placeholder="Search">
  <br>
	 
		 <div id="content">

<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
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
$url = "http://myrailtime.ktmb.com.my:8080/authApi/api/timetable/public?origin=$stationid&count=10000";

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
$trainfreq = count($jsondata);
if ($trainfreq < 1) {
echo "No train available for this station. Please try again later.";} else {

      echo '<div class="table-responsive">';
    echo '<table class="table table-bordered center">
  <thead class="thead-light">
  <tr>
    <th>Origin Station Name</th>
    <th>Origin Train Number</th>
    <th>Origin Train Depart Time</th>
    <th>Destination Station</th>
    <th>Destination Train Arrival Time</th>
    <th>Via</th>

  </tr></thead>  <tbody id="myTable">';
foreach($jsondata as $value){
    echo "<tr>";
 echo "<td>" . $value["originStationName"] . " </td>";
  echo "<td>" . $value["originTrainNo"] . " </td>";
 echo "<td>" . $value["originTimeToDepart"] . " </td>";
  echo "<td>" . $value["destinationStationName"] . " </td>";
    echo "<td>" . $value["destinationTimeToArrival"] . " </td>";
     echo "<td>" . $value["destinationStationService"] . " </td>";
   echo "</tr>";
}
echo "</table>";
    
}
?>
<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
console.log('Thanks for using this services.')
</script>
		 </div>
		 	 </div>
	
    </div>
	</body>
	</html>