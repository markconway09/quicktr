<?php
// exportModelos.php

require_once "../model/Database.php";

// Database connection
$db = new Database();
$pdo = $db->pdo;

// Set headers to force download of the CSV file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=modelos_export_'.date("Y-m-d").'.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Write the header row to the CSV file
fputcsv($output, ['Marca', 'Tipo', 'Modelo', 'Submodelo', 'Parte', 'Original', 'Compatible', 'Incel']);

// Fetch data from the database
$stmt = $pdo->query("
    SELECT 
        dm.nombre_marca AS Marca,
        dm.nombre_tipo AS Tipo,
        dm.modelo AS Modelo,
        dm.submodelo AS Submodelo,
        dp.nombre AS Parte,
        dp.original AS Original,
        dp.compatible AS Compatible,
        dp.incel AS Incel
    FROM d_modelo dm
    LEFT JOIN d_parte dp ON dm.id = dp.id_modelo
");

// Write each row of data to the CSV file
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

// Close the output stream
fclose($output);
exit;
?>
