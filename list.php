<?php
    session_start();
    if(!isset($_SESSION["login"])){
        header('Location: index.php');
    }

    // IMPORT FUNCTIONS
    require_once "functions.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="favicon.ico"/>
        <title>Orden de reparación</title>
        <!-- GFONTS -->
         <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <!-- BOOTSTRAP -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- JQUERY -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <style>
            body{
                font-family: "comfortaa";
            }
            .material-symbols-outlined {
              font-variation-settings:
              'FILL' 0,
              'wght' 400,
              'GRAD' 0,
              'opsz' 24
            }
        </style>
    </head>
    <body class="bg-secondary">
        <!-- NAVBAR -->
        <nav class="navbar navbar-light" style="background-color:rgb(43,45,46);">
            <a class="navbar-brand mx-auto" href="index.php">
                <img class="rounded" src="LOGO.png" alt="logo" height="90">
            </a>
            <a href="index.php" class="btn btn-secondary mx-2"><i class="bi bi-ui-checks"></i> Formulario</a>
            <?php
            if(isset($_SESSION["login"])){
                echo '<a href="index.php?logout=true" class="btn btn-danger mx-2"><i class="bi bi-box-arrow-in-left"></i> Log Out</a>';
            }
            ?>
        </nav>
        <div class="container border my-4 px-4 py-2 bg-dark rounded">
            <?php
            if(isset($_GET["id"])){
                $id = $_GET["id"];
                echo '<a href="list.php" class="btn btn-secondary mx-2 mt-3"><i class="bi bi-arrow-left"></i> Volver</a>';
                $row = selectBD($id);
                if($row["tipo"]=="venta"){
                    $d = explode(";", $row["desc"]);
                    $p = explode(";", $row["preciosVenta"]);
                    $c = explode(";", $row["cantidadVenta"]);
                    $desc = '<b>Producto(s):</b> ';
                    for($k = 0; $k<count($d);){
                        $desc .= $d[$k] . " (".(isset($p[$k])?$p[$k]."€":"?")." x ".(isset($c[$k])?$c[$k]:"?").")";
                        $k++;
                        if(isset($d[$k])){
                            $desc .= ", ";
                        }
                    }
                } else{
                    $desc = '<b>Descripción:</b> '.$row["desc"];
                    $precios = "";
                    $cant = "";
                }
                if($row["tipo"]=="venta"){
                    echo '<a href="execute.php?ticketventa=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-ticket-detailed"></i> Ticket</a>';
                    echo '<a href="execute.php?venta=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-receipt-cutoff"></i> Factura</a>';
                    echo '<a href="execute.php?ventasimp=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-receipt"></i> Factura Simplificada</a>';
                }else{
                    echo '<a href="execute.php?ticketservicio=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-ticket-detailed"></i> Ticket</a>';
                    echo '<a href="execute.php?servicio=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-receipt-cutoff"></i> Factura</a>';
                    echo '<a href="execute.php?servsimp=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-receipt"></i> Factura Simplificada</a>';
                }
                echo '<a href="execute.php?enviar=1&id='.$id.'" target="_blank" class="btn btn-success mx-2 mt-3"><i class="bi bi-send"></i> Enviar</a>';
                echo '<a href="edit.php?id='.$id.'" class="btn btn-success mx-2 mt-3"><i class="bi bi-pencil-square"></i> Editar</a>';
                if(!empty($row["did"])){
                    echo '<a href="execute.php?deshacer=1&id='.$id.'" class="btn btn-danger mx-2 mt-3"><i class="bi bi-arrow-counterclockwise"></i> Deshacer</a>';
                } else {
                    echo '<a href="execute.php?devolucion=1&id='.$id.'" class="btn btn-danger mx-2 mt-3"><i class="bi bi-arrow-counterclockwise"></i> Devolución</a>';
                }
                if($_SESSION["login"] == "admin"){
                    echo '<button type="button" class="btn btn-danger mx-2 mt-3" data-bs-toggle="modal" data-bs-target="#elimModal"><i class="bi bi-trash"></i> Eliminar</button>';
                }                
                echo '<div class="col-12">
                            <div class="card my-3">
                                <h5 class="card-header py-3">'.ucfirst($row["tipo"]).' # '.$row["id"].'</h5>
                                <div class="card-body">
                                    <p class="card-text"><b>Nombre:</b> '.$row["nombre"].'</p>
                                    <p class="card-text"><b>Documento:</b> '.$row["documento"].'</p>
                                    <p class="card-text"><b>Fecha (d/m/y):</b> '.$row["fecha"].'</p>
                                    <p class="card-text"><b>Servicio:</b> '.$row["servicio"].'</p>
                                    <p class="card-text"><b>Email:</b> '.$row["email"].'</p>
                                    <p class="card-text"><b>Dirección:</b> '.$row["direccion"]." - ".$row["cp"].'</p>
                                    <p class="card-text"><b>Teléfono:</b> <a href="https://wa.me//'.$row["telefono"].'" target="_blank">'.$row["telefono"].'<a></p>
                                    <hr>
                                    <p class="card-text">'.$desc.'</p>
                                    <p class="card-text"><b>Local:</b> '.$row["local"].'</p>
                                    <p class="card-text"><b>Cómo nos encontró:</b> '.$row["razon"].'</p>
                                    <p class="card-text"><b>Departamento:</b> '.$row["dept"].'</p>
                                    ';
                                    echo'
                                </div>
                                <hr> 
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item"><b>Precio:</b> '.$row["precio"].'€ (+ IVA '.$row["iva"].'%) = <b>'.$row["precio-final"].'€</b></li>
                                </ul>
                            </div>
                        </div>';
                echo '<div class="modal fade" id="elimModal" tabindex="-1" aria-labelledby="elimModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="elimModalLabel">Eliminar esta factura? (# '.$row["id"].')</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="GET" action="execute.php">
                                <input type="hidden" name="id" value="'.$row["id"].'">
                                <input class="btn btn-danger" type="submit" name="eliminar" id="eliminar" value="Eliminar">
                                <button type="button" class="btn btn-secondary"data-bs-dismiss="modal">Cancelar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>';
            exit();
            }
            ?>
            <div class="row">
                <div class="col-12 mt-2">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-12 col-md-8">
                                <div class="form-floating">
                                    <input type="text" placeholder="Buscar... (Tipo, Nombre, Servicio, Id...)" name="search" id="search" class="form-control my-2">
                                    <label for="search">Buscar... (Tipo, Nombre, Id...)</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="submit" id="submit" class="btn btn-primary btn-block p-3 my-2"><i class="bi bi-search"></i> Buscar</button>
                                <?php
                                if(isset($_GET["search"])){
                                    echo '<a href="list.php" class="btn btn-secondary btn-block p-3">Quitar filtro</a>';
                                }else{
                                    if($_SESSION["login"]=="admin"){
                                        echo '<a href="index.php?pag=totalventas" class="btn btn-success btn-block p-3 mx-2">Total Ventas</a>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div></i>
            <?php
            if(!isset($_GET["search"])){
                $pdo = connect();
                $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden) ORDER BY i.id DESC");
                $stmt->execute();
            }else{
                $pdo = connect();
                echo '<p class="display-5 text-light">Resultados para <i>\''.$_GET["search"].'\'</i></p>';
                $search = "%".$_GET["search"]."%";
                $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden)
                WHERE `tipo` LIKE :search OR i.id LIKE :search OR `nombre` LIKE :search OR `local` LIKE :search OR `servicio` LIKE :search
                ORDER BY i.id DESC");
                $stmt->bindParam(':search', $search);
                $stmt->execute();
            }
            $i = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                if($i==0) echo '<div class="row">';
                if(strlen($row["desc"])>25){
                    $desc = substr($row["desc"], 0, 25) . '...';
                }else{
                    $desc = $row["desc"];
                }
                $bg;
                switch($row["local"]){
                    case "Barcelona":
                        $bg = "text-bg-secondary";
                        break;
                    case "Mataró":
                        $bg = "bg-success-subtle";
                        break;
                    case "Badalona":
                        $bg = "bg-warning-subtle";
                        break;
                        
                    default:
                        $bg = "bg-light-subtle";
                }
                $dev = "";
                if(!empty($row["did"])) {
                    $bg = "text-bg-dark";
                    $dev = " - <span class='text-danger'>DEVUELTO</span>";
                }
                echo '
                    <div class="col-lg-4 col-12">
                        <div class="card '.$bg.' my-3">
                            <h5 class="card-header py-3">'.ucfirst($row["tipo"]).' # '.$row["id"].'<br>'.$row["local"].$dev.'</h5>
                            <div class="card-body">
                                <p class="card-text"><b>Nombre:</b> '.$row["nombre"].'</p>
                                <p class="card-text"><b>Documento:</b> '.$row["documento"].'</p>
                                <p class="card-text"><b>Fecha (d/m/y):</b> '.$row["fecha"].'</p>
                                <p class="card-text"><b>Servicio:</b> '.$row["servicio"].'</p>
                                <p class="card-text"><b>Email:</b> '.$row["email"].'</p>
                                <p class="card-text"><b>Teléfono:</b> '.$row["telefono"].'</p>
                                <p class="card-text"><b>Descripción:</b> '.$desc.'</p>
                            </div> 
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item '.$bg.'"><b>Precio:</b> '.$row["precio"].'€ (+ IVA '.$row["iva"].'%) = <b>'.$row["precio-final"].'€</b></li>
                                <li class="list-group-item '.$bg.'"><b>Descuento:</b> '.$row["descuento"].'%</b></li>
                            </ul>
                            <div class="card-body">
                                <a href="list.php?id='.$row["id"].'" class="btn btn-primary">Más Info <i class="bi bi-caret-right-fill"></i></a>
                            </div>
                        </div>
                    </div>';
                if($i==2){
                    $i=0;
                    echo '</div>';
                }else{
                    $i++;
                }
            }
            ?>
        </div>
    </body>
</html>