<?php
require 'class/class.ptscrap.php';
header('Content-Type: application/json');

if($_GET){
    $action = $_GET['action'];
    $url = $_GET['url'];
    if($url != "") {
        $count = explode('/', $url);
        if(count($count) == 2 || count($count) == 4) {
            if($action == "create"){
                $ptscrap = new ptscrap($url);
                $pages = $ptscrap->getPages();
                $pins = $ptscrap->getPins();
                $scrape = $ptscrap->scrape();
                if($scrape) {
                    $create = $ptscrap->create();
                    $deliver = $ptscrap->deliver();
                    $file = $ptscrap->getFilename();
                    echo json_encode(array('pins' => $pins, 'pages' => $pages, 'delivered' => 'true', 'file' => $file));
                }
                else {
                    echo json_encode(array('error' => "Could not connect to board")); 
                }
            }
            elseif($action == "check") {
                if (glob("tmp/*.jpg") != false) {
                    $files = count(glob("tmp/*.jpg"));
                }
                echo json_encode(array('files' => $files));
            }
            elseif($action == "getTotalPins") {

                $ptscrap = new ptscrap($url);
                //var_dump($ptscrap);

                if($ptscrap) {
                    $pages = $ptscrap->getPages();
                    $pins = $ptscrap->getPins();    
                    echo json_encode(array('pins' => $pins, 'pages' => $pages));                
                }
                else {
                    echo json_encode(array('error' => "Could not connect to board"));
                }
            }
            else {
                echo json_encode(array('error' => 'Invalid action'));   
            }
        }
        else {
            echo json_encode(array('error' => 'Invalid URL.'));   
        }
    }
}
else {
    echo json_encode(array('error' => 'No GET')); 
}


?>