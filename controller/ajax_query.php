<?php
require_once 'functions.php';

$call = $_GET["call"];

if($call == 0) {
    $q = "SELECT * FROM `info_orden` ORDER BY id DESC";
} else if($call == 1) {
    $q = "SELECT `codigo_socio` FROM `info_orden` WHERE `codigo_socio` IS NOT NULL";
}

$pdo = connect();
$stmt = $pdo->prepare($q);
$stmt->execute();

$var = "[";  // Initialize the array
$first = true;  // Flag to track if it's the first element

while ($q = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Only add a comma if it's not the first item
    if (!$first) {
        $var .= ",";  // Add a comma before the next element
    }
    // Append the current item
    $var .= json_encode($q, JSON_UNESCAPED_UNICODE);
    $first = false;  // After the first iteration, set $first to false
}

$var .= "]";  // Close the JSON array


echo $var;