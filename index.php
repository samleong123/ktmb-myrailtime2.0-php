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
<title>KTM Station Schedule Checker</title>
</head>
<body>
<body>
<body>
<div class="px-4 py-5 my-5 text-center" onload="myFunction()"> <h1 class="display-5 fw-bold">KTM Station Schedule Checker</h1>  </br>
<h2 class="lead mb-4">Check any KTM Station Schedule in Malaysia.<br>This page requires JavaScript to work.<br>Powered by <a href="https://github.com/samleong123">Sam Sam</a></h2>
   <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
  
			
				  </div>
			<br> <p>Station ID last updated date : 9/6/2022 <br> Latest Station ID <a href="./api/stationlist.php" target="_blank" rel="noopener noreferrer">API</a> <br> 	 <a href="./journey">Plan your journey</a></p>
	   <br>  <p>Type something in the input field to search the KTM Station name :</p>  
  <input class="form-control" id="myInput" type="text" placeholder="Search">
  <br>
	 
		 <div id="content">
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
$json = './station.json';
$data = json_decode(file_get_contents($json),true);
$station = $data["data"];
   echo '<div class="table-responsive">';
    echo '<table class="table table-bordered center">
  <thead class="thead-light">
  <tr>
    <th>Station Name</th>
    <th>Station ID</th>
 <th>Online Schedule via MyRailTime 2.0 API [Recommended]</th>
 <th>MyRailTime 2.0 Website Online Schedule</th>
  </tr></thead>  <tbody id="myTable">';
foreach($station as $value){
    echo "<tr>";
 echo "<td>" . $value["name"] . " </td>";
  echo "<td>" . $value["code"] . " </td>";
   echo '<td><a href="./station/station.php?name=' . $value["name"] . '&id='.$value["code"].'" target="_blank" rel="noopener noreferrer">Click here</a> </td>';
      echo '<td><a href="http://myrailtime.ktmb.com.my/timetable/?origin=' . $value["code"] . '" target="_blank" rel="noopener noreferrer">Click here</a> </td>';
   echo "</tr>";
}
echo "</table>";
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