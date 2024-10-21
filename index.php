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
                $_SESSION["login"] = $row["tipo"];
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['valueLocal'])) {
            if($_POST["valueLocal"]=="Todo"){
                $_SESSION['local'] = null;
            } else {
                $_SESSION['local'] = $_POST['valueLocal']; // Update the session variable
            }
        }
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
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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
                font-family: "Montserrat";
            }
            .gallery {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            .gallery img {
                width: 100%;
                height: auto;
                max-width: 200px; /* Adjust as needed */
                cursor: pointer;
            }
        </style>
    </head>
    <body class="bg-secondary">
        <!-- NAVBAR -->
        <nav class="navbar navbar-light" style="background-color:rgb(43,45,46);">
            <a class="navbar-brand mx-auto" href="index.php">
                <img class="rounded" src="LOGO.png" alt="logo" height="90">
                <span class="badge badge-pill bg-danger">1.10.0</span>
            </a>
            <?php
            if(isset($_SESSION["login"])){ ?>
                <span class="text-light mx-3"><?php echo (!$_SESSION["login"]?"":$_SESSION["login"]); ?></span>
                <?php if($_SESSION["login"]=="tecnico"||$_SESSION["login"]=="admin") { ?>
                <select id="sessionSelect" onchange="updateSession(this.value)">
                    <option value="Todo" <?php echo $_SESSION["local"]==null?'selected':''?>>Todo</option>
                    <option value="Barcelona" <?php echo $_SESSION["local"]=="Barcelona"?'selected':''?>>Barcelona</option>
                    <option value="Mataró" <?php echo $_SESSION["local"]=="Mataró"?'selected':''?>>Mataró</option>
                </select>
                <?php } else { echo $_SESSION["local"]; } ?>
                <a href="index.php?logout=true" class="btn btn-danger mx-2"><i class="bi bi-box-arrow-in-left"></i> Log Out</a>';
            <?php
            }
            ?>
        </nav>
        <?php
        if(!isset($_SESSION["login"])){
            include 'login.php';
            exit();
        }
        ?>
        <div class="container mx-auto p-2 rounded my-4 text-center sticky-top" style="background-color: rgb(43,45,46);">
            <form action="index.php" method="get">
                <div class="col-12">
                    <div class="input-group d-flex">
                        <?php if ($_SESSION["login"] == "admin") { ?>
                            <div class="btn-group">
                                <button class="btn btn-secondary text-light dropdown-toggle" style="border-radius:6px 0 0 6px" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-gear-fill"></i> Admin
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" target="_blank" href="/almacen">Almacén</a></li>
                                    <li><a class="dropdown-item" href="?pag=totalventas">Total Ventas</a></li>
                                    <li><a class="dropdown-item" href="?pag=user-admin">Usuarios</a></li>
                                    <li><a class="dropdown-item" href="?pag=infoClientes">Clientes</a></li>
                                    <li><a class="dropdown-item" href="?pag=infoOrdenes">Exportar Ordenes</a></li>
                                </ul>
                            </div>
                        <?php } ?>
                        <button class="btn btn-primary flex-fill" type="submit" name="pag" value="form-servicio">
                            <i class="bi bi-pencil-square"></i> Formulario
                        </button>
                        <!--<a class="btn btn-success flex-fill" target="_blank" href="form-cliente.php?local=<?php echo $_SESSION["local"]; ?>" type="submit" name="pag" value="form-servicio">
                            <i class="bi bi-pencil-square"></i> Formulario Cliente
                        </a>-->
                        <button class="btn btn-secondary flex-fill" type="submit" name="pag" value="list">
                            <i class="bi bi-columns-gap"></i> Lista
                        </button>
                    </div>
                </div>
            </form>
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
            function updateSession(selectedValue) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "index.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send("valueLocal=" + encodeURIComponent(selectedValue));
            }
        </script>
    </body>
</html>