<?php
session_start();
require_once '../functions.php';
header('Content-Type: application/json');

$pdo = connect();
$stmt = $pdo->prepare("SELECT `estado` FROM `info_orden`");
try {
    $stmt->execute();
} catch(PDOException $e){
    echo $e->getMessage();
}

$estados = "";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $estados.=$row["estado"];
}

$currentData = [
    'lastUpdate' => time(),
    'content' => $estados,
];

// Check if the session variable for last content exists
if (!isset($_SESSION['lastContent'])) {
    $_SESSION['lastContent'] = $currentData['content'];
}

// Check for specific change
$notification = '';
if ($_SESSION['lastContent'] !== $currentData['content']) {
    $notification = 'Un ticket ha cambiado de estado.';
    $_SESSION['lastContent'] = $currentData['content'];
}

// Return JSON response
echo json_encode([
    'content' => $currentData['content'],
    'notification' => $notification,
]);
?>
