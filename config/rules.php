<?php

require_once 'vars.php';

/**
 * Those functions will determine the conditions to send an email
 */

function battery($value)
{
    return ($value + 0) < MINIMUM_BATTERY_VALUE;
}

function relativeHumdity($value)
{
    return ($value + 0) > MAXIMUM_RELATIVE_HUMIDITY;
}

function airTempreture($value)
{
    return ($value + 0) < MINIMUM_AIR_TEMPERATURE;
}


