<?php

function main($argv)
{
    //check inputs
    if(!isset($argv[1], $argv[2], $argv[3])){
        throw new Exception("missing params");
    }
    list(,$path, $from, $to) = $argv;

    if(!file_exists($path)){
        throw new Exception("file not found($path)");
    }
    $fromTs = strtotime($from);
    $toTs = strtotime($to);

    $handler = fopen($path, "r");
    if(empty($handler)){
        throw new Exception("fopen fail($path)");
    }
    $ipRegex = "\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}";
    $dateTimeRegex = "\d{2}\/[a-zA-Z]{3}\/\d{4}:\d{2}:\d{2}:\d{2} [\+\-]\d{4}";
    $regex = "/^($ipRegex) - - \[($dateTimeRegex)\]/";
    $requestCounter = 0;
    $hostDict = [];
    $countryDict = [];
    while(($line = fgets($handler)) !== false) {
        if(!preg_match($regex, $line, $matches)){
            continue;
        }
        list(,$ip, $dateTime) = $matches;
        //Q1
        $requestCounter++;

        //Q2
        $ts = strtotime($dateTime);
        if($fromTs <= $ts && $ts < $toTs){
            if(!isset($hostDict[$ip])){
                $hostDict[$ip] = 0;
            }
            $hostDict[$ip]++;
        }

        //Q3
        $country = @geoip_country_code_by_name($ip);
        //only count known country if necessary
        if(empty($country)){
            $country = "UNKNOWN";
        }
        if(!isset($countryDict[$country])){
            $countryDict[$country] = 0;
        }
        $countryDict[$country]++;
    }
    fclose($handler);

    $results = [];

    $results["Q1"] = $requestCounter;

    arsort($hostDict);
    $results["Q2"] = array_slice($hostDict, 0 , 10);

    arsort($countryDict);
    $results["Q3"] = array_slice($countryDict, 0 , 2);

    print_r($results);
    return;
}
//$argv[2] = "2017-06-10";
//$argv[3] = "2017-06-20";
main($argv);

