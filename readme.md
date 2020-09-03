# PI_TASK

### How I solved the task
I used a package called [php-resque](https://github.com/resque/php-resque) which allows you to run workers, those workers can run jobs seperated from the main php thread, so whenever the client issue a request a new  job will be queued to send  an email.

#### How to prevent system from sending too much emails:
you can find a constant called `SEND_EMAIL_EVERY_X_MINUTES` which is set to `60` minutes, it means that every 60 minutes only one message will be sent to the client(user).

The logic behind this, is that I have set a variable inside redis (memory database) called `LAST_TIME_SENT` at the first time it will be `null` then when the sensor send its first email, `LAST_TIME_SENT` will be changed to a string date, the second time the sensor will send a request I will subtract the current date from `LAST_TIME_SENT` and if the result is greater than or equal to  `SEND_EMAIL_EVERY_X_MINUTES` an email will be sent, otherwise nothing will be sent.



### Other packages I have used

[swiftmailer/swiftmailer](https://github.com/swiftmailer/swiftmailer): I used it for sending emails to the user

[predis/predis](https://github.com/predis/predis): I used it to set and get values from  redis, I actually needed it just to set the last time that an email has been sent.

- please note that the minimum php version should 5.4

## How run the project

1- first of a ll you need to copy the folder to a web server like  nginx, wamp, xampp or mamp.

2- navigate to the project folder and do `composer install` to  install the packages

3- open a terminal, navigate to the project folder, and call
`VERBOSE=1 QUEUE=* php ./vendor/resque/php-resque/bin/resque`  this should be stay working to send emails. Please note that this command may change if you are working in windows environment for settings those values `VERBOSE=1 QUEUE=*`.

4- you can test  the  project, by issueing a  request to
`localhost:{port}/{project_name}/sensorEndPoint.php`

5- Please do not forget to change the value `EMAIL_RECIEVER` inside `config/var.php` to  your email so you can recieve the emails.
this will result in sending an email to the user.

## Explaining the logic I have done

Inside `sensorEndPoint.php` class I have added two functions:

1- `loadRandomPayloadObject` : this will load a  random object from the payload array to send it to the job

2-  `recievePayload`: this function will be called by the client (sensor) and will call
`loadRandomPayloadObject` to get a random payload object, and pass it the job, and at the end this function will return (you can try it on postman)
```
{
    "payload": {
        "header": {
            "CRC": 45504,
            "messageID": 126,
            "messageVersion": 128,
            "deviceType": 1,
            "HW": 112,
            "FW": 111,
            "status": 0,
            "serial": "0340039D"
        },
        "data": {
            "battery": 2387,
            "solar": 0,
            "rain": 194.4,
            "air_avg": 12.13,
            "air_mn": 12,
            "air_mx": 12.3,
            "rh_avg": 100,
            "rh_mn": 100,
            "rh_mx": 100,
            "dt_avg": 655.12,
            "dt_mn": 655.11,
            "dt_mx": 655.12,
            "dew_avg": 12.13,
            "dew_mn": 11.99,
            "vpd_avg": 0,
            "vpd_mn": 0,
            "leaf": 15
        }
    },
    "processId": "689be700e95277add4f337231aac76d9"
}
```
## Project Hierarchy:
    ├── config
    │   ├── vars.php #contains constants which I used accross the whole project, you can change their values according to your needs.
    │   ├── rules.php  # this file has multiple functions that determine the logic for sending the email.
    │
    └── jobs
    │   ├── EmailJob.php #contains the logic that sends the email, you can have a look at the main function `perform` which is being called by php-resque
     |
    └── sensorEndPoint.php #the main file which is being called by the client(sensor)


