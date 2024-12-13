<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function crearPDF($id, $enviar = 0)
{
    // SERVICIO
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    // DIRECCIÓN
    switch ($datos["local"]) {
        case 'Barcelona':
            $direccion = 'Carrer d\'Entença, 117, Local-1, 08015';
            $id = '0002 - ' . $id;
            break;
        case 'Barcelona Oficina':
            $direccion = 'Carrer de Valencia, 235 P-1, 08007';
            $id = '0002 - ' . $id;
            break;
        case 'Mataró':
            $direccion = 'Ronda O\'Donnell, 14-16, 08302 Mataró, Barcelona';
            $id = '0003 - ' . $id;
            break;
        default:
            $direccion = 'Carrer d\'Entença, 117, Local-1, 08015';
            break;
    }

    //---------------CREAR PDF---------------//

    $pdf = new FPDF();
    $width = $pdf->GetPageWidth() / 3;
    $pdf->AddPage();
    $pdf->SetMargins(2, 2, 2);
    // LOGO
    $pdf->Cell($width, 5);
    $pdf->Ln(1);
    $pdf->Image('../LOGO.png', null, null, $width);
    $pdf->Ln(1);
    // DATOS QTR
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($width / 3, 5, 'NIF: B19359082');
    $pdf->SetFont('Arial', '', 8);
    $fecha = empty($datos["fecha_pago"]) ? date('d/m/Y') : $datos["fecha_pago"];
    $pdf->Cell($width / 3, 5, 'Fecha: ' . $fecha);
    $pdf->Ln();
    $pdf->Cell($width, 5, 'QUICK T&R, S.L.');
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 8);
    if (isset($datos["did"])) {
        $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'DEVOLUCIÓN # ' . $id), 0, 1);
    } else {
        $pdf->Cell($width, 5, 'TICKET DE SERVICIO # ' . $id, 0, 1);
    }
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', $datos["local"]), 0, 1);
    $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', $direccion), 0, 1);
    if ($datos["local"] == "Barcelona") {
        $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 650 01 04 38'), 0, 1);
        $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Telefono: 934 960 016'), 0, 1);
    }
    if ($datos["local"] == "Barcelona Oficina") {
        $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 606 46 59 79'), 0, 1);
        $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Telefono: 933 496 389'), 0, 1);
    }
    if ($datos["local"] == "Mataró") $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 612 25 96 31'), 0, 1);
    $pdf->Ln();
    // DATOS CLIENTE
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($width, 5, 'DATOS DEL CLIENTE', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($width / 4, 5, 'Nombre', 0, 0);
    $pdf->Cell($width / 1.5, 5, iconv('UTF-8', 'windows-1252', $datos["nombre"]), 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width / 4, 5, iconv('UTF-8', 'windows-1252', 'Teléfono'), 0, 0);
    $pdf->Cell($width / 1.5, 5, $datos["telefono"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width / 4, 5, 'Dni/NIE', 0, 0);
    $pdf->Cell($width / 1.5, 5, $datos["documento"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width / 4, 5, 'Email', 0, 0);
    $pdf->Cell($width / 1.5, 5, $datos["email"], 1, 1);

    $pdf->Ln(5);

    $pdf->Cell($width / 4, 5, 'Servicio', 0, 0);
    $pdf->MultiCell($width / 1.5, 5, iconv('UTF-8', 'windows-1252', $datos["servicio"]), 1, 1);
    $pdf->Ln(1);
    $motivo = iconv('UTF-8', 'windows-1252', $datos["desc"]);
    $pdf->Cell($width / 4, 5, iconv('UTF-8', 'windows-1252', 'Descripción'), 0, 0);
    $pdf->MultiCell($width / 1.5, 5, $motivo, 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width / 4, 5, 'Dispositivo', 0, 0);
    $pdf->Cell($width / 1.5, 5, $datos["nombre_dispositivo"], 1, 1);
    $pdf->Ln(1);
    $pdf->Cell($width / 4, 5, 'Precio Aprox.', 0, 0);
    $pdf->Cell($width / 1.5, 5, iconv('UTF-8', 'windows-1252', $datos["precio"] . " €"), 1, 1);
    $pdf->Ln(1);
    if ($datos["descuento"] > 0) {
        $pdf->Cell($width / 4, 5, 'Descuento', 0, 0);
        $pdf->Cell($width / 1.5, 5, $datos["descuento"] . '%', 1, 1);
        $pdf->Ln(1);
    }
    $pdf->Cell($width / 4, 5, 'IVA', 0, 0);
    $pdf->Cell($width / 1.5, 5, $datos["iva"] . '%', 1, 1);
    $pdf->Ln(1);
    if (isset($datos["did"])) {
        $pdf->Cell($width / 4, 5, iconv('UTF-8', 'windows-1252', 'Devolución'), 0, 0);
        $pdf->Cell($width / 1.5, 5, iconv('UTF-8', 'windows-1252', "-" . $datos["precio-final"] . " €"), 1, 1);
    } else {
        $pdf->Cell($width / 4, 5, 'Precio Sgdo.', 0, 0);
        $pdf->Cell($width / 1.5, 5, iconv('UTF-8', 'windows-1252', $datos["precio-final"] . " €"), 1, 1);
    }
    $pdf->Ln(1);
    $pdf->MultiCell($width, 5, iconv('UTF-8', 'windows-1252', 'Método de pago: ' . $datos["metodo"]), 0, 0);
    $pdf->Ln(1);
    if (!empty($datos["firma"]) && file_exists("../firmas/" . $datos["firma"])) {
        $pdf->Image('../firmas/' . $datos["firma"], null, null, 70, 30);
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
    $pdf->SetFont('Arial', '', 6);
    $pdf->SetXY($width * 1.1, 10);
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
    if ($enviar != 0) {
        $pdf->Output('F', 'temp/doc.pdf', true);
    } else {
        // ABRIR PDF
        $pdf->Output('I', null, true);
    }
}

function crearFServicio($id, $enviar = 0)
{
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    // DIRECCIÓN
    $direccion = 'CL P.J. Maragall Num 1 16, 28020 Madrid, Madrid';
    switch ($datos["local"]) {
        case 'Barcelona':
            $id = '0002 - ' . $id;
            break;
        case 'Mataró':
            $id = '0003 - ' . $id;
            break;
        default:
            $id = '0002 - ' . $id;
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
    $pdf->Image('../LOGO.png', null, null, $width / 3);
    $pdf->Ln(1);
    // DATOS QTR
    $pdf->SetFont('Arial', '', 8);
    $fecha = empty($datos["fecha_pago"]) ? date('d/m/Y') : $datos["fecha_pago"];
    $pdf->Cell($width / 2, 5, 'Fecha: ' . $fecha);
    $pdf->Ln();
    $pdf->Cell($width / 2, 5, iconv('UTF-8', 'windows-1252', $datos["local"]));
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 8);
    if (isset($datos["did"])) {
        $pdf->Cell($width / 2, 5, iconv('UTF-8', 'windows-1252', 'DEVOLUCIÓN # ' . $id), 0, 1);
    } else {
        $pdf->Cell($width / 2, 5, 'FACTURA DE VENTA # ' . $id, 0, 1);
    }
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($width / 2, 5, 'QUICK T&R, S.L.', 0, 1);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($width / 2, 5, 'NIF: B19359082', 0, 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($width / 2, 5, iconv('UTF-8', 'windows-1252', $direccion), 0, 1);
    if ($datos["local"] == "Barcelona") {
        $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 650 01 04 38'), 0, 1);
        $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Telefono: 934 960 016'), 0, 1);
    }
    if ($datos["local"] == "Barcelona Oficina") {
        $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 606 46 59 79'), 0, 1);
        $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Telefono: 933 496 389'), 0, 1);
    }
    if ($datos["local"] == "Mataró") $pdf->Cell($width, 5, iconv('UTF-8', 'windows-1252', 'Nº Whatsapp: 612 25 96 31'), 0, 1);
    $pdf->Ln();

    // DATOS CLIENTE
    $pdf->SetXY($width / 2, 20);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($width / 2, 5, 'DATOS DEL CLIENTE', 0, 1, 'C');
    $pdf->SetXY($width / 2, 25);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($width / 8, 5, 'Nombre', 0, 0);
    $pdf->Cell($width / 4, 5, $datos["nombre"], 1, 1);
    $pdf->Ln(1);
    $pdf->SetXY($width / 2, 30);
    $pdf->Cell($width / 8, 5, iconv('UTF-8', 'windows-1252', 'Teléfono'), 0, 0);
    $pdf->Cell($width / 4, 5, $datos["telefono"], 1, 1);
    $pdf->Ln(1);
    $pdf->SetXY($width / 2, 35);
    $pdf->Cell($width / 8, 5, 'Dni/NIE', 0, 0);
    $pdf->Cell($width / 4, 5, $datos["documento"], 1, 1);
    $pdf->Ln(1);
    $pdf->SetXY($width / 2, 40);
    $pdf->Cell($width / 8, 5, 'Email', 0, 0);
    $pdf->Cell($width / 4, 5, $datos["email"], 1, 1);
    $pdf->Ln(1);
    $pdf->SetXY($width / 2, 45);
    $pdf->Cell($width / 8, 5, iconv('UTF-8', 'windows-1252', 'Dirección'), 0, 0);
    $pdf->Cell($width / 4, 5, $datos["direccion"], 1, 1);
    $pdf->Ln(1);
    $pdf->SetXY($width / 2, 50);
    $pdf->Cell($width / 8, 5, iconv('UTF-8', 'windows-1252', 'C. Postal'), 0, 0);
    $pdf->Cell($width / 4, 5, $datos["cp"], 1, 1);

    $pdf->Ln(30);

    $pdf->Cell($width / 2, 5, iconv('UTF-8', 'windows-1252', 'Descripción'), 1, 0);
    $pdf->Cell($width / 10, 5, iconv('UTF-8', 'windows-1252', 'IVA'), 1, 0);
    $pdf->Cell($width / 10, 5, iconv('UTF-8', 'windows-1252', 'P.U.'), 1, 0);
    $pdf->Cell($width / 10, 5, iconv('UTF-8', 'windows-1252', 'Cant.'), 1, 0);
    $pdf->Cell($width / 10, 5, iconv('UTF-8', 'windows-1252', 'Base Imp.'), 1, 1);

    if ($datos["desc_tecnico"] != "") $pdf->Cell($width / 2, 5, iconv('UTF-8', 'windows-1252', $datos["desc_tecnico"]), 1, 0);
    else $pdf->Cell($width / 2, 5, iconv('UTF-8', 'windows-1252', $datos["desc"]), 1, 0);
    $pdf->Cell($width / 10, 5, iconv('UTF-8', 'windows-1252', $datos["iva"] . "%"), 1, 0);
    $pdf->Cell($width / 10, 5, iconv('UTF-8', 'windows-1252',  $datos["precio"] . " €"), 1, 0);
    $pdf->Cell($width / 10, 5, iconv('UTF-8', 'windows-1252', 1), 1, 0);
    $pdf->Cell($width / 10, 5, iconv('UTF-8', 'windows-1252',  $datos["precio"] . " €"), 1, 1);
    $iva = round(($datos["precio"] * $datos["iva"]) / 100, 2);

    $pdf->Ln(2);

    $pdf->SetX($width / 1.72);
    $pdf->Cell($width / 6, 5, iconv('UTF-8', 'windows-1252',  "Total (Base Imp.): "), 1, 0);
    $pdf->Cell($width / 5, 5, iconv('UTF-8', 'windows-1252',  $datos["precio"] . " €"), 1, 1);
    $pdf->SetX($width / 1.72);
    $pdf->Cell($width / 6, 5, iconv('UTF-8', 'windows-1252',  "Total IVA: "), 1, 0);
    $pdf->Cell($width / 5, 5, iconv('UTF-8', 'windows-1252',  $iva . " €"), 1, 1);
    if ($datos["descuento"] > 0) {
        $pdf->SetX($width / 1.72);
        $pdf->Cell($width / 6, 5, iconv('UTF-8', 'windows-1252',  "Descuento: "), 1, 0);
        $pdf->Cell($width / 5, 5, iconv('UTF-8', 'windows-1252',  $datos["descuento"] . " %"), 1, 1);
    }
    $pdf->SetX($width / 1.72);
    $pdf->Cell($width / 6, 5, iconv('UTF-8', 'windows-1252',  "Total: "), 1, 0);

    if (isset($datos["did"])) {
        $pdf->Cell($width / 5, 5, iconv('UTF-8', 'windows-1252', "-" . $datos["precio-final"] . " €"), 1, 1);
    } else {
        $pdf->Cell($width / 5, 5, iconv('UTF-8', 'windows-1252', ($datos["precio"] + $iva) - ((($datos["precio"] + $iva) * $datos["descuento"]) / 100) . " €"), 1, 1);
    }

    $pdf->Ln(5);
    $metodo = $datos["metodo"];
    $pdf->SetX($width / 1.8);
    $str = iconv('UTF-8', 'windows-1252', 'Método de pago: ' . $metodo);
    $pdf->MultiCell($width / 5, 5, $str, null, 'C');

    // ABRIR PDF
    $pdf->Output('I', null, true);

    //---------------END CREAR PDF---------------//
    if ($enviar != 0) $pdf->Output('F', 'temp/doc.pdf', true);
}

function correoReview($id)
{
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    if (!empty($datos["servicio"])) {
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
        $mail->Subject = '¡Gracias por confiar en nosotros! «' . ucfirst($ser1) . '»';
        $mail->AddEmbeddedImage('temp/estrellas.png', 'estrellas');
        $mail->Body    = '
            <body style="font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; margin: 10; padding: 10;">

                <div style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h1 style="color: #25BED4; font-size: 24px;">¡Gracias por confiar en nosotros!</h1>
                    </div>
                    <div style="font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
                        <p>Estimado/a ' . $datos["nombre"] . ',</p>
                        <p>Esperamos que el servicio de reparación de su dispositivo (' . $datos["nombre_dispositivo"] . ') haya sido de su satisfacción. Para nosotros, es muy importante conocer tu experiencia y saber si podemos mejorar en algo. Tu opinión es fundamental para poder seguir brindando un excelente servicio.</p>
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

function enviarCorreo($id)
{
    //---------------RECOGER DATOS---------------//
    $datos = selectBD($id);
    if (!empty($datos["servicio"])) {
        $ser = explode(": ", $datos["servicio"]);
        $ser1 = $ser[0];
        $ser2 = $ser[1];
    } else {
        $ser1 = "Servicio";
        $ser2 = "";
    }
    if (!is_null($datos["codigo_socio"])) {
        $socio = "<p><strong>CÓDIGO DE SOCIO:</strong> " . $datos["codigo_socio"] . "</p>";
    } else {
        $socio = null;
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
        $mail->addAddress('sistemas@dvagroup.es');
        crearPDF($id, 1);
        $mail->addAttachment('temp/doc.pdf');

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Quick Tech Repair «' . ucfirst($ser1) . '»';
        $mail->AddEmbeddedImage('temp/LogoCorreo.png', 'logo_qtr');
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
                            <p><strong>Fecha:</strong> ' . $datos["fecha"] . '</p>
                        </div>
                        <div>
                            <h1 style="text-align: center; color: #0056b3;">' . ucfirst($ser1) . ' # ' . $id . ' - ' . $ser2 . '</h1>
                        </div> 
                        <div style="padding: 30px">
                            <div style="flex: 1; min-width: 200px;">
                            <h2>Detalles del Cliente</h2>
                            <p><strong>Nombre:</strong> ' . $datos["nombre"] . '</p>
                            <p><strong>Email:</strong> ' . $datos["email"] . '</p>
                            <p><strong>Teléfono:</strong> <a href="https://wa.me//' . $datos["telefono"] . '" target="_blank">' . $datos["telefono"] . '<a></a></p>
                            <p><strong>Documento:</strong> ' . $datos["documento"] . '</p>
                            </div>
                            <div>
                            <h2>Detalles del Servicio</h2>
                            <p><strong>Tipo de servicio:</strong> ' . $ser1 . '</p>
                            <p><strong>Servicio Reportado:</strong> ' . $ser2 . '</p>
                            <p><strong>Descripción:</strong> ' . $datos["desc"] . '</p>
                            ' . $socio . '
                            </div>
                        </div>
                        <div style="padding: 30px; background-color: #f9f9f9;">  
                            <h2>Costos</h2>
                            <p>Precio: ' . $datos["precio"] . '€</p>
                            <p>IVA: ' . $datos["iva"] . '%</p>
                            <p><strong>Precio Total:</strong> ' . $datos["precio-final"] . '€</p>
                        </div>
                    </div>
                </body>
        ';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function editarEntrada($id)
{
    $servicio = $_POST["servicio"] . ": " . $_POST["servicio2"];
    $doc = "";
    $dir = "";
    $cp = "";
    $metodo = "";
    $disp = "";
    if (isset($_POST["doc"])) $doc = $_POST["doc"];
    if (isset($_POST["direccion"])) $dir = $_POST["direccion"];
    if (isset($_POST["cp"])) $cp = $_POST["cp"];
    if (isset($_POST["metodo"])) $metodo = $_POST["metodo"];
    if (isset($_POST["dispositivo"])) $disp = $_POST["dispositivo"];

    $desc = $_POST["motivo"];

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
    } catch (PDOException $e) {
        echo '<p class="text-light">' . $e->getMessage() . '</p>';
    }
}

function devolucion($id, $des = 0)
{
    $pdo = connect();
    if ($des === 0) {
        $stmt = $pdo->prepare("INSERT INTO devolucion VALUES (null, :id)");
        $stmt->bindParam(':id', $id);
    } else {
        $stmt = $pdo->prepare("DELETE FROM `devolucion` WHERE `devolucion`.`id_orden` = :id");
        $stmt->bindParam(':id', $id);
    }
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage() . "<br>";
        $stmt->debugDumpParams();
    }
}

function cambiarEstado($id, $estado, $metodo = null)
{
    $date = $estado == 4 ? date('Y-m-d') : null;
    $pdo = connect();
    $stmt = $pdo->prepare("UPDATE `info_orden` SET `estado` = :estado, `fecha_pago` = :pago, `metodo` = :metodo WHERE `info_orden`.`id` = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':pago', $date);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':metodo', $metodo);
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo '<p class="text-light">' . $e->getMessage() . '</p>';
    }
    if ($estado == 4) correoReview($id);
}

function eliminarEntrada($id)
{
    $pdo = connect();
    $stmt = $pdo->prepare("DELETE FROM info_orden WHERE `id` = :num");
    $stmt->bindParam(":num", $id);
    $stmt->execute();
}