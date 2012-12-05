<?php

    require_once('../../Holidays.class.php');
    
    $extras = array(
        'Fri. before Memorial Day' => array(-5, 1, 5, 5),
        'Fri. after Thanksgiving'  => array(+5, 4, 11, 4)
    );
    
    //Get the year from the request array
    $year  = $_REQUEST['year'];

    //Instantiate the object
    $holidays = new Holidays($year, $extras);

    //set the content type
    header('Content-type: application/json');
    
    //output the json
    echo $holidays->getJson();
    
    exit();
