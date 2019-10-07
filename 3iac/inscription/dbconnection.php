<?php
include_once 'config.php';

function dbconnection()
{
    try {
        $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD);
    } catch (Exception $th) {
        die("Erreur : ".$th->getMessage());
    }
    return $db;
}

?>