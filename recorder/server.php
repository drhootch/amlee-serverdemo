<?php
 
mb_internal_encoding("UTF-8");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

require '../config.php';


 
// DB table to use
$table = 'records';
 
// Table's primary key
$primaryKey = 'id';
 

$columns = array(
    array( 'db' => 'id', 'dt' => 0 ),
    array( 'db' => 'text', 'dt' => 1 ),
    array( 'db' => 'audio', 'dt' => 2, 'formatter' => function( $d, $row ) { return '<audio controls><source src="//amly.app/audio/records/'.$d.'" type="audio/wav">Your browser does not support the audio element.</audio>'; }),
);


// SQL server connection information
$sql_details = array(
    'user' => $db_config['username'],
    'pass' => $db_config['password'],
    'db'   => $db_config['dbname'],
    'host' => $db_config['host']
);
 

 
require( 'ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);
