<?php

require "functions.php";

$pdo = connect();
$stmt = $pdo->prepare("INSERT INTO `user` VALUES (null, :user, :pass, 0)");
$user = "usuario";
$pass = password_hash("usuario123", PASSWORD_DEFAULT);
$stmt->bindParam(':user', $user);
$stmt->bindParam(':pass', $pass);
try {
    $stmt->execute();
} catch(PDOException $e){
    echo $e->getMessage();
}