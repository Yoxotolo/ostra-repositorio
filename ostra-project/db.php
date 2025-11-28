<?php
$servername = "localhost";
$username = "root";   // seu usuário do MySQL
$password = "";       // senha (deixe vazio se estiver no XAMPP padrão)
$dbname = "ostra";    // nome do banco que vamos criar

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Em um contexto AJAX, não use die(). O script handler deve tratar a falha.
}
