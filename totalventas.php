<?php
require_once 'functions.php';

if(isset($_GET["total"])){
    $pdo = connect();
    if($_GET["total"] == "mes"){
        $stmt = $pdo->prepare("SELECT * FROM `info_orden` WHERE MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
        $m = date('m');
        $y = date('Y');
        $stmt->bindParam(':m', $m);
        $stmt->bindParam(':y', $y);
        $date = date('m-Y');
    } else {
        $stmt = $pdo->prepare("SELECT * FROM `info_orden` WHERE DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
        $d = date('d');
        $m = date('m');
        $y = date('Y');
        $stmt->bindParam(':d', $d);
        $stmt->bindParam(':m', $m);
        $stmt->bindParam(':y', $y);
        $date = date('d-m-Y');
    }
} else {exit;}

try {
    $stmt->execute();
} catch(PDOException $e){
    echo $e->getMessage();
}

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
$pdf->Cell(30, 5, iconv('UTF-8', 'windows-1252', 'ID'), 1, 0);
$pdf->Cell($width/2-30, 5, iconv('UTF-8', 'windows-1252', 'Descripción'), 1, 0);
$pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'IVA'), 1, 0);
$pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'P.U.'), 1, 0);
$pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'Cant.'), 1, 0);
$pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'Base Imp.'), 1, 1);
$pdf->SetFont('Arial','',8);

$tot = 0;
$iv = 0;
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    if($row["tipo"] == "servicio"){
        $pdf->Cell(30, 5, iconv('UTF-8', 'windows-1252', $row["id"]." - ".$row["tipo"]), 1, 0);
        $pdf->Cell($width/2-30, 5, iconv('UTF-8', 'windows-1252', $row["servicio"]), 1, 0);
        $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', $row["iva"]), 1, 0);
        $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252',  $row["precio"]." €"), 1, 0);
        $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 1), 1, 0);
        $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252',  $row["precio"]." €"), 1, 1);
        $iva = round(($row["precio"] * $row["iva"])/100, 2);
        $iv+=$iva;
        $tot+=doubleval($row["precio"]);
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
    
            $pdf->Cell(30, 5, iconv('UTF-8', 'windows-1252', $row["id"]." - ".$row["tipo"]), 1, 0);
            $pdf->Cell($width/2-30, 5, iconv('UTF-8', 'windows-1252', $pro[$i]), 1, 0);
            $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', $row["iva"]), 1, 0);
            $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252',  doubleval($p)." €"), 1, 0);
            $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', $c), 1, 0);
            $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252',  (intval($c)*doubleval($p))." €"), 1, 1);
            $total += (doubleval($p)*intval($c));
        }
        $tot+=$total;
        $iva = round(($total * $row["iva"])/100, 2);
        $iv+=$iva;
    }
    $pdf->Ln(5);
}
    
$pdf->Ln(2);

$pdf->SetX($width/1.73);
$pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total (Base Imp.): "), 1, 0);
$pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $tot." €"), 1, 1);
$pdf->SetX($width/1.73);
$pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total IVA: "), 1, 0);
$pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $iv." €"), 1, 1);
$pdf->SetX($width/1.73);
$pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total: "), 1, 0);
$pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  ($tot+$iv)." €"), 1, 1);

// ABRIR PDF
$pdf->Output('I', null, true);