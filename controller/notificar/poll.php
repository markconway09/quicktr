<?php
session_start();
require_once '../functions.php';
header('Content-Type: application/json');

$pdo = connect();
$stmt = $pdo->prepare("SELECT `id`, `estado` FROM `info_orden`");
try {
    $stmt->execute();
} catch(PDOException $e){
    echo $e->getMessage();
}

$estados = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $estados[$row['id']] = $row['estado'];
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
$notification = [];
foreach ($estados as $id => $estado) {
    if (isset($_SESSION['lastContent'][$id]) && $_SESSION['lastContent'][$id] !== $estado) {
        $notification[] = "El ticket # $id ha cambiado.";
        // Return JSON response
        echo json_encode([
            'content' => $currentData['content'],
            'notification' => $notification,
        ]);
    }
}

// Update the session content
$_SESSION['lastContent'] = $currentData['content'];

?>
