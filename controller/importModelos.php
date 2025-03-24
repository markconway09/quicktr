<?php
// importModelos.php

require_once "../model/Database.php";

// Check if a file was uploaded
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['csv_file']['tmp_name'];

    // Open the uploaded CSV file
    if (($handle = fopen($fileTmpPath, 'r')) !== false) {
        // Database connection
        $db = new Database();
        $pdo = $db->pdo;

        // Skip the header row
        fgetcsv($handle);

        // Prepare the SQL statement for inserting or updating data in d_modelo
        $stmt = $pdo->prepare("
            INSERT INTO d_modelo (nombre_marca, nombre_tipo, modelo, submodelo)
            VALUES (:Marca, :Tipo, :Modelo, :Submodelo)
            ON DUPLICATE KEY UPDATE
                nombre_marca = VALUES(nombre_marca),
                nombre_tipo = VALUES(nombre_tipo),
                submodelo = VALUES(submodelo)
        ");

        // Prepare the SQL statement for inserting or updating data in d_parte
        $stmtParte = $pdo->prepare("
            INSERT INTO d_parte (id_modelo, nombre, original, compatible, incel)
            VALUES (:id_modelo, :Parte, :Original, :Compatible, :Incel)
            ON DUPLICATE KEY UPDATE
                nombre = VALUES(nombre),
                original = VALUES(original),
                compatible = VALUES(compatible),
                incel = VALUES(incel)
        ");

        // Process each row in the CSV file
        while (($d = fgetcsv($handle, 1000, ',')) !== false) {
            // Insert or update into d_modelo table
            $data = explode(",", $d[0]);
            
            if($data[0] == 'Marca') continue;
            if($data[0] == '') continue;
            if($data[0] == null) continue;

            $stmt->execute([
                ':Marca' => $data[0],
                ':Tipo' => $data[1],
                ':Modelo' => $data[2],
                ':Submodelo' => $data[3],
            ]);

            // Get the last inserted ID for d_modelo
            $idModelo = $pdo->lastInsertId();

            // Insert or update into d_parte table
            $stmtParte->execute([
                ':id_modelo' => $idModelo,
                ':Parte' => $data[4],
                ':Original' => $data[5],
                ':Compatible' => $data[6],
                ':Incel' => $data[7],
            ]);
        }

        fclose($handle);
        echo "CSV file imported successfully.";
    } else {
        echo "Error opening the file.";
    }
} else {
    echo "No file uploaded or an error occurred.";
}
?>
