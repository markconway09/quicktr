<?php
// IMPORT FUNCTIONS
require_once "functions.php";

if(isset($_POST["guardar-servicio"])){
    $id = insertarBDS();
    header('Location: list.php?id='.$id);
}
if(isset($_POST["guardar-venta"])){
    $id = insertarBDV();
    header('Location: list.php?id='.$id);
}
if(isset($_GET["enviar"])) enviarCorreo($_GET["id"]);
if(isset($_GET["servicio"])) {
    crearFServicio($_GET["id"]);
    insertFactura($_GET["id"]);
}
if(isset($_GET["ticketservicio"])) crearPDF($_GET["id"]);
if(isset($_GET["servsimp"])) {
    crearPDF($_GET["id"],1);
    insertFactura($_GET["id"],1);
}
if(isset($_GET["venta"])){
    crearFactura($_GET["id"]);
    insertFactura($_GET["id"]);
}
if(isset($_GET["ticketventa"])) crearTVenta($_GET["id"]);
if(isset($_GET["ventasimp"])) {
    crearTVenta($_GET["id"],1);
    insertFactura($_GET["id"],1);
}
if(isset($_GET["devolucion"])) devolucion($_GET["id"]);
if(isset($_GET["deshacer"])) devolucion($_GET["id"],1);
if(isset($_GET["eliminar"])) eliminarEntrada($_GET["id"]);