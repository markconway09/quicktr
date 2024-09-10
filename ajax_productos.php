<?php
require_once 'functions.php';
$pdo = connect();
if(isset($_GET["id"])){
    $stmt = $pdo->prepare("SELECT precio FROM `producto` WHERE `id` = :id AND `stock` > 0");
    $stmt->bindParam(":id", $_GET["id"]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM `producto` WHERE `stock` > 0");
}
$stmt->execute();

$var="[";
while($q = $stmt->fetch(PDO::FETCH_ASSOC)){
    $var .= json_encode($q, JSON_UNESCAPED_UNICODE).",";
}
$var .="{}]";

echo $var;