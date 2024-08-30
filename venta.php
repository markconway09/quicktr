<?php
    session_start();
    // IMPORT FUNCTIONS
    require_once "functions.php";

    if(isset($_POST["venta"])) crearTVenta();
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
        <!-- SIGNATURE CSS -->
        <link href="css/jquery.signature.css" rel="stylesheet">
        <style>
        .kbw-signature { width: 300px; height: 200px; }
        </style>
        <!-- JQUERY -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <!-- SIGNATURE JS -->
        <script src="js/jquery.signature.js"></script>
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
            <a class="navbar-brand mx-auto" href="https://quicktr.com/" target="_blank">
                <img class="rounded" src="LOGO.png" alt="logo" height="90">
            </a>
            <a href="index.php" class="btn btn-secondary mx-2">Formulario</a>
            <?php
            if(isset($_SESSION["login"])){
                echo '<a href="list.php" class="btn btn-secondary mx-2">Lista</a>';
                echo '<a href="index.php?logout=true" class="btn btn-danger mx-2">Log Out</a>';
            }
            ?>
        </nav>
        <?php
        if(!isset($_SESSION["login"])){
            include 'login.php';
            exit();
        }
        ?>
        <!-- FORM -->
        <div class="container my-4">
            <form action="" target="_blank" method="POST" class="form-control p-4 bg-dark">
                <div class="row">
                    <div class="col-12 col-md-9 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Nombre" type="text" name="nombre" id="nombre" required>
                            <label for="nombre">Nombre del producto</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Cantidad" type="number" name="cant" id="cant" value=1>
                            <label for="cant">Cantidad</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onblur="findTotal()" placeholder="Precio" type="number" step="0.01" name="precio" id="precio" required>
                            <label for="precio">Precio €</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onblur="findTotal()" placeholder="Iva 21%" type="number" step="0.1" value=21 name="iva" id="iva" required>
                            <label for="iva">Iva 21%</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onblur="findPrecio()" placeholder="Precio Final" step="0.01" type="number" name="precio-final" id="precio-final" required>
                            <label for="precio-final">Precio Final €</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="form-floating">
                            <select onchange="bgChange()" class="form-control form-select" name="local" id="local" required>
                                <option class="text-bg-secondary" value="Barcelona">Barcelona</option>
                                <option class="text-bg-success" value="Mataró">Mataró</option>
                                <option class="text-bg-warning" value="Badalona">Badalona</option>
                            </select>
                            <label for="local">Local</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" name="venta" class="btn btn-primary col-11 mx-auto" value="Crear ticket de venta">
                </div>
            </form>
        </div>
        
        <script type="text/javascript">
            // CÁLCULOS IVA
            function findTotal() {
                var precio = parseFloat(document.getElementById('precio').value);
                var iva = parseFloat(document.getElementById('iva').value);
                var final = parseFloat(document.getElementById('precio-final').value);
                let calc = precio + (precio*(iva/100));
                document.getElementById('precio-final').value = calc.toFixed(2);
            }
            function findPrecio() {
                var precio = parseFloat(document.getElementById('precio').value);
                var iva = parseFloat(document.getElementById('iva').value);
                var final = parseFloat(document.getElementById('precio-final').value);
                let calc = (final/(100+iva))*100;
                document.getElementById('precio').value = calc.toFixed(2);
            }
        </script>
    </body>
</html>