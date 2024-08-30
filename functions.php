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

function subirFirma(){
    // SUBIR FIRMA
    $folderPath = "upload/";
    $image_parts = explode(";base64,", $_POST['sign']);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $image_id = uniqid() . '.'.$image_type;
    $file = $folderPath . $image_id;
    file_put_contents($file, $image_base64);
    return $file;
}

function insertarBD($firma){
    $tel = $_POST["countryCode"] . $_POST["tel"];
    if($_POST["tipo"]=='servicio'){
        $servicio = $_POST["servicio"];
    }else{
        $servicio = "Venta";
    }
    $doc="-";$dir="-";$cp="-";
    if(isset($_POST["doc"])) $doc = $_POST["doc"];
    if(isset($_POST["direccion"])) $dir = $_POST["direccion"];
    if(isset($_POST["cp"])) $cp = $_POST["cp"];
    $pdo = connect();
    $stmt = $pdo->prepare("INSERT INTO info_orden VALUES 
    (null, :nom, :tel, :doc, :ser, :email, :direccion, :cp, :precio, :iva, :final, :descr, :loc, :fecha, :firma, :tipo, :razon, :dept)");
    $stmt->bindParam(':nom', $_POST["nombre"]);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':doc', $doc);
    $stmt->bindParam(':ser', $servicio);
    $stmt->bindParam(':email', $_POST["email"]);
    $stmt->bindParam(':direccion', $dir);
    $stmt->bindParam(':cp', $cp);
    $stmt->bindParam(':precio', $_POST["precio"]);
    $stmt->bindParam(':iva', $_POST["iva"]);
    $stmt->bindParam(':final', $_POST["precio-final"]);
    if($_POST["tipo"]=='servicio'){
        $stmt->bindParam(':descr', $_POST["motivo"]);
    }else{
        $stmt->bindParam(':descr', $_POST["desc"]);
    }
    $stmt->bindParam(':loc', $_POST["local"]);
    $date = date('Y-m-d');
    $stmt->bindParam(':fecha', $date);
    if($_POST["tipo"]=='servicio'){
        $stmt->bindParam(':firma', explode("/", $firma)[1]);
    }else{
        $stmt->bindValue(':firma', null, PDO::PARAM_NULL);
    }
    $stmt->bindParam(':tipo', $_POST["tipo"]);
    $stmt->bindParam(':razon', $_POST["razon"]);
    $stmt->bindParam(':dept', $_POST["dept"]);
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    return $pdo->lastInsertId();
}

function selectBD($id=0){
    $pdo = connect();
    if($id == 0){
        $stmt = $pdo->prepare("SELECT * FROM `info_orden`");
    } else{
        $stmt = $pdo->prepare("SELECT * FROM `info_orden` WHERE `id` = :id");
        $stmt->bindParam(':id', $id);
    }
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function crearPDF($id, $enviar=0){
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    // DIRECCIÓN
    switch($datos["local"]){
        case 'Barcelona':
            $direccion = 'Carrer de Valencia, 235 P-1, 08007';
            break;
        case 'Mataró':
            $direccion = 'Ronda O\'Donnell, 14-16, 08302 Mataró, Barcelona';
            break;
        case 'Badalona':
            $direccion = 'Badalona';
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
    $pdf->Cell($width/3, 5, date('d/m/Y'));
    $pdf->Ln();
    $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', $datos["local"]));
    $pdf->Ln(8);
    $pdf->SetFont('Arial','B',8);
    $orden = $datos["tipo"] == 'servicio'?'TICKET DE SERVICIO':'FACTURA DE VENTA';
    $pdf->Cell($width, 5, $orden . ' # ' . $id, 0, 1);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell($width, 5, 'QUICK T&R, S.L.', 0, 1);
    $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', $direccion), 0, 1);
    $pdf->Cell($width, 5, 'Nro Telefono 933496389', 0, 1);
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
    $pdf->Cell($width/1.5, 5, iconv('UTF-8', 'windows-1252', $datos["servicio"]), 1, 1);
    $pdf->Ln(1);
    $motivo = iconv('UTF-8', 'windows-1252', $datos["desc"]);
    $pdf->Cell($width/4, 5, iconv('UTF-8', 'windows-1252', 'Descripción'), 0, 0);
    $pdf->MultiCell($width/1.5, 5, $motivo, 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Fecha', 0, 0);
    $pdf->Cell($width/1.5, 5, date('d/m/Y'), 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Precio', 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["precio"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'IVA', 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["iva"] . '%', 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Precio Final', 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["precio-final"], 1, 1);
    $pdf->Ln(1);
    // FIRMA
    if($datos["firma"]!=null){
        $pdf->Cell($width/4, 5, 'Firma', 0, 0);
        $pdf->Cell($width/1.5, 32, '', 1, 0);
        $pdf->Ln(1);
        $pdf->Image('upload/'.$datos["firma"], 20, null, $width/1.55);
    }

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

    // ABRIR PDF
    $pdf->Output('I', null, true);
    
    //---------------END CREAR PDF---------------//
    if($enviar != 0) $pdf->Output('F', 'doc.pdf', true);
}

function crearTVenta(){
    //---------------RECOGER DATOS---------------//
    $n = $_POST["nombre"];
    $p = $_POST["precio"];
    $i = $_POST["iva"];
    $f = $_POST["precio-final"];
    $l = $_POST["local"];
    $c = intval($_POST["cant"])!=0?$_POST["cant"]:1;
    $t = floatval($f) * intval($c);
    
    switch($l){
        case 'Barcelona':
            $direccion = 'Carrer de Valencia, 235 P-1, 08007';
            break;
        case 'Mataró':
            $direccion = 'Ronda O\'Donnell, 14-16, 08302 Mataró, Barcelona';
            break;
        case 'Badalona':
            $direccion = 'Badalona';
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
    $pdf->Cell($width/3, 5, date('jS \of F Y'));
    $pdf->Ln();
    $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', $l));
    $pdf->Ln(8);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($width, 5, 'TICKET DE VENTA', 0, 1);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell($width, 5, 'QUICK T&R, S.L.', 0, 1);
    $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', $direccion), 0, 1);
    $pdf->Cell($width, 5, 'Nro Telefono 933496389', 0, 1);
    $pdf->Ln();
    // DATOS CLIENTE
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($width, 5, 'DATOS DE LA VENTA', 0, 1, 'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell($width/4, 5, 'Producto', 0, 0);
    $pdf->Cell($width/1.5, 5, $n, 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Cantidad', 0, 0);
    $pdf->Cell($width/1.5, 5, $c, 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Precio', 0, 0);
    $pdf->Cell($width/1.5, 5, $p, 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'IVA', 0, 0);
    $pdf->Cell($width/1.5, 5, $i . '%', 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Precio Final', 0, 0);
    $pdf->Cell($width/1.5, 5, $f, 1, 1);
    $pdf->Ln(5);
    $pdf->Cell($width/4, 5, 'Total', 0, 0);
    $pdf->Cell($width/1.5, 5, $t, 1, 1);

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

    // ABRIR PDF
    $pdf->Output('I', null, true);
    
    //---------------END CREAR PDF---------------//
}

function enviarCorreo($id){
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);

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
        crearPDF($id, 1);
        $mail->addAttachment('doc.pdf');
        if($datos["firma"]!=null){
            $mail->AddEmbeddedImage('upload/'.$datos["firma"], 'firma');
        }

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Nueva orden de '.$datos["nombre"].' «'.$datos["servicio"].'»';
        $mail->Body    = '<html><body><h1>'.ucfirst($datos["tipo"]).' # '.$id.' - '.$datos["servicio"].'</h1>
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
            <p>
            Firma
            </p>
            <img src="cid:firma"></body></html>';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function editarEntrada($id){
    $doc="-";$dir="-";$cp="-";
    if(isset($_POST["doc"])) $doc = $_POST["doc"];
    if(isset($_POST["direccion"])) $dir = $_POST["direccion"];
    if(isset($_POST["cp"])) $cp = $_POST["cp"];
    $pdo = connect();
    $stmt = $pdo->prepare("UPDATE `info_orden` SET `nombre` = :nombre, `telefono` = :tel, `documento` = :doc, `servicio` = :servicio, `email` = :email, `direccion` = :direccion, `cp` = :cp, `precio` = :precio, `iva` = :iva, `precio-final` = :preciofinal, `desc` = :de, `local` = :loc, `razon` = :razon WHERE `info_orden`.`id` = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nombre', $_POST["nombre"]);
    $stmt->bindParam(':tel', $_POST["tel"]);
    $stmt->bindParam(':doc', $doc);
    $stmt->bindParam(':servicio', $_POST["servicio"]);
    $stmt->bindParam(':email', $_POST["email"]);
    $stmt->bindParam(':direccion', $dir);
    $stmt->bindParam(':cp', $cp);
    $stmt->bindParam(':precio', $_POST["precio"]);
    $stmt->bindParam(':iva', $_POST["iva"]);
    $stmt->bindParam(':preciofinal', $_POST["precio-final"]);
    $stmt->bindParam(':de', $_POST["desc"]);
    $stmt->bindParam(':loc', $_POST["local"]);
    $stmt->bindParam(':razon', $_POST["razon"]);
    try {
        $stmt->execute();
    } catch (PDOException $e){
        echo '<p class="text-light">'.$e->getMessage().'</p>';
    }
    
    header('Location: list.php?id='.$id);
}

function eliminarEntrada($id){
    $pdo = connect();
    $stmt = $pdo->prepare("DELETE FROM info_orden WHERE `id` = :num");
    $stmt->bindParam(":num", $id);
    $stmt->execute();
    header('Location: list.php');
}

function totalVentas(){
    $pdo = connect();
    $stmt = $pdo->prepare("SELECT * FROM `info_orden` WHERE `tipo` = 'venta'");
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    $stmt2 = $pdo->prepare("SELECT round(sum(`precio-final`),2) as total FROM `info_orden` WHERE `tipo` = 'venta'");
    try {
        $stmt2->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    $total=$stmt2->fetch(PDO::FETCH_ASSOC);

    $pdf = new FPDF();
    $width = $pdf->GetPageWidth();
    $pdf->AddPage();
    $pdf->SetMargins(2, 2, 2);
    // LOGO
    $pdf->Cell($width, 5);
    $pdf->Ln(1);
    $pdf->Image('LOGO.png', null, null, $width/1.1);
    $pdf->Ln(5);
    // DATOS
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', 'Producto'), 0, 0);
        $pdf->Cell($width/1.3, 5, iconv('UTF-8', 'windows-1252', $row["desc"]), 1, 1);
        $pdf->Ln(1);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', 'Fecha'), 0, 0);
        $pdf->Cell($width/1.3, 5, $row["fecha"], 1, 1);
        $pdf->Ln(1);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', 'Precio'), 0, 0);
        $pdf->Cell($width/1.3, 5, $row["precio"], 1, 1);
        $pdf->Ln(1);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', 'Precio Final'), 0, 0);
        $pdf->Cell($width/1.3, 5, $row["precio-final"], 1, 1);
        $pdf->Ln(5);
    }
    $pdf->SetFont('Arial','B',8);
    $pdf->Ln(5);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252', 'TOTAL'), 0, 0);
    $pdf->Cell($width/1.3, 5, $total["total"], 1, 1);
    // ABRIR PDF
    $pdf->Output('I', null, true);
}

?>