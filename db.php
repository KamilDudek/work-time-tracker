<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "projekty";

$conn = null;

try {
  $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

// misc
function format_project_number($number) {
    return str_pad($number, 4, '0', STR_PAD_LEFT);
}