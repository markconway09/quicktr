<?php
require('fpdf186/fpdf.php');

include_once "functions_form.php";
include_once "functions_list.php";
include_once "functions_admin.php";

function connect(){
    try {
        $db = "mysql:host=localhost;dbname=quicktrc_formulario";
        return new PDO($db, 'uvzcmq8ynnon4', 'quicktr2024');
    } catch (PDOException $e){
        echo $e->getMessage();
    }
}

function selectBD($id=0){
    $pdo = connect();
    if($id == 0){
        $stmt = $pdo->prepare("SELECT *, o.id as id, d.id as did, f.id as fid, s.archivo as firma FROM `info_orden` o LEFT JOIN `devolucion` d ON (d.id_orden = o.id) LEFT JOIN `factura` f ON (f.id_orden = o.id) LEFT JOIN `firma` s ON (s.id_orden = o.id)");
    } else{
        $stmt = $pdo->prepare("SELECT *, o.id as id, d.id as did, f.id as fid, s.archivo as firma FROM `info_orden` o LEFT JOIN `devolucion` d ON (d.id_orden = o.id) LEFT JOIN `factura` f ON (f.id_orden = o.id) LEFT JOIN `firma` s ON (s.id_orden = o.id) WHERE o.`id` = :id");
        $stmt->bindParam(':id', $id);
    }
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertFactura($id, $tipo){
    $pdo = connect();
    $check = $pdo->prepare("SELECT * FROM factura WHERE `id_orden` = :id");
    $check->bindParam(':id', $id);
    $check->execute();
    $row = $check->fetch(PDO::FETCH_ASSOC);
    if(!empty($row["id"])){
        // SI YA EXISTE ESA ORDEN EN LA TABLA SOLO ACTUALIZAMOS
        if($tipo == 0){
            $stmt = $pdo->prepare("UPDATE factura SET `factura`.`factura` = '1' WHERE `id` = :id");
        } else if($tipo == 1) {
            $stmt = $pdo->prepare("UPDATE factura SET `simplificada` = '1' WHERE `id` = :id");
        } else if($tipo == 2) {
            $stmt = $pdo->prepare("UPDATE factura SET `ticket` = '1' WHERE `id` = :id");
        }
        $stmt->bindParam(':id', $row["id"]);
    } else {
        // SINO INSERTAMOS
        $fac=0;$simp=0;$ticket=0;
        if($tipo == 0){
            $fac = 1;
        } else if($tipo == 1) {
            $simp = 1;
        } else if($tipo == 2) {
            $ticket = 1;
        }
        $stmt = $pdo->prepare("INSERT INTO factura VALUES (null, :id, :fac, :simp, :ticket)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':fac', $fac);
        $stmt->bindParam(':simp', $simp);
        $stmt->bindParam(':ticket', $ticket);
    }
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage()."<br>";
        $stmt->debugDumpParams();
    }
}


function logError($errorMessage, $logFile = 'error_log.txt') {
    // Ensure the message is sanitized
    $sanitizedMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8');

    // Prepare the log entry with timestamp
    $logEntry = "[" . date("Y-m-d H:i:s") . "] " . $sanitizedMessage . PHP_EOL;

    // Write the log entry to the specified file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>