<?php

require_once '../config.php';

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname );

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if cuid already exists in the database
$query = "TRUNCATE TABLE kuitit";
$result = mysqli_query($conn, $query);

$query = "TRUNCATE TABLE subtrans";
$result = mysqli_query($conn, $query);

$query = "TRUNCATE TABLE tika";
$result = mysqli_query($conn, $query);

$query = "TRUNCATE TABLE trans";
$result = mysqli_query($conn, $query);

$query = "TRUNCATE TABLE vati";
$result = mysqli_query($conn, $query);


mysqli_close($conn);
