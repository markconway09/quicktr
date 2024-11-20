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
    $stmt->bindParam(':archivo', $image_id);
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
}

function insertarBDS($garantia=0) {
    // Initialize variables with default values
    $servicio = "No especificado";
    $pV = null;
    $cV = null;
    $doc = "No especificado";
    $local = "No especificado";
    $dir = "No especificado";
    $cp = "No especificado";
    $email = "No especificado";
    $tel = "";
    $nombre = "No especificado";
    $ins_d = "";
    $ins_p = 0;
    $metodo = "";
    $disp = "No especificado";
    $precio = 0;
    $descuento = 0;
    $iva = 0;
    $preciofinal = 0;
    $razon = "No especificado";
    $dept = "No especificado";
    $desc = "Sin descripción";

    // Handle phone number with country code
    if (!empty($_POST["countryCode"])) {
        $cc = ($_POST["countryCode"][0] !== '+') ? '+' . $_POST["countryCode"] : $_POST["countryCode"];
        if (isset($_POST["tel"])) $tel = $cc . $_POST["tel"];
    } else {
        if (isset($_POST["tel"])) $tel = $_POST["tel"];
    }

    // Handle other POST variables
    if (!empty($_POST["servicio"]) && isset($_POST["servicio2"])) $servicio = $_POST["servicio"] . ": " . $_POST["servicio2"];
    $doc = $_POST["doc"] ?? $doc;
    $local = $_POST["local"] ?? $local;
    $razon = $_POST["razon"] ?? $razon;
    $dept = $_POST["dept"] ?? $dept;
    $desc = $_POST["motivo"] ?? $desc;
    $nombre = $_POST["nombre"] ?? $nombre;
    $dir = $_POST["direccion"] ?? $dir;
    $cp = $_POST["cp"] ?? $cp;
    $email = $_POST["email"] ?? $email;
    $ins_d = $_POST["insumo_desc"] ?? $ins_d;
    $ins_p = $_POST["insumo_precio"] ?? $ins_p;
    $metodo = $_POST["metodo"] ?? $metodo;
    $disp = $_POST["dispositivo"] ?? $disp;
    $precio = $_POST["precio"] ?? $precio;
    $descuento = $_POST["descuento"] ?? $descuento;
    $iva = $_POST["iva"] ?? $iva;
    $preciofinal = $_POST["precio-final"] ?? $preciofinal;

    // Prepare database connection and insert query
    $pdo = connect();
    $stmt = $pdo->prepare("INSERT INTO info_orden 
        VALUES (null, :nom, :tel, :doc, :ser, :email, :direccion, :cp, :preciosV,
                :cantV, :precio, :descuento, :iva, :final, :ins_d, :ins_p, null, :disp,
                :descr, null, :loc, :fecha, null, :garantia, 0, :razon, :dept)");
    
    // Bind parameters
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
    $stmt->bindParam(':disp', $disp);
    $stmt->bindParam(':descr', $desc);
    $stmt->bindParam(':loc', $local);
    $date = date('Y-m-d');
    $stmt->bindParam(':fecha', $date);
    $stmt->bindParam(':garantia', $garantia);
    $stmt->bindParam(':razon', $razon);
    $stmt->bindParam(':dept', $dept);

    // Execute the statement and handle any errors
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        error_log($e->getMessage()); // Log error message
        error_log($stmt->queryString); // Log SQL query for debugging
        echo "An error occurred. Please try again later.";
    }

    return $pdo->lastInsertId(); // Return last inserted ID
}

function insertarFotos(){
    $pdo = connect();
    $stmt = $pdo->prepare("UPDATE info_orden SET `desc` = :d WHERE id = :id");
    $stmt->bindParam(':id', $_POST["id"]);
    $stmt->bindParam(':d', $_POST["desc"]);
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    $targetDir = "fotos/";
    foreach ($_FILES['images']['name'] as $key => $name) {
        $fileTmpPath = $_FILES['images']['tmp_name'][$key];
        $fileName = basename($name);
        $targetFilePath = $targetDir . $fileName;
        // Move the uploaded file to the target directory
        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
            $stmt = $pdo->prepare("INSERT INTO foto (id_orden, archivo) VALUES (:id, :archivo)");
            $stmt->bindParam(':id', $_POST["id"]);
            $stmt->bindParam(':archivo', $fileName);
            try {
                $stmt->execute();
            } catch(PDOException $e){
                echo $e->getMessage();
            }
        } else {
            echo "Error uploading file: " . $name;
        }
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
    if($datos["local"]=="Barcelona") $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 606 46 59 79'), 0, 1);
    if($datos["local"]=="Mataró") $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 612 25 96 31'), 0, 1);
    if($datos["local"]=="Barcelona") $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Telefono: 933 496 389'), 0, 1);
    $pdf->Ln();
    // DATOS CLIENTE
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($width, 5, 'DATOS DEL CLIENTE', 0, 1, 'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell($width/4, 5, 'Nombre', 0, 0);
    $pdf->Cell($width/1.5, 5, iconv('UTF-8', 'windows-1252', $datos["nombre"]), 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, iconv('UTF-8', 'windows-1252', 'Teléfono'), 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["telefono"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Dni/NIE', 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["documento"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Email', 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["email"], 1, 1);
    /*
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, iconv('UTF-8', 'windows-1252', 'Dirección'), 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["direccion"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, iconv('UTF-8', 'windows-1252', 'C. Postal'), 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["cp"], 1, 1);*/

    $pdf->Ln(5);

    $pdf->Cell($width/4, 5, 'Servicio', 0, 0);
    $pdf->MultiCell($width/1.5, 5, iconv('UTF-8', 'windows-1252', $datos["servicio"]), 1, 1);
    $pdf->Ln(1);
    $motivo = iconv('UTF-8', 'windows-1252', $datos["desc"]);
    $pdf->Cell($width/4, 5, iconv('UTF-8', 'windows-1252', 'Descripción'), 0, 0);
    $pdf->MultiCell($width/1.5, 5, $motivo, 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Dispositivo', 0, 0);
    $pdf->Cell($width/1.5, 5, $datos["nombre_dispositivo"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width/4, 5, 'Precio Aprox.', 0, 0);
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
        $pdf->Cell($width/4, 5, 'Precio Sgdo.', 0, 0);
        $pdf->Cell($width/1.5, 5, iconv('UTF-8', 'windows-1252', $datos["precio-final"]." €"), 1, 1);
    }
    $pdf->Ln(1);
    $pdf->MultiCell($width, 5, iconv('UTF-8', 'windows-1252', 'Método de pago: '.$datos["metodo"]), 0, 0);
    $pdf->Ln(1);
    if(!empty($datos["firma"]) && file_exists("upload/".$datos["firma"])){
        $pdf->Image('upload/'.$datos["firma"], null, null, 70, 30);
        $pdf->Ln(1);
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

    // TEXTO LEGAL
    $pdf->SetFont('Arial','',6);
    $pdf->SetXY($width*1.1, 10);
    $txt = iconv('UTF-8', 'windows-1252', '
1. TERMINOS Y CONDICIONES GENERALES DE ACEPTACION DE LA ORDEN DE REPARACION Y CUSTODIA DEL TERMINAL: El Cliente, mediante la firma del presente documento (en adelante, orden de reparación), encarga en nombre propio al Centro (según se identifica abajo) la reparación de su dispositivo, con simultánea entrega del mismo. Se hace constar que la reparación será realizada en un plazo estimado que corresponda a la fecha prevista de entrega, arriba indicada. Si el servicio requerido no pudiera ser realizado por el Centro, este lo remitirá a su proveedor (Quick Tech Repair), encargándose por cuenta del cliente la reparación, asumiendo el Centro el correspondiente transporte. El Cliente reconoce y acepta que el Centro no será responsable de eventuales pérdidas o extravío de datos o informaciones contenidas en el dispositivo cuando sean supuestos directamente imputables o de dolo o negligencia; por tanto, se recomienda al cliente realizar la correspondiente copia de seguridad antes de la entrega. La apertura o intento de reparación puede conllevar riesgos, como encender humedad, daños en placa base a nivel de microelectrónica (IS, taps, procesador, etc.), chasis doblados o dañados por golpe, implicando el riesgo de derivar en daños secundarios, incluso de no volver a encender el dispositivo. El cliente es concedor de estos riesgos, y el Centro adoptará todos sus esfuerzos, recursos y la mejor técnica, para minimizar estos riesgos utilizando herramientas de última tecnología.

2. TERMINOS Y CONDICIONES GENERALES DE VENTA: El presente documento recoge en su correspondiente apartado una breve descripción del servicio requerido y el precio imponible a tratar según acuerden las partes. Para analizar la recopilación de dicha información, se muestran todos los datos introducidos al cliente, quien deberá revisarlo antes de suscribir la orden de reparación. El ticket o la factura se emitirán al realizar el correspondiente pago.

3. TÉRMINOS Y CONDICIONES GENERALES DE REPARACIONES: El dispositivo se entrega sin ningún tipo de accesorios, como por ejemplo batería. Para permitir la reparación del dispositivo, se recomienda además eliminar o desactivar los códigos PIN y/o códigos de desbloqueo o bien facilitar dichos códigos al momento de la entrega del dispositivo. El Cliente acepta que, tras la aceptación del dispositivo, el Centro o, en su caso, el proveedor pueda realizar fotografías que revelen el estado real del dispositivo y/o del proceso de reparación, y que en productos clasificados IP67-IP68 o modelos posteriores no será posible en su caso recuperar la capacidad y las funciones submarinas en cuanto a las que hayan sido dañadas por la ruptura causada por el cliente. Las fotografías no se difundirán a terceros, pero podrán ser incorporadas a la correspondiente ficha que acompaña el proceso de reparación realizado.

4. TIPOLOGÍA DE PIEZAS DE RECAMBIO UTILIZADAS Y GARANTÍA POST REPARACIÓN:
    1. El Centro pone a disposición del Cliente justificación documental referente al origen, naturaleza y precio de las piezas de repuesto utilizadas para las reparaciones. De ser solicitada dicha justificación, la misma podrá ser entregada al Cliente. No serán utilizadas piezas de recambio de baja calidad, no conformes, no apropiadas o de calidad inferior al estándar original. Las piezas de recambio OEM son compatibles, de igual calidad y con las mismas características que las de un original.
    2. Los productos objeto de reparación gozarán de la correspondiente garantía de reparación que cubrirá los mismos durante un plazo de tres meses, según detalle indicado en la correspondiente hoja técnica (pantallas, baterías, LCD restaurados, soldaduras, etc.). En cualquier caso, la garantía no cubrirá los defectos comunicados fuera del periodo de garantía. El Centro no reparará ni reemplazará ninguna pieza que haya sido modificada o reparada por terceros. Asimismo, el Centro no se responsabiliza de la avería sobrevenida cuando el fallo se derive de la no aceptación por parte del Cliente de la reparación de averías ocultas previamente comunicadas y cuando la referida falta de aceptación se haga constar en la factura. En general, la garantía no tendrá validez si existen pruebas de uso negligente o mal uso. Dicha garantía está sujeta a lo dispuesto en el artículo 6 del Real Decreto 58/1988, de 29 de enero, sobre Protección de los Derechos del Consumidor en el servicio de reparación de Aparatos de Uso Doméstico, que establece la obligación de garantizar, durante un plazo mínimo de tres meses, las reparaciones o instalaciones efectuadas en cualquier servicio de asistencia técnica. El Cliente deberá conservar el comprobante de reparación (ticket de reparación y/o factura) para realizar posibles reclamaciones sujetas a garantía. En caso de que el Cliente encargue servicios de reparación y/o asistencia en un dispositivo cubierto por la garantía comercial del fabricante, el Centro no asume ningún tipo de responsabilidad respecto a la eventual pérdida de dicha garantía del fabricante, ya que el Cliente está al corriente de que, al solicitar un servicio de reparación o asistencia a una entidad que no se corresponde con el fabricante, la garantía del fabricante puede quedar anulada o reducida, si bien seguirá teniendo la garantía legal dada por nosotros como vendedores. Las piezas del aparato que hayan sido sustituidas, a los efectos del art. 4.3 del Real Decreto 58/1988, podrán ser restituidas al cliente en el caso de que este lo requiera.
    3. En el caso de que el Cliente haya adquirido un producto, será asimismo aplicable la garantía legal prevista en tales supuestos de venta que cubre el producto durante un plazo de tres años. Sin perjuicio de lo anterior, si dicho producto es de segunda mano, el vendedor y el Cliente podrán pactar un plazo menor, que no podrá ser inferior a un año desde la entrega. En caso de reparación, dicha garantía cubrirá también las piezas nuevas, que sean relevantes para el producto reparado, que hayan sido implementadas en sustitución de otras. En cualquier caso, la garantía no cubrirá los defectos comunicados fuera del periodo de garantía. El Centro no reparará ni reemplazará ninguna pieza que haya sido modificada o reparada por terceros.

5. DERECHO DE RECUPERACIÓN: El derecho de recuperación del dispositivo entregado para su reparación prescribirá un año después del momento de la entrega. Transcurrido dicho plazo, el dispositivo podrá ser considerado como abandonado, por lo tanto, el Centro podrá disponer del mismo libremente, pudiendo incluso deshacerse o resetearlo, eliminando cualquier tipo de información y ponerlo a la venta como aparato de segunda mano.

6. LEGISLACIÓN Y COMPETENCIA: Resultará de aplicación el Real Decreto Legislativo 1/2007, de 16 de noviembre, por el que se aprueba el texto refundido de la Ley General para la Defensa de los Consumidores y Usuarios y otras leyes complementarias, así como el Real Decreto 58/1988, de 29 de enero, sobre protección de los derechos del consumidor en el servicio de reparación de aparatos de uso doméstico, en todo lo que dichas normativas establezcan con carácter inderogable a favor de los consumidores y usuarios. En caso de controversias, resultarán competentes los tribunales que correspondan al domicilio del consumidor y usuario.

POLITICA DE PRIVACIDAD: De acuerdo con el Reglamento (UE) 2016/679, de 27 de abril de 2016 del Parlamento Europeo, el titular queda informado y, en caso de que firme en el apósito espacio indicado al final de la presente clausula, presta su consentimiento a la incorporación de sus datos a los cheros, automatizados o no, de la sociedad QUICK T&R, S.L. con sede legal en Calle Puigcerda,
    130 de Barcelona, con CIF: B63667570 y al tratamiento automatizado de los mismos, para las calidades de comercialización de sus productos y servicios, de envío de comunicaciones promocionales, incluidas las comunicaciones electrónicas, a los efectos de lo establecido en los artículos 21 y 22 de la Ley 34/2002, de 11 de julio de Servicios de la Sociedad de la información y de Comercio electrónico, y cuya cumplimentación es necesaria para la aplicación de los puntos y premios correspondientes. Asimismo, queda informado de la posibilidad de ejercer sus derechos de acceso, rectificación, oposición, olvido, limitación del tratamiento y portabilidad en la forma prevista en la legislación vigente, debiendo remitir escrito a la sociedad QUICK T&R, S.L., a la dirección info@quicktr.es. Todo ello, en estricta aplicación de los cánones y requisitos aplicables según la normativa ya referenciada. Los dichos datos personales a los que el Centro tendrá acceso serán aquéllos que el Cliente facilite voluntariamente y su recogida y tratamiento se realizara de conformidad con lo previsto en la LOPD. El Cliente queda informado de su derecho de acceso, rectificación, oposición, olvido, limitación del tratamiento y portabilidad, respecto de sus datos personales en los términos previstos en la Ley, pudiendo ejercitar estos derechos por escrito mediante carta, acompañada de copia del Documento de Identidad, y dirigida al Centro (cuyos datos constan en el correspondiente apanado de este mismo documento).

PROTECCIÓN DE DATOS: QUICK T&R, S.L. es el Responsable del tratamiento de los datos personales del Interesado y le informa de que estos datos serán tratados de conformidad con lo dispuesto en el Reglamento (UE) 2016/679, de 27 de abril (GDPR), y la Ley Orgánica 3/2018, de 5 de diciembre (LOPDGDD). Dicho tratamiento se realizará para mantener una relación comercial (por interés legítimo del responsable, art. 6.1.f GDPR) y envío de comunicaciones de productos o servicios (con el consentimiento del interesado, art. 6.1.a GDPR). Los datos se conservarán durante no más tiempo del necesario para mantener el fin del tratamiento o mientras existan prescripciones legales que dictaminen su custodia. No está previsto comunicar los datos a terceros (salvo obligación legal), y si fuera necesario hacerlo para la ejecución del contrato, se informará previamente al Interesado.
    Se informa al Interesado de que podrá ejercer los derechos de acceso, rectificación, supresión y portabilidad de sus datos, y los de limitación u oposición al tratamiento dirigiéndose a QUICK T&R, S.L...
    Carrer Puigcerdà, 130 - 08019 Barcelona. E-mail: info@quicktr.es, y si considera que el tratamiento de datos personales no se ajusta a la normativa vigente, también tiene derecho a presentar una reclamación ante la Autoridad de control (www.aepd.es).
');
    $pdf->MultiCell(null, 2.75, $txt, null);
    
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
    if($datos["local"]=="Barcelona") $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 606 46 59 79'), 0, 1);
    if($datos["local"]=="Mataró") $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 612 25 96 31'), 0, 1);
    if($datos["local"]=="Barcelona") $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Telefono: 933 496 389'), 0, 1);
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

    $pdf->Cell($width/2, 5, iconv('UTF-8', 'windows-1252', $datos["desc_tecnico"]), 1, 0);
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

function correoReview($id){
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    if(!empty($datos["servicio"])) {
        $ser = explode(": ", $datos["servicio"]);
        $ser1 = $ser[0];
    } else {
        $ser1 = "Servicio";
    }

    // ENVIAR CORREO
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";
    $mail->Encoding = 'base64';

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'mail.quicktr.es';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mail@quicktr.es';
        $mail->Password   = 'Barcelon@2024.';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('info@quicktr.es');
        $mail->addAddress($datos["email"]);

        //Content
        $mail->isHTML(true);
        $mail->Subject = '¡Gracias por confiar en nosotros! «'.ucfirst($ser1).'»';
        $mail->AddEmbeddedImage('estrellas.png', 'estrellas');
        $mail->Body    = '
            <body style="font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; margin: 10; padding: 10;">

                <div style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h1 style="color: #25BED4; font-size: 24px;">¡Gracias por confiar en nosotros!</h1>
                    </div>
                    <div style="font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
                        <p>Estimado/a '.$datos["nombre"].',</p>
                        <p>Esperamos que el servicio de reparación de su dispositivo ('.$datos["nombre_dispositivo"].') haya sido de su satisfacción. Para nosotros, es muy importante conocer tu experiencia y saber si podemos mejorar en algo. Tu opinión es fundamental para poder seguir brindando un excelente servicio.</p>
                        <p>Si pudieras dedicar unos minutos para dejarnos una reseña, te estaríamos muy agradecidos. Solo tienes que hacer clic en el botón de abajo para compartir tu experiencia.</p>
                        <p style="text-align: center;">
                            <img src="cid:estrellas" alt="estrellas" style="width: 180px; height: 100%; text-align:center;"><br>
                            <a href="https://admin.trustindex.io/api/googleWriteReview?place-id=ChIJd8ieUSOjpBIRuTHGH3C64Fs" style="display: inline-block; padding: 10px 20px; background-color: #25BED4; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">Deja tu reseña aquí</a>
                        </p>
                    </div>
                    <div style="text-align: center; font-size: 14px; color: #777;">
                        <p>Gracias por elegirnos. Si tienes alguna pregunta o necesitas asistencia adicional, no dudes en contactarnos.</p>
                        <p>Atentamente, <br> El equipo de Quick TR</p>
                    </div>
                </div>

            </body>
        ';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function enviarCorreo($id){
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    if(!empty($datos["servicio"])) {
        $ser = explode(": ", $datos["servicio"]);
        $ser1 = $ser[0];
        $ser2 = $ser[1];
    } else {
        $ser1 = "Servicio";
        $ser2 = "";
    }

    // ENVIAR CORREO
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";
    $mail->Encoding = 'base64';

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'mail.quicktr.es';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mail@quicktr.es';
        $mail->Password   = 'Barcelon@2024.';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('info@quicktr.es');
        $mail->addAddress('sistemas@dvagroup.es');
        crearPDF($id, 0, 1);
        $mail->addAttachment('doc.pdf');

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Nueva orden de '.$datos["nombre"].' «'.ucfirst($ser1).'»';
        $mail->AddEmbeddedImage('LogoCorreo.png', 'logo_qtr');
        $mail->Body    = '
                <body>
                    <div style="background-color: #f4f4f4; color: #333; margin: 0; max-width: 900px; margin: 20px auto; border: 2px solid #ddd; border-radius: 10px; background-color: #ffffff; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); ">
                        <div style="padding: 30px; border-bottom: 2px solid #007bff;">
                            <img src="cid:logo_qtr" alt="logo" style="width: 240px; height: auto; margin: auto;">
                            <p style="font-weight: bold;">QUICK T&R, S.L.</p>
                            <p>Carrer de València, 235</p>
                            <p>Principal, 1 Eixample</p>
                            <p>08007 Barcelona</p>
                            <p>Teléfono Barcelona: 933 496 389</p>
                            <p>Whatsapp Barcelona: 606 46 59 79</p>
                            <p>Whatsapp Mataró: 612 25 96 31</p>
                            <br>
                            <p><strong>Fecha:</strong> '.$datos["fecha"].'</p>
                        </div>
                        <div>
                            <h1 style="text-align: center; color: #0056b3;">'.ucfirst($ser1).' # '.$id.' - '.$ser2.'</h1>
                        </div>
                        <div style="padding: 30px">
                            <div style="flex: 1; min-width: 200px;">
                            <h2>Detalles del Cliente</h2>
                            <p><strong>Nombre:</strong> '.$datos["nombre"].'</p>
                            <p><strong>Email:</strong> '.$datos["email"].'</p>
                            <p><strong>Teléfono:</strong> <a href="https://wa.me//'.$datos["telefono"].'" target="_blank">'.$datos["telefono"].'<a></a></p>
                            <p><strong>Documento:</strong> '.$datos["documento"].'</p>
                            </div>
                            <div>
                            <h2>Detalles del Servicio</h2>
                            <p><strong>Tipo de servicio:</strong> '.$ser1.'</p>
                            <p><strong>Servicio Reportado:</strong> '.$ser2.'</p>
                            <p><strong>Descripción:</strong> '.$datos["desc"].'</p>
                            </div>
                        </div>
                        <div style="padding: 30px; background-color: #f9f9f9;">  
                            <h2>Costos</h2>
                            <p>Precio: '.$datos["precio"].'€</p>
                            <p>IVA: '.$datos["iva"].'%</p>
                            <p><strong>Precio Total:</strong> '.$datos["precio-final"].'€</p>
                        </div>
                    </div>
                </body>
        ';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function enviarCorreoCliente($id){
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    if(!empty($datos["servicio"])) {
        $ser = explode(": ", $datos["servicio"]);
        $ser1 = $ser[0];
        $ser2 = $ser[1];
    } else {
        $ser1 = "Servicio";
        $ser2 = "";
    }

    // ENVIAR CORREO
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";
    $mail->Encoding = 'base64';

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'mail.quicktr.es';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mail@quicktr.es';
        $mail->Password   = 'Barcelon@2024.';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('info@quicktr.es');
        $mail->addAddress($datos["email"]);
        crearPDF($id, 0, 1);
        $mail->addAttachment('doc.pdf');

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Quick Tech Repair «'.ucfirst($ser1).'»';
        $mail->AddEmbeddedImage('LogoCorreo.png', 'logo_qtr');
        $mail->Body    = '
                <body>
                    <div style="background-color: #f4f4f4; color: #333; margin: 0; max-width: 900px; margin: 20px auto; border: 2px solid #ddd; border-radius: 10px; background-color: #ffffff; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); ">
                        <div style="padding: 30px; border-bottom: 2px solid #007bff;">
                            <img src="cid:logo_qtr" alt="logo" style="width: 240px; height: auto; margin: auto;">
                            <p style="font-weight: bold;">QUICK T&R, S.L.</p>
                            <p>Carrer de València, 235</p>
                            <p>Principal, 1 Eixample</p>
                            <p>08007 Barcelona</p>
                            <p>Teléfono Barcelona: 933 496 389</p>
                            <p>Whatsapp Barcelona: 606 46 59 79</p>
                            <p>Whatsapp Mataró: 612 25 96 31</p>
                            <br>
                            <p><strong>Fecha:</strong> '.$datos["fecha"].'</p>
                        </div>
                        <div>
                            <h1 style="text-align: center; color: #0056b3;">'.ucfirst($ser1).' # '.$id.' - '.$ser2.'</h1>
                        </div> 
                        <div style="padding: 30px">
                            <div style="flex: 1; min-width: 200px;">
                            <h2>Detalles del Cliente</h2>
                            <p><strong>Nombre:</strong> '.$datos["nombre"].'</p>
                            <p><strong>Email:</strong> '.$datos["email"].'</p>
                            <p><strong>Teléfono:</strong> <a href="https://wa.me//'.$datos["telefono"].'" target="_blank">'.$datos["telefono"].'<a></a></p>
                            <p><strong>Documento:</strong> '.$datos["documento"].'</p>
                            </div>
                            <div>
                            <h2>Detalles del Servicio</h2>
                            <p><strong>Tipo de servicio:</strong> '.$ser1.'</p>
                            <p><strong>Servicio Reportado:</strong> '.$ser2.'</p>
                            <p><strong>Descripción:</strong> '.$datos["desc"].'</p>
                            </div>
                        </div>
                        <div style="padding: 30px; background-color: #f9f9f9;">  
                            <h2>Costos</h2>
                            <p>Precio: '.$datos["precio"].'€</p>
                            <p>IVA: '.$datos["iva"].'%</p>
                            <p><strong>Precio Total:</strong> '.$datos["precio-final"].'€</p>
                        </div>
                    </div>
                </body>
        ';

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

    $desc = $_POST["desc"];

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
    if($estado == 4) correoReview($id);
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
        } else {
            $stmt = $pdo->prepare("SELECT *, o.id as id, f.id as fid, d.id as did FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) WHERE MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
            $stmt->bindParam(':loc', $local);
        }
        $stmt->bindParam(':m', $m);
        $stmt->bindParam(':y', $y);
    } else {
        if($local === 0){
            $stmt = $pdo->prepare("SELECT *, o.id as id, f.id as fid, d.id as did FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) WHERE DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y");
        } else {
            $stmt = $pdo->prepare("SELECT *, o.id as id, f.id as fid, d.id as did FROM `info_orden` o left JOIN `factura` f ON (f.id_orden = o.id) left JOIN `devolucion` d ON (o.id = d.id_orden) WHERE DAY(`fecha`) = :d AND MONTH(`fecha`) = :m AND YEAR(`fecha`) = :y AND `local` = :loc");
            $stmt->bindParam(':loc', $local);
        }
        $stmt->bindParam(':d', $d);
        $stmt->bindParam(':m', $m);
        $stmt->bindParam(':y', $y);
    }
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    
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
    $pdf->Cell(17, 5, iconv('UTF-8', 'windows-1252', 'Local'), 1, 0);
    $pdf->Cell(15, 5, iconv('UTF-8', 'windows-1252', 'Método'), 1, 0);
    $pdf->Cell(10, 5, iconv('UTF-8', 'windows-1252', '- %'), 1, 0);
    $pdf->Cell(15, 5, iconv('UTF-8', 'windows-1252', 'IVA'), 1, 0);
    $pdf->Cell(19, 5, iconv('UTF-8', 'windows-1252', 'P.U.'), 1, 0);
    $pdf->Cell(11, 5, iconv('UTF-8', 'windows-1252', 'Cant.'), 1, 0);
    $pdf->Cell(19, 5, iconv('UTF-8', 'windows-1252', 'Base Imp.'), 1, 0);
    $pdf->Cell(19, 5, iconv('UTF-8', 'windows-1252', 'Total'), 1, 0);
    $pdf->Cell(15, 5, iconv('UTF-8', 'windows-1252', 'Estado'), 1, 1);
    $pdf->SetFont('Arial','',8);
    
    $total_total = 0;
    $iva_total = 0;
    $total_efectivo = 0;
    $total_tarjeta = 0;
    $iva_efectivo = 0;
    $iva_tarjeta = 0;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $estado = $row["estado"]==4?"Pagado":"Pendiente";
        $pdf->Cell(28, 5, iconv('UTF-8', 'windows-1252', $row["id"]." - ".(empty($row["fecha_pago"])?"Sin fecha":$row["fecha_pago"])), 1, 0);
        $pdf->Cell($width/2-60, 5, iconv('UTF-8', 'windows-1252', ucfirst($row["servicio"])), 1, 0);
        $pdf->Cell(4, 5, iconv('UTF-8', 'windows-1252', $row["ticket"]?"X":""), 1, 0);
        $pdf->Cell(17, 5, iconv('UTF-8', 'windows-1252', $row["local"]), 1, 0);
        $pdf->Cell(15, 5, iconv('UTF-8', 'windows-1252', $row["metodo"]), 1, 0);
        $pdf->Cell(10, 5, iconv('UTF-8', 'windows-1252', $row["descuento"]."%"), 1, 0);
        $pdf->Cell(15, 5, iconv('UTF-8', 'windows-1252', $row["iva"]."%"), 1, 0);
        $pdf->Cell(19, 5, iconv('UTF-8', 'windows-1252',  $row["precio"]." €"), 1, 0);
        $pdf->Cell(11, 5, iconv('UTF-8', 'windows-1252', 1), 1, 0);
        $pdf->Cell(19, 5, iconv('UTF-8', 'windows-1252',  $row["precio"]." €"), 1, 0);
        $pdf->Cell(19, 5, iconv('UTF-8', 'windows-1252',  $row["precio-final"]." €"), 1, 0);
        $pdf->Cell(15, 5, iconv('UTF-8', 'windows-1252',  $estado), 1, 1);
        if(!empty($row["did"])){
            $pdf->Cell(28, 5, iconv('UTF-8', 'windows-1252', $row["id"]." - ".$row["fecha"]), 1, 0);
            $pdf->Cell(198.5, 5, iconv('UTF-8', 'windows-1252', "DEVOLUCIÓN"), 1, 0);
            $pdf->Cell(34, 5, iconv('UTF-8', 'windows-1252',  "-".$row["precio-final"]." €"), 1, 1);
        } else if(!empty($row["fecha_pago"])) {
            $final = $row["precio"] - ($row["precio"]/100*$row["descuento"]);
            $iva = round(($final * $row["iva"])/100, 2);
            $iva_total+=$iva;
            $total_total+=doubleval($final+$iva);
            if($row["metodo"] == "Efectivo") {
                $total_efectivo += doubleval($final);
                $iva_efectivo += $iva;
            }
            if($row["metodo"] == "Tarjeta") {
                $total_tarjeta += doubleval($final);
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
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $total_total." €"), 1, 1);
    //$pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Descuento: "), 1, 0);
    //$pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  ($total_total-$iva_total-$total_total)." €"), 1, 1);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total IVA: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $iva_total." €"), 1, 1);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  ($total_total+$iva_total)." €"), 1, 1);
    
    $pdf->Ln(15);

    $pdf->SetX(10);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "TARJETA"), 0, 0);
    $pdf->SetX(122);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "EFECTIVO"), 0, 1);

    // BASE TARJETA
    $pdf->SetX(10);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total (Base Imp.): "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $total_tarjeta." €"), 1, 0);
    // BASE EFECTIVO
    $pdf->SetX(122);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total (Base Imp.): "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  $total_efectivo." €"), 1, 1);

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
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  ($total_tarjeta+$iva_tarjeta)." €"), 1, 0);
    // TOTAL EFECTIVO
    $pdf->SetX(122);
    $pdf->Cell($width/6, 5, iconv('UTF-8', 'windows-1252',  "Total: "), 1, 0);
    $pdf->Cell($width/5, 5, iconv('UTF-8', 'windows-1252',  ($total_efectivo+$iva_efectivo)." €"), 1, 1);
    
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