# KTM Station Schedule Checker & Journey Planner in Web Form (PHP)
KTMB (Keretapi Tanah Melayu Berhad) Station Schedule Checker & Journey Planner in Web Form , written with PHP.

## Why I need to use this instead of MyRailTime2.0 App?
I **personally** feel that MyRailTime2.0 App **isn't providing a good experience for me** , and their web page form of MyRailTime2.0 [**took lots of time to load their webpacked vendor JS file**](https://user-images.githubusercontent.com/58818070/173094728-4139ff9b-e832-4fda-9a54-4182f09b24bd.png),  makes the **loading time to 5 seconds and above** , but the **API only uses 100ms+ to get the responses**.

## Requirements 
1. PHP 7.4 and above
2. Redis and Redis PHP Extension installed (For Rate Limit purposes)
3. A VPS / Computer / Server that have a stable Internet Connection and allowed to access MyRailTime 2.0's API Endpoint (myrailtime.ktmb.com.my:8080)
4. Nginx / Apache / Any web server with PHP configured install and running

## Deployment
1. Download the source code and extract to the designated folder on ur web server root folder.
2. Make sure Redis and Redis PHP Extension installed.
3. Make sure PHP is preconfigured with your web server.
4. Expose it to public and you are up and running!.


## Data Sources
- MyRailTime 2.0 APP on version 1.8 (API Endpoint Grabbed via Proxyman on iOS for APP)
- MyRailTime 2.0 Website (For checking station schedule only)

## API Endpoints 
1. To retrieve full KTM Station List with Name and Code :

Send **POST** request to ```http://myrailtime.ktmb.com.my:8080/mobileApi/api/station/?isActive=true```

with header : 
```
Host: myrailtime.ktmb.com.my:8080
User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 15_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148
```
with content in JSON form :
```
{
  "clientId": "userMobile",
  "secretKey": "s3cr3t"
}
```

Response : 

In JSON form 

Make sure ```statusCode``` is ```200``` and there's data inside ```data``` under the JSON response.

2. To retrieve specified station timetable / schedule 

Send **POST** request to ```http://myrailtime.ktmb.com.my:8080/authApi/api/timetable/public?origin=$origincode&count=10000```

where $origincode is the Station Code which can retrieved under the 1st API Endpoint

For example Kepong Sentral Code = 18400 and I wanted to retrieve Kepong Sentral's timetable / schedule :

The API Endpoint URL should look like this : 
```http://myrailtime.ktmb.com.my:8080/authApi/api/timetable/public?origin=18400&count=10000```

with header : 
```
Host: myrailtime.ktmb.com.my:8080
User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 15_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148
```
with content in JSON form :
```
{
  "clientId": "userMobile",
  "secretKey": "s3cr3t"
}
```
Response : 

In JSON form 

Make sure ```statusCode``` is ```200``` 

If there's **NO** data inside ```data``` under the JSON response , which means there are currently no train for this station.

3. To retrieve specified selected Origin - Destination timetable
Send **POST** request to ```http://myrailtime.ktmb.com.my:8080/mobileApi/api/processor/journey?origin=$origincode&destination=$destinationcode&datetime=$time&limited=false```

where 

$origincode is the Origin Station Code which can retrieved under the 1st API Endpoint

$destinationcode is the Destination Station Code which can retrieved under the 1st API Endpoint

$time is 24 Hour format involving hour and minute only (Example : ```0916``` indicates 9:16AM)

For example : My Origin Station is Bandar Tasik Selatan (19600) and my Destination Station is KL Sentral (19100), and the time I'm proceed to check it is 11:15 AM (1115)

Your API Endpoint URL should be :
```http://myrailtime.ktmb.com.my:8080/mobileApi/api/processor/journey?origin=19600&destination=19100&datetime=1115&limited=false```

with header : 
```
Host: myrailtime.ktmb.com.my:8080
User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 15_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148
```
with content in JSON form :
```
{
  "clientId": "userMobile",
  "secretKey": "s3cr3t"
}
```
Response : 

In JSON form 

Make sure ```statusCode``` is ```200``` 

If there's **NO** data inside ```data``` under the JSON response , which means there are currently no train serve from your Origin Station to Destination Station.


## Issues
So far I have not yet encountered any Issue about this project , kindly create a Issue if you found anything that might affect the stability of this project. 

I'm not very familar with PHP but I'll try my best to solve it. 

Any Pull-Requests that make senses and able to improve this project is welcome!

## Credits
1. MyRailTime2.0
2. KTMB

## Screenshots 
![image](https://user-images.githubusercontent.com/58818070/173098800-28a9564f-58d4-4118-9f3c-9a5abf442578.png)
![image](https://user-images.githubusercontent.com/58818070/173098909-8cc72f6e-f8ff-4d79-922f-77722cb71162.png)

![image](https://user-images.githubusercontent.com/58818070/173099187-ae503a19-cf30-45c9-b898-25473105997b.png)
![image](https://user-images.githubusercontent.com/58818070/173099077-1fbaddf2-c949-4c29-ba10-8fbc6744f860.png)





