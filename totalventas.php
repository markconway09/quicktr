<?php
require_once 'functions.php';

$pdo = connect();

$stmt = $pdo->prepare("SELECT * FROM `info_orden` WHERE `fecha` = :fecha");
$date = date('Y-m-d');
$stmt->bindParam(':fecha', $date);
try {
    $stmt->execute();
} catch(PDOException $e){
    echo $e->getMessage();
}

$stmt2 = $pdo->prepare("SELECT round(sum(`precio-final`),2) as total, round(sum(`precio`),2) as base FROM `info_orden` WHERE `fecha` = :fecha");
$stmt2->bindParam(':fecha', $date);
try {
    $stmt2->execute();
} catch(PDOException $e){
    echo $e->getMessage();
}
$t=$stmt2->fetch(PDO::FETCH_ASSOC);

// CREAR PDF
$pdf = new FPDF();
$width = $pdf->GetPageWidth();
$pdf->AddPage();
$pdf->SetMargins(10, 5, 5);
// LOGO
$pdf->Cell($width, 5);
$pdf->Ln(1);
$pdf->Image('LOGO.png', null, null, $width/3);
$pdf->Ln(5);
$pdf->SetFont('Arial','',8);
$pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', "Fecha: ".$date), 0, 1);
$pdf->Ln(5);
// DATOS

$pdf->SetFont('Arial','B',8);
$pdf->Cell(20, 5, iconv('UTF-8', 'windows-1252', 'ID'), 1, 0);
$pdf->Cell($width/2-20, 5, iconv('UTF-8', 'windows-1252', 'Descripción'), 1, 0);
$pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'IVA'), 1, 0);
$pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'P.U.'), 1, 0);
$pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'Cant.'), 1, 0);
$pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'Base Imp.'), 1, 1);
$pdf->SetFont('Arial','',8);

$iv = 0;
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    if($row["tipo"] == "servicio"){
        $pdf->Cell(20, 5, iconv('UTF-8', 'windows-1252', $row["id"]), 1, 0);
        $pdf->Cell($width/2-20, 5, iconv('UTF-8', 'windows-1252', $row["servicio"]), 1, 0);
        $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', $row["iva"]), 1, 0);
        $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252',  $row["precio"]." €"), 1, 0);
        $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 1), 1, 0);
        $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252',  $row["precio"]." €"), 1, 1);
        $iva = round(($row["precio"] * $row["iva"])/100, 2);
        $iv+=$iva;
    }else{
        $pro = explode(";", $row["desc"]);
        $pre = explode(";", $row["preciosVenta"]);
        $can = explode(";", $row["cantidadVenta"]);
        $total = 0;
        $iva = 0;
        for($i = 0;$i<count($pro);$i++){
            if($pro[$i] == null) break;
            $p = isset($pre[$i]) ? $pre[$i] : 0;
            $c = isset($can[$i]) ? $can[$i] : 1;
    
            $pdf->Cell(20, 5, iconv('UTF-8', 'windows-1252', $row["id"]), 1, 0);
            $pdf->Cell($width/2-20, 5, iconv('UTF-8', 'windows-1252', $pro[$i]), 1, 0);
            $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', $row["iva"]), 1, 0);
            $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252',  $p." €"), 1, 0);
            $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', $c), 1, 0);
            $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252',  ($c*$p)." €"), 1, 1);
            $total += ($p*$c);
        }
        $iva = round(($total * $row["iva"])/100, 2);
        $iv+=$iva;
    }
    $pdf->Ln(5);
}
    
$pdf->Ln(2);

$pdf->SetX($width/1.73);
$pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total (Base Imp.): "), 1, 0);
$pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $t["base"]." €"), 1, 1);
$pdf->SetX($width/1.73);
$pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total IVA: "), 1, 0);
$pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $iv." €"), 1, 1);
$pdf->SetX($width/1.73);
$pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total: "), 1, 0);
$pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  ($t["total"])." €"), 1, 1);
// ABRIR PDF
$pdf->Output('I', null, true);