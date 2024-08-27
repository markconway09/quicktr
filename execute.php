<?php
// IMPORT FUNCTIONS
require_once "functions.php";

if(isset($_POST["guardar"])){
    $firma = subirFirma();
    $id = insertarBD($firma);
    header('Location: list.php?id='.$id);
}
if(isset($_GET["enviar"])) enviarCorreo($_GET["id"]);
if(isset($_GET["pdf"])) crearPDF($_GET["id"]);
if(isset($_GET["edit"])) editarEntrada($_GET["id"]);
if(isset($_GET["eliminar"])) eliminarEntrada($_GET["id"]);
if(isset($_GET["ventas"])) totalVentas();