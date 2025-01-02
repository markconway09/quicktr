<?php
require_once 'functions.php';

$call = $_GET["call"];

switch($call){
    case 0:
        $q = "SELECT * FROM `info_orden` ORDER BY id DESC";
        break;
    case 1:
        $q = "SELECT `codigo_socio` FROM `info_orden` WHERE `codigo_socio` IS NOT NULL";
        break;
    case 2:
        $q = "SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, fecha as `date` FROM info_orden i
                    LEFT JOIN devolucion d ON (i.id = d.id_orden)";
        if(isset($_GET["search"])&&$_GET["search"] != ""){
            $params = $_GET["search"] ?? "";
            $search = " WHERE `nombre_dispositivo` LIKE :search OR i.id LIKE :search OR
                    `nombre` LIKE :search OR `local` LIKE :search OR `servicio` LIKE :search";
             $q .= $search;
        }
        $q .= " ORDER BY i.id DESC";
        break;
}

$pdo = connect();
$stmt = $pdo->prepare($q);
if(isset($_GET["search"])&&$_GET["search"] != ""){
    $params = "%" . $params . "%";
    $stmt->bindParam(':search', $params);
}
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