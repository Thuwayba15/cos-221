
<?php
// Database configuration
$dbHost = 'wheatley.cs.up.ac.za';
$dbUsername = 'u21554995'; //db username
$dbPassword = 'VZN7VIXPCMNK5G5RDR2EY7TAF6K26KH2'; //dp password from wheatley files.php
$dbName = 'u21554995_hoop'; //db name

//establish database connection
$dbConnection = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

//check connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
} //else {
    //echo "Connected to database successfully :)\n" + $count++;
//}

?>