<?php
// IMPORT FUNCTIONS
require_once "functions.php";

// GUARDAR SERVICIO
if(isset($_POST["guardar-servicio"])){
    $id = insertarBDS();
    enviarCorreo($id);
    header('Location: index.php?pag=list&id='.$id);
}

// GUARDAR VENTA
if(isset($_POST["guardar-venta"])){
    $id = insertarBDV();
    enviarCorreo($id);
    header('Location: index.php?pag=list&id='.$id);
}
// FACTURA SERVICIO
if(isset($_GET["servicio"])) {
    crearFServicio($_GET["id"]);
    insertFactura($_GET["id"]);
}
// TICKET SERVICIO
if(isset($_GET["ticketservicio"])) crearPDF($_GET["id"]);
// FACTURA SIMPLIFICADA SERVICIO
if(isset($_GET["servsimp"])) {
    crearPDF($_GET["id"],1);
    insertFactura($_GET["id"],1);
}
// FACTURA VENTA
if(isset($_GET["venta"])){
    crearFactura($_GET["id"]);
    insertFactura($_GET["id"]);
}
// TICKET VENTA
if(isset($_GET["ticketventa"])) crearTVenta($_GET["id"]);
// FACTURA SIMPLIFICADA VENTA
if(isset($_GET["ventasimp"])) {
    crearTVenta($_GET["id"],1);
    insertFactura($_GET["id"],1);
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
// COBRAR (PENDIENTE->TERMINADO / TERMINADO->PENDIENTE)
if(isset($_GET["cobrar"])) cobrarServicio($_GET["id"],1);
if(isset($_GET["desCobrar"])) cobrarServicio($_GET["id"],0);