<?php
require 'class/class.ptscrap.php';
header('Content-Type: application/json');

// If we receive any GET data
if( $_GET ){
    $action = $_GET['action'];
    $url = $_GET['url'];

    // If URL isn't empty
    if( $url != "" ) {
        $count = explode('/', $url);

        if( count($count) == 2 || count($count) == 4 ) {

            // CREATE
            if( $action == "create" ){
                $ptscrap = new ptscrap($url);
                $pages = $ptscrap->getPages();
                $pins = $ptscrap->getPins();
                $scrape = $ptscrap->scrape();

                if( $scrape ) {
                    $create = $ptscrap->create();
                    $deliver = $ptscrap->deliver();
                    $file = $ptscrap->getFilename();
                    echo json_encode(array('pins' => $pins, 'pages' => $pages, 'delivered' => 'true', 'file' => $file));
                } else {
                    echo json_encode(array('error' => "Could not connect to board")); 
                }

            // CHECK
            } elseif( $action == "check" ) {
                $files = 0;
                if ( glob("tmp/*.jpg") != false ) {
                    $files = count(glob("tmp/*.jpg"));
                }

                echo json_encode(array('files' => $files));

            // TOTALPINS
            } elseif( $action == "getTotalPins" ) {

                $ptscrap = new ptscrap($url);

                if( $ptscrap ) {
                    $pages = $ptscrap->getPages();
                    $pins = $ptscrap->getPins();    
                    echo json_encode(array('pins' => $pins, 'pages' => $pages));                
                } else {
                    echo json_encode(array('error' => "Could not connect to board"));
                }

            } else {
                echo json_encode(array('error' => 'Invalid action'));   
            }

        } else {
            echo json_encode(array('error' => 'Invalid URL.'));   
        }

    }

} else {
    echo json_encode(array('error' => 'No GET')); 
}


?>