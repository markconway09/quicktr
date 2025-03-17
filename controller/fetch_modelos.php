<?php
require_once '../model/Database.php';

$db = new Database();
$pdo = $db->pdo;

if (isset($_GET['submodel_id'])) {
    $submodel_id = $_GET['submodel_id'];
    // Fetch parts based on the selected submodel
    $stmt = $pdo->prepare("SELECT * FROM d_parte WHERE id_modelo = :submodel_id");
    $stmt->bindParam(':submodel_id', $submodel_id, PDO::PARAM_INT);
    $stmt->execute();
    $parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($parts);
}

if (isset($_GET['model_name'])) {
    $model_id = $_GET['model_name'];
    // Fetch submodels based on the selected model
    $stmt = $pdo->prepare("SELECT * FROM d_modelo WHERE modelo = :model_name");
    $stmt->bindParam(':model_name', $model_id);
    $stmt->execute();
    $submodels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($submodels);
}

if (isset($_GET['brand_name'])) {
    $brand_name = $_GET['brand_name'];
    // Fetch models based on the selected brand
    $stmt = $pdo->prepare("SELECT distinct modelo FROM d_modelo WHERE nombre_marca = :brand_name");
    $stmt->bindParam(':brand_name', $brand_name);
    $stmt->execute();
    $models = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($models);
}