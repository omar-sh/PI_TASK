<?php

/**
 * 
 * EMAIL_RECIEVER: You can set it to your email when you want to test the script
 *
 * EMAIL_SENDER & EMAIL_SENDER_PASSWORD: I created a new email to use it for sending emails
 *
 * MINIMUM_BATTERY_VALUE & MAXIMUM_RELATIVE_HUMIDITY & MINIMUM_AIR_TEMPERATURE: I put those values according to readme.md you can change them as  you like
 *
 * SEND_EMAIL_EVERY_X_MINUTES : If the sensor sends payload every 15 minutes this variable will make sure that only one message will be sent every one hour (60 minutes)
 * 
 */

define('EMAIL_RECIEVER', 'omar.shrbajy1@gmail.com');
define('EMAIL_SENDER', 'pl.test.2020.email@gmail.com');
define('EMAIL_SENDER_PASSWORD', '2131636+');
define('MINIMUM_BATTERY_VALUE', 2300);
define('MAXIMUM_RELATIVE_HUMIDITY', 90);
define('MINIMUM_AIR_TEMPERATURE', 13);
define('SEND_EMAIL_EVERY_X_MINUTES', 60);
