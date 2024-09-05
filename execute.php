<?php
// IMPORT FUNCTIONS
require_once "functions.php";

if(isset($_POST["guardar"])){
    $id = insertarBD();
    header('Location: list.php?id='.$id);
}
if(isset($_GET["enviar"])) enviarCorreo($_GET["id"]);
if(isset($_GET["servicio"])) crearPDF($_GET["id"]);
if(isset($_GET["venta"])) crearFactura($_GET["id"]);
if(isset($_GET["edit"])) editarEntrada($_GET["id"]);
if(isset($_GET["eliminar"])) eliminarEntrada($_GET["id"]);
if(isset($_GET["ventas"])) totalVentas();