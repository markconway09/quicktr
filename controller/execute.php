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