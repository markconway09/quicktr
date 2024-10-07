<?php
    // IMPORT FUNCTIONS
    require_once "functions.php";

    session_start();
    if(isset($_POST["login"])){
        $pdo = connect();
        $stmt = $pdo->prepare("SELECT * FROM user WHERE username = :user");
        $stmt->bindParam(':user', $_POST["user"]);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($row["username"])==1){
            $hash = $row["password"];
            $pass = $_POST["pass"];
            $verify = password_verify($pass, $hash);
            if ($verify) { 
                $_SESSION["login"] = $row["admin"]==1?"admin":"user";
                $_SESSION["local"] = $row["local"]!=null?$row["local"]:null;
            } else { 
                echo '<script>alert("Contraseña incorrecta")</script>'; 
            }
        }
    }
    if(isset($_GET["logout"])){
        session_destroy();
        header('Location: index.php');
    }
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
        </style>
    </head>
    <body class="bg-secondary">
        <!-- NAVBAR -->
        <nav class="navbar navbar-light" style="background-color:rgb(43,45,46);">
            <a class="navbar-brand mx-auto" href="index.php">
                <img class="rounded" src="LOGO.png" alt="logo" height="90">
                <span class="badge badge-pill bg-danger">1.86</span>
            </a>
            <?php
            if(isset($_SESSION["login"])){
                echo '<span class="text-light mx-3">'.(!$_SESSION["local"]?"Admin":$_SESSION["local"]).'</span><a href="index.php?logout=true" class="btn btn-danger mx-2"><i class="bi bi-box-arrow-in-left"></i> Log Out</a>';
            }
            ?>
        </nav>
        <?php
        if(!isset($_SESSION["login"])){
            include 'login.php';
            exit();
        }
        ?>
        <div class="container p-2 mx-auto my-4 rounded text-center" style="background-color: rgb(43,45,46);box-shadow: 0px 0px 15px black;">
            <?php
            if(!$_SESSION["local"]){
                ?>
                <button class="btn btn-secondary text-light d-inline dropdown-toggle mx-5" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Admin
            </button>
                <?php
            }
            ?>
            
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="?pag=totalventas">Total Ventas</a>
                <a class="dropdown-item" href="?pag=user-admin">Usuarios</a>
                <a class="dropdown-item" href="?pag=infoClientes">Clientes</a>
            </div>
            <a class="btn btn-primary my-2" href="?pag=form-servicio">Servicio</a>
            <a class="btn btn-primary my-2" href="?pag=form-venta">Venta</a>
            <a class="btn btn-success my-2" target="_blank" href="/almacen">Almacén <i class="bi bi-box-arrow-up-right"></i></a>
            <a class="btn btn-secondary my-2 mx-5" href="?pag=list"><i class="bi bi-columns-gap"></i> Lista</a>
        </div>
        
        <!-- CONTENIDO -->
        <div class="container my-4">
            <?php
                if(isset($_GET["pag"])){
                    include_once $_GET["pag"].'.php';
                } else {
                    include_once 'form-servicio.php';
                }
            ?>
        </div>
        <script type="text/javascript">
            // CÁLCULOS IVA
            function findTotal() {
                var precio = parseFloat(document.getElementById('precio').value);
                var iva = parseFloat(document.getElementById('iva').value);
                var final = parseFloat(document.getElementById('precio-final').value);
                var descuento = parseFloat(document.getElementById('descuento').value);
                let calc = precio - ((precio*descuento)/100);
                calc = calc + (calc*(iva/100));
                document.getElementById('precio-final').value = calc.toFixed(2);
            }
            function findPrecio() {
                var precio = parseFloat(document.getElementById('precio').value);
                var iva = parseFloat(document.getElementById('iva').value);
                var final = parseFloat(document.getElementById('precio-final').value);
                let calc = (final/(100+iva))*100;
                document.getElementById('precio').value = calc.toFixed(2);
            }
            function findPrecioTotal(){
                var productos = document.getElementsByClassName("p-input");

                let total = parseFloat(0);

                var precio = parseFloat(document.getElementById('precio').value);
                if(isNaN(precio)) precio = 0;

                for(let i=1;i<=productos.length;i++){
                    let pre = document.getElementById("prec"+i).value;
                    let can = document.getElementById("cant"+i).value;
                    if(pre=="")pre = 0;
                    if(can=="")can = 0;
                    total += (parseFloat(pre)*can);
                }
                
                if(!isNaN(total)){
                    document.getElementById('precio').value = total.toFixed(2);
                }
                findTotal();
            }

            var i = 1;
            function addProd(){
                var prod = document.getElementById("venta-desc");
                var clone = document.getElementById("input-prod").cloneNode(true);
                i++;
                clone.innerHTML='<div class="form-floating w-50">'+
                                    '<input type="text" class="form-control" placeholder="Descripción" name="prod'+i+'" id="prod'+i+'">'+
                                    '<label for="prod'+i+'">Descripción del producto</label>'+
                                '</div>'+
                                '<div class="form-floating w-25">'+
                                    '<input type="number" step="0.01" onblur="findPrecioTotal()" class="form-control" placeholder="Precio" name="prec'+i+'" id="prec'+i+'">'+
                                    '<label for="prec'+i+'">Precio</label>'+
                                '</div>'+
                                '<div class="form-floating w-25">'+
                                    '<input type="number" onblur="findPrecioTotal()" class="form-control" placeholder="Cantidad" value=1 name="cant'+i+'" id="cant'+i+'">'+
                                    '<label for="cant'+i+'">Cantidad</label>'+
                                '</div>';
                prod.appendChild(clone);
            }
        </script>
    </body>
</html>