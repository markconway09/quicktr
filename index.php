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
                $_SESSION["login"]="login";
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
        <style>
        .kbw-signature { width: 300px; height: 200px; }
        </style>
        <!-- JQUERY -->
        <script src="jquery-3.7.1.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <style>
            body{
                font-family: "comfortaa";
            }
        </style>
    </head>
    <body class="bg-secondary" onload="formChange()">
        <!-- NAVBAR -->
        <nav class="navbar navbar-light" style="background-color:rgb(43,45,46);">
            <a class="navbar-brand mx-auto" href="https://quicktr.com/" target="_blank">
                <img class="rounded" src="LOGO.png" alt="logo" height="90">
            </a>
            <a href="venta.php" class="btn btn-success">Ticket Venta</a>
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
            <form action="execute.php" target="_blank" method="POST" class="form-control p-4 bg-dark">
                <div class="row mb-5">
            <span class="text-center mb-2 text-white">¡Importante recargar página ANTES de introducir datos por si se ha expirado la sesión!</span>
                    <select class="form-control-lg col-12 col-md-4 text-center mx-auto" onchange="formChange()" name="tipo" id="tipo">
                            <option value="servicio" selected>ORDEN DE SERVICIO</option>
                            <option value="venta">FACTURA DE VENTA</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Nombre" type="text" name="nombre" id="nombre" required>
                            <label for="nombre">Nombre</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="input-group">
                            <div class="form-floating">
                            <?php
                            include 'countrycodes.php';
                            ?>
                            </div>
                            
                            <div class="form-floating">
                                <input class="form-control" placeholder="Teléfono" type="tel" name="tel" id="tel" required>
                                <label for="tel">Teléfono</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="DNI/NIF/NIE" type="text" name="doc" id="doc">
                            <label for="doc">DNI/NIF/NIE</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="servicio-change" class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-control" name="servicio" id="servicio" required>
                                <option value="Reparación Móvil" selected>Reparación Móvil</option>
                                <option value="Reparación Ordenador">Reparación Ordenador</option>
                                <option value="Reparación Consola">Reparación Consola</option>
                                <option value="Reparación Tablet">Reparación Tablet</option>
                                <option value="Mantenimiento Otros">Mantenimiento Otros</option>
                                <option value="Servicio Desarrollo Web">Servicio Desarrollo Web</option>
                            </select>
                            <label for="servicio">Servicio</label>
                        </div>
                    </div>
                    <div id="email-change" class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Email" type="email" name="email" id="email" required>
                            <label for="email">Email</label>
                        </div>
                    </div>
                </div>
                <div class="row" id="venta-dir">
                    <div class="col-12 col-md-9 mb-3">
                        <div class="form-floating">
                            <input type="text" name="direccion" id="direccion" placeholder="Dirección" class="form-control">
                            <label for="direccion">Dirección</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating">
                            <input type="text" name="cp" id="cp" placeholder="Código Postal" class="form-control">
                            <label for="cp">Código Postal</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="form-floating">
                            <select onchange="bgChange()" class="form-control form-select" name="local" id="local" required>
                                <option class="text-primary" value="Barcelona">Barcelona</option>
                                <option class="text-success" value="Mataró">Mataró</option>
                                <option class="text-warning" value="Madrid">Madrid</option>
                            </select>
                            <label for="local">Local</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="serv-razon" class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                        <select class="form-control" name="razon" id="razon" required>
                                <option value="sin especificar" selected>-</option>
                                <option value="web">Página Web</option>
                                <option value="maps">Google/Apple Maps</option>
                                <option value="flyer">Flyer</option>
                                <option value="retorno">Retorno de cliente</option>
                                <option value="otro">Otro</option>
                            </select>
                            <label for="razon">Como nos encontró</label>
                        </div>
                    </div>
                    <div id="serv-dept" class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-control" name="dept" id="dept" required>
                                <option value="hardware">Hardware</option>
                                <option value="web">Web</option>
                                <option value="redes">Redes</option>
                            </select>
                            <label for="dept">Departamento</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="serv-motivo" class="col-12 mb-3">
                        <div class="form-floating">
                            <textarea rows="5" style="height:100%;" class="form-control" placeholder="Descripción" name="motivo" id="motivo"></textarea>
                            <label for="motivo">Descripción</label>
                        </div>
                    </div>
                    <div id="venta-desc" class="col-12 d-none mb-3">
                        <div class="row">
                            <div class="col-8 mb-1">
                                <div class="form-floating">
                                    <input type="text" class="form-control" placeholder="Descripción" name="prod1" id="prod1">
                                    <label for="prod1">Descripción del producto</label>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" onblur="findPrecioTotal()" class="form-control" placeholder="Precio" name="prec1" id="prec1">
                                    <label for="prec1">Precio</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8 mb-1">
                                <div class="form-floating">
                                    <input type="text" class="form-control" placeholder="Descripción" name="prod2" id="prod2">
                                    <label for="prod2">Descripción del producto</label>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" onblur="findPrecioTotal()" class="form-control" placeholder="Precio" name="prec2" id="prec2">
                                    <label for="prec2">Precio</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8 mb-1">
                                <div class="form-floating">
                                    <input type="text" class="form-control" placeholder="Descripción" name="prod3" id="prod3">
                                    <label for="prod3">Descripción del producto</label>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" onblur="findPrecioTotal()" class="form-control" placeholder="Precio" name="prec3" id="prec3">
                                    <label for="prec3">Precio</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8 mb-1">
                                <div class="form-floating">
                                    <input type="text" class="form-control" placeholder="Descripción" name="prod4" id="prod4">
                                    <label for="prod4">Descripción del producto</label>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" onblur="findPrecioTotal()" class="form-control" placeholder="Precio" name="prec4" id="prec4">
                                    <label for="prec4">Precio</label>
                                </div>
                            </div>
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
                    <input type="submit" name="guardar" class="btn btn-success col-5 mx-auto" value="Guardar">
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
            function findPrecioTotal(){
                var p1 = document.getElementById('prec1').value;
                var p2 = document.getElementById('prec2').value;
                var p3 = document.getElementById('prec3').value;
                var p4 = document.getElementById('prec4').value;
                var precio = parseFloat(document.getElementById('precio').value);
                if(isNaN(precio)) precio = 0;
                if(p1=="") p1 = 0;
                if(p2=="") p2 = 0;
                if(p3=="") p3 = 0;
                if(p4=="") p4 = 0;
                let total = parseFloat(p1)+parseFloat(p2)+parseFloat(p3)+parseFloat(p4);
                if(!isNaN(total)){
                    document.getElementById('precio').value = total.toFixed(2);
                }
                findTotal();
            }
            // CAMBIO DE FORMULARIO
            function formChange() {
                var t = document.getElementById('tipo').value;
                if(t == 'servicio'){
                    $('#serv-motivo').removeClass('d-none');
                    $('#serv-razon').removeClass('col-md-12');
                    $('#serv-razon').addClass('col-md-6');
                    $('#email-change').removeClass('col-md-12');
                    $('#email-change').addClass('col-md-6');
                    $('#servicio-change').removeClass('d-none');
                    $('#serv-dept').removeClass('d-none');
                    $('#venta-desc').addClass('d-none');
                    $('#venta-dir').addClass('d-none');
                } else if(t == 'venta'){
                    $('#serv-motivo').addClass('d-none');
                    $('#serv-razon').removeClass('col-md-6');
                    $('#serv-razon').addClass('col-md-12');
                    $('#email-change').removeClass('col-md-6');
                    $('#email-change').addClass('col-md-12');
                    $('#servicio-change').addClass('d-none');
                    $('#serv-dept').addClass('d-none');
                    $('#venta-desc').removeClass('d-none');
                    $('#venta-dir').removeClass('d-none');
                }
            }

            // CAMBIO COLOR SELECT LOCAL
            $(document).ready(bgChange());
            function bgChange() {
                var sel = document.getElementById("local");
                var ind = sel.selectedIndex;
                var opt = sel.options;

                switch(opt[ind].value){
                    case "Barcelona":
                        $('#local').addClass("text-primary");
                        $('#local').removeClass("text-success");
                        $('#local').removeClass("text-warning");
                        break;
                    case "Mataró":
                        $('#local').removeClass("text-primary");
                        $('#local').addClass("text-success");
                        $('#local').removeClass("text-warning");
                        break;
                    case "Badalona":
                        $('#local').removeClass("text-primary");
                        $('#local').removeClass("text-success");
                        $('#local').addClass("text-warning");
                        break;
                }

            }
        </script>
    </body>
</html>