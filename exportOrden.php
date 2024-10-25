<?php 

require_once 'functions.php'; 

$filename = "ordenes_" . date('Y-m-d') . ".csv"; 

$delimiter = ","; 

$f = fopen('php://memory', 'w'); 
fputs($f, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

$fields = array('ID', 'Nombre', 'Telefono', 'Documento', 'Email', 'Servicio', 'Insumo', 'Precio Final', 'Metodo', 'Descripción', 'Local', 'Fecha', 'Estado'); 

fputcsv($f, $fields, $delimiter); 

$pdo = connect();
$stmt = $pdo->prepare("SELECT * FROM InfoOrden");
try {
    $stmt->execute();
} catch(PDOException $e){
    echo $e->getMessage();
}

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $description = str_replace(array("\r\n", "\n", "\r"), ' ', $row["desc"]);
    $description = preg_replace('/[\r\n\t]+/', ' ', $row["desc"]);
    $lineData = array(
        $row['id'], 
        $row['nombre'], 
        $row['telefono'], 
        $row['documento'], 
        $row['email'], 
        $row['servicio'], 
        $row["insumo_precio"], 
        $row["precio-final"], 
        $row["metodo"], 
        '"' . $description . '"', // Wrap description in quotes
        $row["local"], 
        $row["fecha"], 
        $row["estado"]
    );
    fputcsv($f, $lineData, $delimiter);
}

fseek($f, 0); 

header('Content-Type: text/csv'); 

header('Content-Disposition: attachment; filename="' . $filename . '";'); 

fpassthru($f); 

exit();

?>