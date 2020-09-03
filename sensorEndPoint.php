<?php

namespace Metos;

require 'vendor/autoload.php';
require_once 'DataParser.php';

use Metos\Jobs\EmailJob;

class sensorEndPoint
{
    /**
     *
     * let's assume this function behavior like when the sensor sending data, it will get a random object from $payloads array and return it
     */
    public function loadRandomPayloadObject()
    {
        $payloads = file('./payloads', FILE_IGNORE_NEW_LINES);
        $random_payload = $payloads[rand(0, 120)];
        $binaryData = base64_decode($random_payload);
        $header = DataParser::parseHeader(substr($binaryData, 0, 14));
        $data = DataParser::parseData(substr($binaryData, 14));
        return ['header' => $header, 'data' => $data];
    }

    public function recievePayload()
    {
        try {
            // get a random payload
            $payload = $this->loadRandomPayloadObject();
            //connect to local redis
            \Resque::setBackend("localhost:6379");
            // queueing a job inside redis
            $processId = \Resque::enqueue('default', EmailJob::class, ['payload' => $payload]);
            // return the value directly (the client  will not be waiting until the email is sent)
            return ['payload' => $payload, 'processId' => $processId];

        } catch (Exception $e) {
            return $e;
        }
    }

}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
$sensorEndPoints = new sensorEndPoint();
echo json_encode($sensorEndPoints->recievePayload());
