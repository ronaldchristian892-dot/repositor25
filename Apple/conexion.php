<?php
    $host="localhost";
    $user="root";
    $pass="";
    $db="productos";
    $conn=new mysqli($host,$user,$pass,$db);

    if ($conn->connect_error) {
        echo "error en la conexion" . $conn->connect_error;
    }
?>