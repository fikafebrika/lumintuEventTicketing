<?php
    $host = "localhost";
    $user = "root";
    $pass = "";
    $name = "lumintu-ticket";

    $conn = mysqli_connect($host, $user, $pass, $name);

    if (mysqli_connect_errno()){
        echo "Failed to connect to DB. " . mysqli_connect_error();
    }
?>