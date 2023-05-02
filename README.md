# KTM Station Schedule Checker & Journey Planner in Web Form (PHP) v2
KTMB (Keretapi Tanah Melayu Berhad) Station Schedule Checker & Journey Planner in Web Form , written with PHP.

## Why is there a V2?
KTM recently has merged MyRailTime into KTMB Integrated Ticketing System (KITS) , all the old backend API has changed. A V2 with better web UI and useable API from KTMB Integrated Ticketing System (KITS) has been build.

## Requirements 
1. PHP 7.4 and above
2. Redis and Redis PHP Extension installed (For Rate Limit purposes)
3. A VPS / Computer / Server that have a stable Internet Connection and allowed to access KTMB Integrated Ticketing System (KITS)'s API Endpoint (online.ktmb.com.my:443)
4. Nginx / Apache / Any web server with PHP configured install and running

## Deployment
1. Download the source code and extract to the designated folder on ur web server root folder.
2. Make sure Redis and Redis PHP Extension installed.
3. Make sure PHP is preconfigured with your web server.
4. Expose it to public and you are up and running!.


## Data Sources
- KTMB Integrated Ticketing System (KITS)

## Token
KTMB Integrated Ticketing System (KITS) uses two types of CSRF Token to verify request :
1. Via Cookie 
2. Via Hidden Input Value in Form
Both of these two token must be present in Cookie & Submit Form way in order to get successful request

## Proxied API Documentation
This project provide proxied MyRailTime - KTMB Integrated Ticketing System (KITS)'s API via PHP and return in JSON form.

1. ```stationScheduleData.php```
- Retrieve specified KTM Station's schedule and timetable in JSON form
- Query String required :

```station``` - Specified KTM Station's Code

2. ```stationData.php```
- Retrieve specified KTM Station's encrypted trips data & token in JSON form 
- Query String required :

```station``` - Specified KTM Station's Code

3. ```tripData.php```
- Retrieve specified KTM Station's trips data via the token & data retrieved from ```stationData.php``` in JSON form 
- Query String required :

- ```Station``` - Specified KTM Station's Code Returned from ```stationData.php```
- ```TokenCookie``` - Specified Token via Cookie retrieved from ```stationData.php```
- ```TokenForm``` - Specified Token via Hidden Input Value retrieved from ```stationData.php```
- ```TripData``` - Specified Trips Data retrieved from ```stationData.php```


## Issues
Slow response , still no solution to solve it. Any PR that could enhance the user experience of this project are welcomed. 
Please create an issue if you experience any problem.

## Credits
1. KTMB

## Screenshots 
![image](https://user-images.githubusercontent.com/58818070/235711584-16264ad7-d899-4dad-addb-5dfc0346c5e3.png)
![image](https://user-images.githubusercontent.com/58818070/235711616-55e97983-b147-4b9f-95c9-b0aab95b9f19.png)





