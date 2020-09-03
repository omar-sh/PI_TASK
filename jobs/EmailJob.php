<?php
namespace Metos\Jobs;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/vars.php';
require_once __DIR__ . '/../config/rules.php';

/**
 * 
 * This job will be executed by sensorEndpoint.php when the user issue an request,
 * this class has a function called `perform` which will be called by the library I am using (php-resque) 
 * 
 */

class EmailJob
{
    /**
     * This function will be called upon initilization (Before calling perform)
     */
    public function setup()
    {
        $this->redisClient = new \Predis\Client();
    }
    
    public function shouldSendEmail()
    {
        // I am setting the time when a new email is sent inside redis (memory database)
        // inside this function I am getting the value from redis and subtract it from the current time and compare it with the constant `SEND_EMAIL_EVERY_X_MINUTES`
        $lastTime = $this->redisClient->get('LAST_TIME_SENT');
        $currentTime = date("Y/m/d H:i:s");
        $minutes = (strtotime($currentTime) - strtotime($lastTime)) / 60;
        return !$lastTime ? true : ceil($minutes) >= SEND_EMAIL_EVERY_X_MINUTES;
    }

    public function perform()
    {
        //get the data from the payload object
        $battery = $this->args['payload']['data']['battery'];
        $relativeHumdity = $this->args['payload']['data']['rh_avg'];
        $airTemp = $this->args['payload']['data']['air_avg'];
        if ($this->shouldSendEmail() && (battery($battery) || relativeHumdity($relativeHumdity) || airTempreture($airTemp))) {
            echo "Sending Email...\n";
            // I am sending the whole object inside the email :) 
            $this->sendEmail("PI ALERT NOTIFICATION", json_encode($this->args['payload']));
            // Set the new time after the  message has been sent
            $this->redisClient->set('LAST_TIME_SENT', date("Y/m/d H:i:s"));
        }

    }

    public function sendEmail($subject, $content)
    {
        // I am using swiftMailer package to send emails 
        $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername(EMAIL_SENDER)
            ->setPassword(EMAIL_SENDER_PASSWORD);
        $mailer = new \Swift_Mailer($transport);
        $message = (new \Swift_Message($subject))
            ->setFrom([EMAIL_SENDER => 'PI ALERT SYSTEM'])
            ->setTo([EMAIL_RECIEVER])
            ->setBody($content);

        $result = $mailer->send($message);
    }

}
