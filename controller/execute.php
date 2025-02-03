<?php
// IMPORT FUNCTIONS
require_once "functions.php";

// GUARDAR SERVICIO
if(isset($_POST["guardar-servicio"])){
    $id = insertarBDS();
    if(isset($_POST["sign"])) subirFirma($id, "../firmas/");
    if(isset($_FILES['images'])) insertarFotos($id);
    enviarCorreo($id);
    header('Location: ../list&id='.$id);
}


// FACTURA SERVICIO
if(isset($_GET["servicio"])) {
    crearFServicio($_GET["id"]);
    insertFactura($_GET["id"],0);
}
// TICKET SERVICIO
if(isset($_GET["ticketservicio"])) {
    crearPDF($_GET["id"]);
    insertFactura($_GET["id"],2);
}

// GUARDAR FOTOS
if(isset($_POST["guardar-fotos"])){
    insertarFotos();
    header('Location: ../list&id='.$_POST["id"]);
}

// ENVIAR AL CLIENTE
if(isset($_GET["enviar"])) {
    enviarCorreo($_GET["id"]);
    header('Location: ../list&id='.$_GET["id"]);
}
// DEVOLUCION
if(isset($_GET["devolucion"])){
    devolucion($_GET["id"]);
    header('Location: ../list&id='.$_GET["id"]);
}
if(isset($_GET["deshacer"])){
    devolucion($_GET["id"],1);
    header('Location: ../list&id='.$_GET["id"]);
}
// ELIMINAR
if(isset($_GET["eliminar"])){
    eliminarEntrada($_GET["id"]);
    header('Location: ../list');
}

// CAMBIAR ESTADO POST (FORM COBRAR)
if(isset($_POST["estado"])){
    if(!isset($_POST["metodo"])){
        cambiarEstado($_POST["id"], $_POST["estado"]);
    } else {
        cambiarEstado($_POST["id"],$_POST["estado"],$_POST["metodo"],$_POST["fecha_pago"]);
    }
    
    if ($_POST["pag"] == 0) {
        header('Location: ../list');
    } else {
        header('Location: ../list&id=' . $_POST["id"]);
    }
}
// CAMBIAR ESTADO GET (FLECHAS)
if(isset($_GET["estado"])){
    if(!isset($_GET["metodo"])){
        cambiarEstado($_GET["id"], $_GET["estado"]);
    } else {
        cambiarEstado($_GET["id"],$_GET["estado"],$_GET["metodo"],$_GET["fecha_pago"]);
    }
    
    if ($_GET["pag"] == 0) {
        header('Location: ../list');
    } else {
        header('Location: ../list&id=' . $_GET["id"]);
    }
}

if(isset($_POST["editar_insumo"])){
    $k = 1;
    $i_desc = "";
    $i_prec = "";
    while(isset($_POST["insumo_desc".$k]) && $_POST["insumo_desc".$k] != ""){
        $i_desc .= $_POST["insumo_desc".$k];
        $i_prec .= $_POST["insumo_precio".$k];
        $k++;
        if(isset($_POST["insumo_desc".$k]) && $_POST["insumo_desc".$k] != ""){
            $i_desc .=";";
            $i_prec .= ";";
        }
    }
    $pdo = connect();
    $stmt = $pdo->prepare("UPDATE `info_orden` SET `insumo_desc` = :insumo_d, `insumo_precio` = :insumo_p, `nombre_dispositivo` = :disp, `desc` = :de, `desc_tecnico` = :dt WHERE `info_orden`.`id` = :id");
    $stmt->bindParam(':id', $_GET["id"]);
    $stmt->bindParam(':disp', $_POST["dispositivo"]);
    $stmt->bindParam(':de', $_POST["desc"]);
    $stmt->bindParam(':dt', $_POST["desc_tecnico"]);
    $stmt->bindParam(':insumo_d', $i_desc);
    $stmt->bindParam(':insumo_p', $i_prec);
    try {
        $stmt->execute();
    } catch (PDOException $e){
        echo '<p class="text-light">'.$e->getMessage().'</p>';
    }
    
    header('Location: ../list&id='.$_GET["id"]);
}