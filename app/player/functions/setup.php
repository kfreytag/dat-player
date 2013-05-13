<?php

function setup(Context $context)
{
    // Use timezone US/Pacific for error log timestamps.
    date_default_timezone_set('US/Pacific');
    // Tell proxies not to cache our pages.
    header('Cache-Control: private');
    // Add p3p header.
    header('P3P: CP="DSP IDC CUR ADM PSA PSDi OTPi DELi STP NAV COM UNI INT PHY DEM"');
}

function done()
{
//	('time_total', round($elapsed, 3));
//    timing_and_info_log();
    exit;
}


?>