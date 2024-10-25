<?php
// IMPORT FUNCTIONS
require_once "functions.php";

// GUARDAR SERVICIO
if(isset($_POST["guardar-servicio"])){
    $id = insertarBDS();
    enviarCorreo($id);
    header('Location: index.php?pag=list&id='.$id);
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
// FACTURA SIMPLIFICADA SERVICIO
if(isset($_GET["servsimp"])) {
    crearPDF($_GET["id"],1);
    insertFactura($_GET["id"],1);
}

// GUARDAR FOTOS
if(isset($_POST["guardar-fotos"])){
    insertarFotos();
    header('Location: index.php?pag=list&id='.$_POST["id"]);
}

// ENVIAR AL CLIENTE
if(isset($_GET["enviar"])) {
    enviarCorreoCliente($_GET["id"]);
    header('Location: index.php?pag=list&id='.$_GET["id"]);
}
// DEVOLUCION
if(isset($_GET["devolucion"])) devolucion($_GET["id"]);
if(isset($_GET["deshacer"])) devolucion($_GET["id"],1);
// ELIMINAR
if(isset($_GET["eliminar"])) eliminarEntrada($_GET["id"]);

// CAMBIAR ESTADO
if(isset($_GET["estado"])){
    if(!isset($_GET["metodo"])){
        cambiarEstado($_GET["id"], $_GET["estado"], $_GET["pag"]);
    } else {
        cambiarEstado($_GET["id"],$_GET["estado"], $_GET["pag"],$_GET["metodo"]);
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
    $stmt = $pdo->prepare("UPDATE `info_orden` SET `insumo_desc` = :insumo_d, `insumo_precio` = :insumo_p, `desc` = :de, `desc_tecnico` = :dt WHERE `info_orden`.`id` = :id");
    $stmt->bindParam(':id', $_GET["id"]);
    $stmt->bindParam(':de', $_POST["desc"]);
    $stmt->bindParam(':dt', $_POST["desc_tecnico"]);
    $stmt->bindParam(':insumo_d', $i_desc);
    $stmt->bindParam(':insumo_p', $i_prec);
    try {
        $stmt->execute();
    } catch (PDOException $e){
        echo '<p class="text-light">'.$e->getMessage().'</p>';
    }
    
    header('Location: index.php?pag=list&id='.$_GET["id"]);
}