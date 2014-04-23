<?php 

function getBrowserOS() { 

    $user_agent     =   $_SERVER['HTTP_USER_AGENT'];         
    $os_platform    =   "Unknown OS Platform";

    // Get the Operating System Platform

    if (preg_match('/windows|win32/i', $user_agent)) {

        $os_platform    =   'Windows';

    } else if (preg_match('/macintosh|mac os x/i', $user_agent)) {

        $os_platform    =   'Mac';


    } else if (preg_match('/linux/i', $user_agent)) {

        $os_platform    =   "Linux";

    }
    return $os_platform;
} 

$os = getBrowserOS();    

?>