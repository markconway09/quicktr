<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require('fpdf186/fpdf.php');

function connect(){
    try {
        $db = "mysql:host=localhost;dbname=quicktrc_formulario";
        return new PDO($db, 'quicktrc_admin', 'quicktr2024');
    } catch (PDOException $e){
        echo $e->getMessage();
    }
}

function subirFirma($id){
    // SUBIR FIRMA
    $folderPath = "upload/";
    $image_parts = explode(";base64,", $_POST['sign']);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $image_id = uniqid() . '.'.$image_type;
    $file = $folderPath . $image_id;
    file_put_contents($file, $image_base64);
    $pdo = connect();
    $stmt = $pdo->prepare("INSERT INTO firma VALUES (null, :id, :archivo)"); 
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':archivo', $file);
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
}

function insertarBDS(){
    $servicio="";$garantia = 0;$pV = "";$cV = "";
    $doc="";$local="";$dir="";$cp="";$email="No especificado";
    $tel="";$nombre="";$ins_d="";$ins_p=0;$metodo="";
    $disp="";$precio=0;$descuento=0;$iva=0;$preciofinal=0;
    $razon="";$dept="";
    if(isset($_POST["servicio"])&&isset($_POST["servicio2"])) $servicio = $_POST["servicio"] . ": " . $_POST["servicio2"];
    if(isset($_POST["tel"])) $tel = $_POST["countryCode"] . $_POST["tel"];
    if(isset($_POST["doc"])) $doc = $_POST["doc"];
    if(isset($_POST["local"])) $local = $_POST["local"];
    if(isset($_POST["razon"])) $razon = $_POST["razon"];
    if(isset($_POST["local"])) $local = $_POST["local"];
    if(isset($_POST["dept"])) $dept = $_POST["dept"];
    if(isset($_POST["motivo"])) $desc = $_POST["motivo"];
    if(isset($_POST["nombre"])) $nombre = $_POST["nombre"];
    if(isset($_POST["direccion"])) $dir = $_POST["direccion"];
    if(isset($_POST["cp"])) $cp = $_POST["cp"];
    if(isset($_POST["email"])) $email = $_POST["email"];
    if(!empty($_POST["insumo_desc"])) $ins_d = $_POST["insumo_desc"];
    if(!empty($_POST["insumo_precio"])) $ins_p = $_POST["insumo_precio"];
    if(!empty($_POST["metodo"])) $metodo = $_POST["metodo"];
    if(!empty($_POST["dispositivo"])) $disp = $_POST["dispositivo"];
    if(!empty($_POST["precio"])) $precio = $_POST["precio"];
    if(!empty($_POST["descuento"])) $descuento = $_POST["descuento"];
    if(!empty($_POST["iva"])) $iva = $_POST["iva"];
    if(!empty($_POST["precio-final"])) $preciofinal = $_POST["precio-final"];

    $pdo = connect();
    $stmt = $pdo->prepare("INSERT INTO info_orden VALUES 
    (null, :nom, :tel, :doc, :ser, :email, :direccion, :cp, :preciosV, :cantV, :precio, :descuento, :iva, :final, :ins_d, :ins_p, :metodo, :disp, :descr, :loc, :fecha, null, :garantia, 0, :razon, :dept)");
    $stmt->bindParam(':nom', $nombre);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':doc', $doc);
    $stmt->bindParam(':ser', $servicio);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':direccion', $dir);
    $stmt->bindParam(':cp', $cp);
    $stmt->bindParam(':preciosV', $pV);
    $stmt->bindParam(':cantV', $cV);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':descuento', $descuento);
    $stmt->bindParam(':iva', $iva);
    $stmt->bindParam(':final', $preciofinal);
    $stmt->bindParam(':ins_d', $ins_d);
    $stmt->bindParam(':ins_p', $ins_p);
    $stmt->bindParam(':metodo', $metodo);
    $stmt->bindParam(':disp', $disp);
    $stmt->bindParam(':descr', $desc);
    $stmt->bindParam(':loc', $local);
    $date = date('Y-m-d');
    $stmt->bindParam(':fecha', $date);
    $stmt->bindParam(':garantia', $garantia);
    $stmt->bindParam(':razon', $razon);
    $stmt->bindParam(':dept', $dept);
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage()."<br>";
        $stmt->debugDumpParams();
    }
    return $pdo->lastInsertId();
}

function selectBD($id=0){
    $pdo = connect();
    if($id == 0){
        $stmt = $pdo->prepare("SELECT *, o.id as id, d.id as did, f.id as fid FROM `info_orden` o LEFT JOIN `devolucion` d ON (d.id_orden = o.id) LEFT JOIN `factura` f ON (f.id_orden = o.id)");
    } else{
        $stmt = $pdo->prepare("SELECT *, o.id as id, d.id as did, f.id as fid FROM `info_orden` o LEFT JOIN `devolucion` d ON (d.id_orden = o.id) LEFT JOIN `factura` f ON (f.id_orden = o.id) WHERE o.`id` = :id");
        $stmt->bindParam(':id', $id);
    }
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function crearPDF($id, $factura=0 , $enviar=0){
    // SERVICIO
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    // DIRECCIÓN
    switch($datos["local"]){
        case 'Barcelona':
            $direccion = 'Carrer de Valencia, 235 P-1, 08007';
            $id = '0002 - '.$id;
            break;
        case 'Mataró':
            $direccion = 'Ronda O\'Donnell, 14-16, 08302 Mataró, Barcelona';
            $id = '0003 - '.$id;
            break;
        default:
            $direccion = 'Carrer de Valencia, 235 P-1, 08007';
            break;
    }

    //---------------CREAR PDF---------------//

    $pdf = new FPDF();
    $width = $pdf->GetPageWidth()/3;
    $pdf->AddPage();
    $pdf->SetMargins(2, 2, 2);
    // LOGO
    $pdf->Cell($width, 5);
    $pdf->Ln(1);
    $pdf->Image('LOGO.png', null, null, $width);
    $pdf->Ln(1);
    // DATOS QTR
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($width/3, 5, 'NIF: B19359082');
    $pdf->SetFont('Arial','',8);
    $fecha = empty($datos["fecha_pago"])?date('d/m/Y'):$datos["fecha_pago"];
    $pdf->Cell($width/3, 5, 'Fecha: ' . $fecha);
    $pdf->Ln();
    $pdf->Cell($width, 5, 'QUICK T&R, S.L.');
    $pdf->Ln(8);
    $pdf->SetFont('Arial','B',8);
    if($factura!=0){
        $pdf->Cell($width, 5, 'FACTURA SIMPLIFICADA # ' . $id, 0, 1);
    } else {
        if(isset($datos["did"])){
            $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'DEVOLUCIÓN # ' . $id), 0, 1);
        } else {
            $pdf->Cell($width, 5, 'TICKET DE SERVICIO # ' . $id, 0, 1);
        }
    }
    $pdf->SetFont('Arial','',8);
    $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', $datos["local"]),0,1);
    $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', $direccion), 0, 1);
    $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 612 259 631'), 0, 1);
    $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Telefono: 933 496 389'), 0, 1);
    $pdf->Ln();
    // DATOS CLIENTE
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($width, 5, 'DATOS DEL CLIENTE', 0, 1, 'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell($width/4, 5, 'Nombre', 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["nombre"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, iconv('UTF-8', 'windows-1252', 'Teléfono'), 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["telefono"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Dni/NIE', 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["documento"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Email', 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["email"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, iconv('UTF-8', 'windows-1252', 'Dirección'), 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["direccion"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, iconv('UTF-8', 'windows-1252', 'C. Postal'), 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["cp"], 1, 1);

    $pdf->Ln(5);

    $pdf->Cell($width/4, 5, 'Servicio', 0, 0);
    $pdf->MultiCell($width/1.5, 5, iconv('UTF-8', 'windows-1252', $datos["servicio"]), 1, 1);
    $pdf->Ln(1);
    $motivo = iconv('UTF-8', 'windows-1252', $datos["desc"]);
    $pdf->Cell($width/4, 5, iconv('UTF-8', 'windows-1252', 'Descripción'), 0, 0);
    $pdf->MultiCell($width/1.5, 5, $motivo, 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Fecha', 0, 0);
    $pdf->Cell($width/1.5, 5, date('d/m/Y'), 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Precio', 0, 0);
    $pdf->Cell($width/1.5, 5, iconv('UTF-8', 'windows-1252', $datos["precio"]." €"), 1, 1);
    $pdf->Ln(1);
    if($datos["descuento"]>0){
        $pdf->Cell($width/4, 5, 'Descuento', 0, 0);
        $pdf->Cell($width/1.5, 5, $datos["descuento"] . '%', 1, 1);
        $pdf->Ln(1);
    }
    $pdf->Cell($width/4, 5, 'IVA', 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["iva"] . '%', 1, 1);
    $pdf->Ln(1);
    if(isset($datos["did"])){
        $pdf->Cell($width/4, 5, iconv('UTF-8', 'windows-1252', 'Devolución'), 0, 0);
        $pdf->Cell($width/1.5, 5, iconv('UTF-8', 'windows-1252', "-".$datos["precio-final"]." €"), 1, 1);
    } else {
        $pdf->Cell($width/4, 5, 'Precio Final', 0, 0);
        $pdf->Cell($width/1.5, 5, iconv('UTF-8', 'windows-1252', $datos["precio-final"]." €"), 1, 1);
    }
    $pdf->Ln(1);
    $pdf->MultiCell($width, 5, iconv('UTF-8', 'windows-1252', 'Método de pago: '.$datos["metodo"]), 0, 0);
    $pdf->Ln(1);

    $pdf->Ln(5);
    $str = '¡¡¡¡¡NO PIERDAS TU TICKET
    PARA RECLAMAR!!!!!
        De acuerdo a nuestras politicas de
        privacidad acepto las condiciones de
        servicio descritas en el correo electronico
        enviado con este ticket.';
    $str = iconv('UTF-8', 'windows-1252', $str);
    $pdf->MultiCell($width, 5, $str, null, 'C');

    $pdf->SetXY($width*1.1, 10);
    $txt1 = iconv('UTF-8', 'windows-1252', 'Políticas de Recogida y Almacenaje del Terminal:
    1. Horario de Atención:
    Estamos disponibles para atender sus
    necesidades de lunes a viernes en dos
    bloques horarios: de 10:00 a 13:00 y de 17:00
    a 20:00. Le pedimos a nuestros clientes que
    coordinen la recogida y entrega dentro de
    estos horarios para garantizar una atención
    eficiente y personalizada.
    2. Diagnóstico Gratuito:
    Nos complace ofrecer un servicio de
    diagnóstico gratuito para evaluar el estado de
    su dispositivo. Nos comprometemos a
    completar este proceso en un plazo máximo
    de 48 horas. En el caso de que se prevea una
    demora, nos comunicaremos previamente
    con el cliente para proporcionar información
    actualizada y transparente.
    3. Tiempo de Reparación:
    El tiempo necesario para la reparación puede
    variar según la complejidad del problema
    identificado durante el diagnóstico. Nuestro
    equipo informará a los clientes sobre el
    tiempo estimado para la reparación una vez
    finalizado el diagnóstico, brindando una
    expectativa realista del proceso.
    4. Almacenaje Post-Reparación:
    Después de completar la reparación, los
    dispositivos podrán permanecer en nuestro
    almacén seguro durante un periodo de hasta
    15 días. En caso de que el cliente necesite
    más tiempo de almacenaje, le pedimos que
    se comunique con nosotros para hacer los
    arreglos necesarios. Pasado este plazo y sin
    previa comunicación, los dispositivos serán
    transferidos a nuestro almacén de reciclaje.');
    $pdf->MultiCell(null, 5, $txt1, null);

    $pdf->SetXY($width*2.025, 16);
    $txt2 = '5. Recuperación de Datos:
    Es importante señalar que en situaciones que
    involucren formateo o reparación de disco, no
    podemos garantizar la recuperación total de
    datos. La viabilidad de la recuperación
    dependerá en gran medida del estado del
    disco o dispositivo. Recomendamos
    encarecidamente a nuestros clientes realizar
    copias de seguridad antes de someter sus
    dispositivos a procesos que puedan afectar la
    integridad de los datos almacenados.
    6. Daños por Almacenaje y Envío:
    Aunque tomamos precauciones rigurosas en
    el manejo y almacenamiento de los
    dispositivos, no nos hacemos responsables
    de los daños que puedan ocurrir durante el
    almacenaje en nuestras instalaciones o
    durante el proceso de envío. Aconsejamos a
    los clientes asegurar adecuadamente sus
    dispositivos antes de la entrega para
    reparación, especialmente si hay
    preocupaciones sobre su fragilidad.
    Nota Importante:
    Al solicitar nuestros servicios, los clientes
    aceptan y reconocen las condiciones
    descritas en estas políticas de recogida y
    almacenaje, las cuales están diseñadas para
    garantizar la transparencia, la eficiencia y el
    cuidado de sus dispositivos.';
    $txt2 = iconv('UTF-8', 'windows-1252', $txt2);
    $pdf->MultiCell(null, 5, $txt2, null);
    
    //---------------END CREAR PDF---------------//
    if($enviar != 0){
        $pdf->Output('F', 'doc.pdf', true);
    } else {
        // ABRIR PDF
        $pdf->Output('I', null, true);
    }
}

function crearFServicio($id, $enviar=0){
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    // DIRECCIÓN
    $direccion = 'CL P.J. Maragall Num 1 16, 28020 Madrid, Madrid';
    switch($datos["local"]){
        case 'Barcelona':
            $id = '0002 - '.$id;
            break;
        case 'Mataró':
            $id = '0003 - '.$id;
            break;
        default:
            $id = '0002 - '.$id;
            break;
    }

    //---------------CREAR PDF---------------//

    $pdf = new FPDF();
    $width = $pdf->GetPageWidth();
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);
    // LOGO
    $pdf->Cell($width, 5);
    $pdf->Ln(1);
    $pdf->Image('LOGO.png', null, null, $width/3);
    $pdf->Ln(1);
    // DATOS QTR
    $pdf->SetFont('Arial','',8);
    $fecha = empty($datos["fecha_pago"])?date('d/m/Y'):$datos["fecha_pago"];
    $pdf->Cell($width/2, 5, 'Fecha: ' . $fecha);
    $pdf->Ln();
    $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', $datos["local"]));
    $pdf->Ln(8);
    $pdf->SetFont('Arial','B',8);
    if(isset($datos["did"])){
        $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', 'DEVOLUCIÓN # ' . $id), 0, 1);
    } else {
        $pdf->Cell($width/2, 5, 'FACTURA DE VENTA # ' . $id, 0, 1);
    }
    $pdf->SetFont('Arial','',8);
    $pdf->Cell($width/2, 5, 'QUICK T&R, S.L.', 0, 1);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($width/2, 5, 'NIF: B19359082', 0, 1);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', $direccion), 0, 1);
    $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 612 259 631'), 0, 1);
    $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', 'Nº Telefono: 933 496 389'), 0, 1);
    $pdf->Ln();

    // DATOS CLIENTE
    $pdf->SetXY($width/2, 20);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($width/2, 5, 'DATOS DEL CLIENTE', 0, 1, 'C');
    $pdf->SetXY($width/2, 25);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell($width/8, 5, 'Nombre', 0, 0);
    $pdf->Cell($width/4, 5, $datos["nombre"], 1, 1);
    $pdf->Ln(1);
    $pdf->SetXY($width/2, 30);
    $pdf->Cell($width/8, 5, iconv('UTF-8', 'windows-1252', 'Teléfono'), 0, 0);
    $pdf->Cell($width/4, 5, $datos["telefono"], 1, 1);
    $pdf->Ln(1);
    $pdf->SetXY($width/2, 35);
    $pdf->Cell($width/8, 5, 'Dni/NIE', 0, 0);
    $pdf->Cell($width/4, 5, $datos["documento"], 1, 1);
    $pdf->Ln(1);
    $pdf->SetXY($width/2, 40);
    $pdf->Cell($width/8, 5, 'Email', 0, 0);
    $pdf->Cell($width/4, 5, $datos["email"], 1, 1);
    $pdf->Ln(1);
    $pdf->SetXY($width/2, 45);
    $pdf->Cell($width/8, 5, iconv('UTF-8', 'windows-1252', 'Dirección'), 0, 0);
    $pdf->Cell($width/4, 5, $datos["direccion"], 1, 1);
    $pdf->Ln(1);
    $pdf->SetXY($width/2, 50);
    $pdf->Cell($width/8, 5, iconv('UTF-8', 'windows-1252', 'C. Postal'), 0, 0);
    $pdf->Cell($width/4, 5, $datos["cp"], 1, 1);

    $pdf->Ln(30);

    $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', 'Descripción'), 1, 0);
    $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'IVA'), 1, 0);
    $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'P.U.'), 1, 0);
    $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'Cant.'), 1, 0);
    $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 'Base Imp.'), 1, 1);

    $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', $datos["desc"]), 1, 0);
    $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', $datos["iva"]."%"), 1, 0);
    $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252',  $datos["precio"]." €"), 1, 0);
    $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252', 1), 1, 0);
    $pdf->Cell($width/10, 5, iconv('UTF-8', 'windows-1252',  $datos["precio"]." €"), 1, 1);
    $iva = round(($datos["precio"] * $datos["iva"])/100, 2);
    
    $pdf->Ln(2);

    $pdf->SetX($width/1.72);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total (Base Imp.): "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $datos["precio"]." €"), 1, 1);
    $pdf->SetX($width/1.72);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total IVA: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $iva." €"), 1, 1);
    if($datos["descuento"]>0){
        $pdf->SetX($width/1.72);
        $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Descuento: "), 1, 0);
        $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $datos["descuento"]." %"), 1, 1);
    }
    $pdf->SetX($width/1.72);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total: "), 1, 0);
    
    if(isset($datos["did"])){
        $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252', "-".$datos["precio-final"]." €"), 1, 1);
    } else {
        $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  ($datos["precio"] + $iva)-((($datos["precio"] + $iva)*$datos["descuento"])/100)." €"), 1, 1);
    }

    $pdf->Ln(5);
    $metodo = $datos["metodo"];
    $pdf->SetX($width/1.8);
    $str = iconv('UTF-8', 'windows-1252', 'Método de pago: '.$metodo);
    $pdf->MultiCell($width/5, 5, $str, null, 'C');

    // ABRIR PDF
    $pdf->Output('I', null, true);
    
    //---------------END CREAR PDF---------------//
    if($enviar != 0) $pdf->Output('F', 'doc.pdf', true);
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

function enviarCorreo($id){
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    if(empty($datos["servicio"])) exit;
    $ser = explode(": ", $datos["servicio"]);

    // ENVIAR CORREO
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";
    $mail->Encoding = 'base64';

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'mail.quicktr.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@quicktr.com';
        $mail->Password   = 'Barcelona2024';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('info@quicktr.com');
        $mail->addAddress('sistemas@dvagroup.es');
        crearPDF($id, 0, 1);
        $mail->addAttachment('doc.pdf');

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Nueva orden de '.$datos["nombre"].' «'.ucfirst($ser[0]).'»';
        $mail->Body    = '<html><body><h1>'.ucfirst($ser[0]).' # '.$id.' - '.$ser[1].'</h1>
            <p>
                De: '.$datos["nombre"].' (<a href="mailto:'.$datos["email"].'">'.$datos["email"].'<a>)
            </p>
            <p>
            Asunto: '.$datos["servicio"].'
            </p>
            <p>
            Nombre: '.$datos["nombre"].'
            </p>
            <p>
            Teléfono: <a href="https://wa.me//'.$datos["telefono"].'" target="_blank">'.$datos["telefono"].'<a>
            </p>
            <p>
            Dni/NIF/NIE: '.$datos["documento"].'
            </p>
            <p>
            Precio: '.$datos["precio"].'
            <br>
            IVA: '.$datos["iva"].'
            <br>
            Precio Total: '.$datos["precio-final"].'
            </p>
            <p>
            Servicio Reportado '.$datos["servicio"].'
            </p>
            <p>
            Observaciones: '.$datos["desc"].'
            </p>
            </body></html>';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function enviarCorreoCliente($id){
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    $ser = explode(": ", $datos["servicio"]);

    // ENVIAR CORREO
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";
    $mail->Encoding = 'base64';

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'mail.quicktr.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@quicktr.com';
        $mail->Password   = 'Barcelona2024';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('info@quicktr.com');
        $mail->addAddress('sistemas@dvagroup.es');
        $mail->addAddress($datos["email"]);
        crearPDF($id, 0, 1);
        $mail->addAttachment('doc.pdf');

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Nueva orden de '.$datos["nombre"].' «'.ucfirst($ser[0]).'»';
        $mail->Body    = '<html><body><h1>'.ucfirst($ser[0]).' # '.$id.' - '.$ser[1].'</h1>
            <p>
                De: '.$datos["nombre"].' (<a href="mailto:'.$datos["email"].'">'.$datos["email"].'<a>)
            </p>
            <p>
            Asunto: '.$datos["servicio"].'
            </p>
            <p>
            Nombre: '.$datos["nombre"].'
            </p>
            <p>
            Teléfono: <a href="https://wa.me//'.$datos["telefono"].'" target="_blank">'.$datos["telefono"].'<a>
            </p>
            <p>
            Dni/NIF/NIE: '.$datos["documento"].'
            </p>
            <p>
            Precio: '.$datos["precio"].'
            <br>
            IVA: '.$datos["iva"].'
            <br>
            Precio Total: '.$datos["precio-final"].'
            </p>
            <p>
            Servicio Reportado '.$datos["servicio"].'
            </p>
            <p>
            Observaciones: '.$datos["desc"].'
            </p>
            </body></html>';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function editarEntrada($id){
    $servicio = $_POST["servicio"] . ": " . $_POST["servicio2"];
    $doc="";$dir="";$cp="";$metodo="";$disp="";
    if(isset($_POST["doc"])) $doc = $_POST["doc"];
    if(isset($_POST["direccion"])) $dir = $_POST["direccion"];
    if(isset($_POST["cp"])) $cp = $_POST["cp"];
    if(isset($_POST["metodo"])) $metodo = $_POST["metodo"];
    if(isset($_POST["dispositivo"])) $disp = $_POST["dispositivo"];

    $k = 1;
    $desc = "";
    $pV = "";
    $cV = "";
    if(!isset($_POST["prod1"])){
        $desc = $_POST["desc"];
    }
    while(isset($_POST["prod".$k]) && $_POST["prod".$k] != ""){
        $desc .= $_POST["prod".$k];
        $pV .= $_POST["prec".$k];
        $cV .= $_POST["cant".$k];
        $k++;
        if(isset($_POST["prod".$k]) && $_POST["prod".$k] != ""){
            $desc .= ";";
            $pV .= ";";
            $cV .= ";";
        }
    }

    $pdo = connect();
    $stmt = $pdo->prepare("UPDATE `info_orden` SET `nombre` = :nombre, `telefono` = :tel, `documento` = :doc, `servicio` = :servicio, `email` = :email, `direccion` = :direccion, `cp` = :cp, `preciosVenta`=:pV, `cantidadVenta`=:cV, `precio` = :precio, `iva` = :iva, `precio-final` = :preciofinal, `descuento` = :descuento, `metodo` = :metodo, `nombre_dispositivo` = :disp, `desc` = :de, `local` = :loc, `razon` = :razon, `dept` = :dept WHERE `info_orden`.`id` = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nombre', $_POST["nombre"]);
    $stmt->bindParam(':tel', $_POST["tel"]);
    $stmt->bindParam(':doc', $doc);
    $stmt->bindParam(':servicio', $servicio);
    $stmt->bindParam(':email', $_POST["email"]);
    $stmt->bindParam(':direccion', $dir);
    $stmt->bindParam(':cp', $cp);
    $stmt->bindParam(':pV', $pV);
    $stmt->bindParam(':cV', $cV);
    $stmt->bindParam(':precio', $_POST["precio"]);
    $stmt->bindParam(':iva', $_POST["iva"]);
    $stmt->bindParam(':preciofinal', $_POST["precio-final"]);
    $stmt->bindParam(':descuento', $_POST["descuento"]);
    $stmt->bindParam(':metodo', $metodo);
    $stmt->bindParam(':disp', $disp);
    $stmt->bindParam(':de', $desc);
    $stmt->bindParam(':loc', $_POST["local"]);
    $stmt->bindParam(':razon', $_POST["razon"]);
    $stmt->bindParam(':dept', $_POST["dept"]);
    try {
        $stmt->execute();
    } catch (PDOException $e){
        echo '<p class="text-light">'.$e->getMessage().'</p>';
    }
    
    header('Location: index.php?pag=list&id='.$id);
}

function garantia($id){
    $servicio = $_POST["servicio"] . ": " . $_POST["servicio2"];
    $garantia = $id;
    $desc = "";
    $pV = "";
    $cV = "";
    $doc="";$dir="";$cp="";$email="No especificado";$tel="";$nombre="";$ins_d="";$ins_p=0;$metodo="";$disp="";
    if(isset($_POST["tel"])) $tel = $_POST["tel"];
    if(isset($_POST["desc"])) $desc = $_POST["desc"];
    if(isset($_POST["doc"])) $doc = $_POST["doc"];
    if(isset($_POST["nombre"])) $nombre = $_POST["nombre"];
    if(isset($_POST["direccion"])) $dir = $_POST["direccion"];
    if(isset($_POST["cp"])) $cp = $_POST["cp"];
    if(isset($_POST["email"])) $email = $_POST["email"];
    if(!empty($_POST["insumo_desc"])) $ins_d = $_POST["insumo_desc"];
    if(!empty($_POST["insumo_precio"])) $ins_p = $_POST["insumo_precio"];
    if(!empty($_POST["metodo"])) $metodo = $_POST["metodo"];
    if(!empty($_POST["dispositivo"])) $disp = $_POST["dispositivo"];

    $pdo = connect();
    $stmt = $pdo->prepare("INSERT INTO info_orden VALUES 
    (null, :nom, :tel, :doc, :ser, :email, :direccion, :cp, :preciosV, :cantV, :precio, :descuento, :iva, :final, :ins_d, :ins_p, :metodo, :disp, :descr, :loc, :fecha, null, :garantia, 0, :razon, :dept)");
    $stmt->bindParam(':nom', $nombre);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':doc', $doc);
    $stmt->bindParam(':ser', $servicio);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':direccion', $dir);
    $stmt->bindParam(':cp', $cp);
    $stmt->bindParam(':preciosV', $pV);
    $stmt->bindParam(':cantV', $cV);
    $stmt->bindParam(':precio', $_POST["precio"]);
    $stmt->bindParam(':descuento', $_POST["descuento"]);
    $stmt->bindParam(':iva', $_POST["iva"]);
    $stmt->bindParam(':final', $_POST["precio-final"]);
    $stmt->bindParam(':ins_d', $ins_d);
    $stmt->bindParam(':ins_p', $ins_p);
    $stmt->bindParam(':metodo', $metodo);
    $stmt->bindParam(':disp', $disp);
    $stmt->bindParam(':descr', $desc);
    $stmt->bindParam(':loc', $_POST["local"]);
    $date = date('Y-m-d');
    $stmt->bindParam(':fecha', $date);
    $stmt->bindParam(':garantia', $garantia);
    $stmt->bindParam(':razon', $_POST["razon"]);
    $stmt->bindParam(':dept', $_POST["dept"]);
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage()."<br>";
        $stmt->debugDumpParams();
    }
    return $pdo->lastInsertId();
}

function devolucion($id, $des = 0){
    $pdo = connect();
    if($des === 0){
        $stmt = $pdo->prepare("INSERT INTO devolucion VALUES (null, :id)");
        $stmt->bindParam(':id', $id);
    } else {
        $stmt = $pdo->prepare("DELETE FROM `devolucion` WHERE `devolucion`.`id_orden` = :id");
        $stmt->bindParam(':id', $id);
    }
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage()."<br>";
        $stmt->debugDumpParams();
    }
    header('Location: index.php?pag=list');
}

function cambiarEstado($id, $estado, $redirect, $metodo=null){
    $date = $estado == 4?date('Y-m-d'):null;
    $pdo = connect();
    $stmt = $pdo->prepare("UPDATE `info_orden` SET `estado` = :estado, `fecha_pago` = :pago, `metodo` = :metodo WHERE `info_orden`.`id` = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':pago', $date);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':metodo', $metodo);
    try {
        $stmt->execute();
    } catch (PDOException $e){
        echo '<p class="text-light">'.$e->getMessage().'</p>';
    }
    if($redirect==0){
        header('Location: index.php?pag=list');
    } else {
        header('Location: index.php?pag=list&id='.$id);
    }
}

function eliminarEntrada($id){
    $pdo = connect();
    $stmt = $pdo->prepare("DELETE FROM info_orden WHERE `id` = :num");
    $stmt->bindParam(":num", $id);
    $stmt->execute();
    header('Location: index.php?pag=list');
}

function totalVentas($d=0, $m, $y, $local=0){
    $date = $d==0?$m."/".$y:$d."/".$m."/".$y;
    $pdo = connect();
    if($d == 0){
        if($local === 0){
            $stmt = $pdo->prepare("SELECT *, o.id as id, f.id as fid, d.id as did FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) WHERE MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
            $total_general = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
            $total_tarjeta = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND metodo='tarjeta' AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
            $total_efectivo = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND metodo='efectivo' AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
        } else {
            $stmt = $pdo->prepare("SELECT *, o.id as id, f.id as fid, d.id as did FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) WHERE MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
            $total_general = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
            $total_tarjeta = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND metodo='tarjeta' AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
            $total_efectivo = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND metodo='efectivo' AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
            $stmt->bindParam(':loc', $local);
            $total_general->bindParam(':loc', $local);
            $total_tarjeta->bindParam(':loc', $local);
            $total_efectivo->bindParam(':loc', $local);
        }
        $stmt->bindParam(':m', $m);
        $stmt->bindParam(':y', $y);
        $total_general->bindParam(':m', $m);
        $total_general->bindParam(':y', $y);
        $total_tarjeta->bindParam(':m', $m);
        $total_tarjeta->bindParam(':y', $y);
        $total_efectivo->bindParam(':m', $m);
        $total_efectivo->bindParam(':y', $y);
    } else {
        if($local === 0){
            $stmt = $pdo->prepare("SELECT *, o.id as id, f.id as fid, d.id as did FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) WHERE DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
            $total_general = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
            $total_tarjeta = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND metodo='tarjeta' AND DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
            $total_efectivo = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND metodo='efectivo' AND DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
        } else {
            $stmt = $pdo->prepare("SELECT *, o.id as id, f.id as fid, d.id as did FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) WHERE DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
            $total_general = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
            $total_tarjeta = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND metodo='tarjeta' AND DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
            $total_efectivo = $pdo->prepare("SELECT round(sum(`precio`),2) as base, round(sum(`precio-final`),2) as total FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) where d.id IS NULL AND metodo='efectivo' AND DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
            $stmt->bindParam(':loc', $local);
            $total_general->bindParam(':loc', $local);
            $total_tarjeta->bindParam(':loc', $local);
            $total_efectivo->bindParam(':loc', $local);
        }
        $stmt->bindParam(':d', $d);
        $stmt->bindParam(':m', $m);
        $stmt->bindParam(':y', $y);
        $total_general->bindParam(':d', $d);
        $total_general->bindParam(':m', $m);
        $total_general->bindParam(':y', $y);
        $total_tarjeta->bindParam(':d', $d);
        $total_tarjeta->bindParam(':m', $m);
        $total_tarjeta->bindParam(':y', $y);
        $total_efectivo->bindParam(':d', $d);
        $total_efectivo->bindParam(':m', $m);
        $total_efectivo->bindParam(':y', $y);
    }
    try {
        $stmt->execute();
        $total_general->execute();
        $total_tarjeta->execute();
        $total_efectivo->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    $total_general = $total_general->fetch(PDO::FETCH_ASSOC);
    $total_tarjeta = $total_tarjeta->fetch(PDO::FETCH_ASSOC);
    $total_efectivo = $total_efectivo->fetch(PDO::FETCH_ASSOC);
    
    // CREAR PDF
    $pdf = new FPDF("L");
    $width = $pdf->GetPageWidth();
    $pdf->AddPage();
    $pdf->SetMargins(10, 5, 5);
    // LOGO
    $pdf->Cell($width, 5);
    $pdf->Ln(1);
    $pdf->Image('LOGO.png', null, null, $width/3);
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', "Fecha: ".$date), 0, 1);//
    if($local!=0) $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', "Local: ".$local), 0, 1);
    $pdf->Ln(5);
    // DATOS
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(28, 5, iconv('UTF-8', 'windows-1252', 'ID - Fecha'), 1, 0);
    $pdf->Cell($width/2-60, 5, iconv('UTF-8', 'windows-1252', 'Descripción'), 1, 0);
    $pdf->Cell(4, 5, iconv('UTF-8', 'windows-1252', 'T'), 1, 0);
    $pdf->Cell(4, 5, iconv('UTF-8', 'windows-1252', 'F'), 1, 0);
    $pdf->Cell(4, 5, iconv('UTF-8', 'windows-1252', 'S'), 1, 0);
    $pdf->Cell(17, 5, iconv('UTF-8', 'windows-1252', 'Local'), 1, 0);
    $pdf->Cell(15, 5, iconv('UTF-8', 'windows-1252', 'Método'), 1, 0);
    $pdf->Cell(10, 5, iconv('UTF-8', 'windows-1252', '- %'), 1, 0);
    $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252', 'IVA'), 1, 0);
    $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252', 'P.U.'), 1, 0);
    $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252', 'Cant.'), 1, 0);
    $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252', 'Base Imp.'), 1, 0);
    $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252', 'Total'), 1, 1);
    $pdf->SetFont('Arial','',8);
    
    //$total_total = 0;
    $iva_total = 0;
    //$total_efectivo = 0;
    //$total_tarjeta = 0;
    $iva_efectivo = 0;
    $iva_tarjeta = 0;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $pdf->Cell(28, 5, iconv('UTF-8', 'windows-1252', $row["id"]." - ".(empty($row["fecha_pago"])?"Sin fecha":$row["fecha_pago"])), 1, 0);
        $pdf->Cell($width/2-60, 5, iconv('UTF-8', 'windows-1252', ucfirst($row["servicio"])), 1, 0);
        $pdf->Cell(4, 5, iconv('UTF-8', 'windows-1252', $row["ticket"]?"X":""), 1, 0);
        $pdf->Cell(4, 5, iconv('UTF-8', 'windows-1252', $row["factura"]?"X":""), 1, 0);
        $pdf->Cell(4, 5, iconv('UTF-8', 'windows-1252', $row["simplificada"]?"X":""), 1, 0);
        $pdf->Cell(17, 5, iconv('UTF-8', 'windows-1252', $row["local"]), 1, 0);
        $pdf->Cell(15, 5, iconv('UTF-8', 'windows-1252', $row["metodo"]), 1, 0);
        $pdf->Cell(10, 5, iconv('UTF-8', 'windows-1252', $row["descuento"]."%"), 1, 0);
        $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252', $row["iva"]."%"), 1, 0);
        $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252',  $row["precio"]." €"), 1, 0);
        $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252', 1), 1, 0);
        $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252',  $row["precio"]." €"), 1, 0);
        $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252',  $row["precio-final"]." €"), 1, 1);
        if(!empty($row["did"])){
            $pdf->Cell(28, 5, iconv('UTF-8', 'windows-1252', $row["id"]." - ".$row["fecha"]), 1, 0);
            $pdf->Cell($width-75.3, 5, iconv('UTF-8', 'windows-1252', "DEVOLUCIÓN"), 1, 0);
            $pdf->Cell($width/15, 5, iconv('UTF-8', 'windows-1252',  "-".$row["precio-final"]." €"), 1, 1);
        } else {
            $final = $row["precio"] - ($row["precio"]/100*$row["descuento"]);
            $iva = round(($final * $row["iva"])/100, 2);
            $iva_total+=$iva;
            //$total_total+=doubleval($row["precio"]);
            if($row["metodo"] == "Efectivo") {
                //$total_efectivo += doubleval($final);
                $iva_efectivo += $iva;
            }
            if($row["metodo"] == "Tarjeta") {
                //$total_tarjeta += doubleval($final);
                $iva_tarjeta += $iva;
            }
        }
        $pdf->Ln(5);
    }
    
    $pdf->Ln(15);

    // TOTAL TOTAL
    $pdf->SetX(10);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "TOTAL"), 0, 1);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total (Base Imp.): "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $total_general["base"]." €"), 1, 1);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Descuento: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  ($total_general["total"]-$iva_total-$total_general["base"])." €"), 1, 1);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total IVA: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $iva_total." €"), 1, 1);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $total_general["total"]." €"), 1, 1);
    
    $pdf->Ln(15);

    $pdf->SetX(10);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "TARJETA"), 0, 0);
    $pdf->SetX(122);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "EFECTIVO"), 0, 1);

    // BASE TARJETA
    $pdf->SetX(10);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total (Base Imp.): "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $total_tarjeta["base"]." €"), 1, 0);
    // BASE EFECTIVO
    $pdf->SetX(122);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total (Base Imp.): "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $total_efectivo["base"]." €"), 1, 1);

    // IVA TARJETA
    $pdf->SetX(10);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total IVA: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $iva_tarjeta." €"), 1, 0);
    // IVA EFECTIVO
    $pdf->SetX(122);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total IVA: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $iva_efectivo." €"), 1, 1);

    // TOTAL TARJETA
    $pdf->SetX(10);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $total_tarjeta["total"]." €"), 1, 0);
    // TOTAL EFECTIVO
    $pdf->SetX(122);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $total_efectivo["total"]." €"), 1, 1);
    
    // ABRIR PDF
    $pdf->Output('I', null, true);
}

function tesoreria($m, $y, $local){
    $pdo = connect();
    $stmt = $pdo->prepare("SELECT fecha, COUNT(*) as cant, SUM(`precio-final`) as total FROM `info_orden` WHERE MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc GROUP BY fecha");
    $stmt->bindParam(':loc', $local);
    $stmt->bindParam(':m', $m);
    $stmt->bindParam(':y', $y);
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
    $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', "Fecha: ".$m."/".$y), 0, 1);
    if($local!=0) $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', "Local: ".$local), 0, 1);
    $pdf->Ln(5);

    // HEADERS
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($width/6-12, 5, iconv('UTF-8', 'windows-1252', 'Fecha'), 1, 0);
    $pdf->Cell($width/6-12, 5, iconv('UTF-8', 'windows-1252', 'Cant.'), 1, 0);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', 'Efectivo'), 1, 0);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', 'Tarjeta'), 1, 0);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', 'Otros'), 1, 0);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', 'Total'), 1, 1);
    $pdf->SetFont('Arial','',8);

    // DATOS
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $tar = $pdo->prepare("SELECT SUM(`precio-final`) as total_tarjeta FROM `info_orden` WHERE `metodo` = 'tarjeta' AND `fecha` = :fecha AND `local` = :loc");
        $tar->bindParam(':fecha', $row["fecha"]);
        $tar->bindParam(':loc', $local);
        $efe = $pdo->prepare("SELECT SUM(`precio-final`) as total_efectivo FROM `info_orden` WHERE `metodo` = 'efectivo' AND `fecha` = :fecha AND `local` = :loc");
        $efe->bindParam(':fecha', $row["fecha"]);
        $efe->bindParam(':loc', $local);
        try {
            $tar->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
        try {
            $efe->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
        $tarj = $tar->fetch(PDO::FETCH_ASSOC);
        $efec = $efe->fetch(PDO::FETCH_ASSOC);
        $pdf->Cell($width/6-12, 5, iconv('UTF-8', 'windows-1252', $row["fecha"]), 1, 0);
        $pdf->Cell($width/6-12, 5, iconv('UTF-8', 'windows-1252', $row["cant"]), 1, 0);
        $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', round($efec["total_efectivo"], 2). " €"), 1, 0);
        $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', round($tarj["total_tarjeta"],2). " €"), 1, 0);
        $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', round(($row["total"] - $efec["total_efectivo"] - $tarj["total_tarjeta"]),2). " €"), 1, 0);
        $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', round($row["total"],2). " €"), 1, 1);
    }

    // ABRIR PDF
    $pdf->Output('I', null, true);
}

function totalGastos($m, $y, $local=0){
    $date = $m."/".$y;
    $pdo = connect();
    if($local == 0){
        $stmt = $pdo->prepare("SELECT *, o.id as id, f.id as fid, d.id as did FROM `factura` f RIGHT JOIN `info_orden` o ON (f.id_orden = o.id) LEFT JOIN `devolucion` d ON (f.id_orden = d.id_orden) WHERE MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
    } else {
        $stmt = $pdo->prepare("SELECT *, o.id as id, f.id as fid, d.id as did FROM `factura` f RIGHT JOIN `info_orden` o ON (f.id_orden = o.id) LEFT JOIN `devolucion` d ON (f.id_orden = d.id_orden) WHERE MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
        $stmt->bindParam(':loc', $local);
    }
    $stmt->bindParam(':m', $m);
    $stmt->bindParam(':y', $y);
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
    $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', "Fecha: ".$m."/".$y), 0, 1);
    if($local!=0) $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', "Local: ".$local), 0, 1);
    $pdf->Ln(5);

    // HEADERS
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', 'ID - Fecha'), 1, 0);
    $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', 'Precio'), 1, 0);
    $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', 'Insumo'), 1, 0);
    $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', 'Coste'), 1, 0);
    $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', 'Total'), 1, 1);
    $pdf->SetFont('Arial','',8);

    $total = 0;
    // DATOS
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if($row["insumo_precio"] == "" || $row["insumo_precio"] == 0) continue;
        $g = $row["precio-final"] - $row["insumo_precio"];
        $total += $g;
        $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', $row["id"] . " - " . $row["fecha"]), 1, 0);
        $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', $row["precio-final"] . " €"), 1, 0);
        $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', $row["insumo_desc"]), 1, 0);
        $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', $row["insumo_precio"] . " €"), 1, 0);
        $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', $g . " €"), 1, 1);
    }
    $pdf->Ln(5);
    $pdf->Cell($width/5.65, 5, iconv('UTF-8', 'windows-1252', "Total: " . $total . " €"), 1, 1);

    // ABRIR PDF
    $pdf->Output('I', null, true);
}

?>