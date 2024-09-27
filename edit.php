<?php
    session_start();
    // IMPORT FUNCTIONS
    require_once "functions.php";
    if(isset($_POST["editar-factura"])){
        editarEntrada($_GET["id"]);
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
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
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
            <?php
            if(isset($_SESSION["login"])){
                echo '<a href="list.php" class="btn btn-secondary mx-2"><i class="bi bi-columns-gap"></i> Lista</a>';
                echo '<a href="index.php?logout=true" class="btn btn-danger mx-2"><i class="bi bi-box-arrow-in-left"></i> Log Out</a>';
            }
            ?>
        </nav>
        <?php
        if(!isset($_SESSION["login"])){
            include 'login.php';
            exit();
        }
        $datos=selectBD($_GET["id"]);
        ?>
        <!-- FORM -->
        <div class="container my-4">
        <?php
        require_once 'functions.php';
        echo '<form action="" method="POST" class="form-control p-4 bg-dark">
                <a href="index.php?pag=list&id='.$datos["id"].'" class="btn btn-secondary mb-4">Volver</a>
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Nombre" type="text" name="nombre" id="nombre" value="'.$datos["nombre"].'">
                            <label for="nombre">Nombre</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Teléfono" type="tel" name="tel" id="tel" value="'.$datos["telefono"].'">
                            <label for="tel">Teléfono</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="DNI/NIF/NIE" type="text" name="doc" id="doc" value="'.$datos["documento"].'">
                            <label for="doc">DNI/NIF/NIE</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-control" name="servicio" id="servicio" required>
                                <option value="'.$datos["servicio"].'">Actual: '.$datos["servicio"].'</option>
                                <option value="Reparación Móvil">Reparación Móvil</option>
                                <option value="Reparación Ordenador">Reparación Ordenador</option>
                                <option value="Reparación Consola">Reparación Consola</option>
                                <option value="Reparación Tablet">Reparación Tablet</option>
                                <option value="Mantenimiento Otros">Mantenimiento Otros</option>
                                <option value="Servicio Desarrollo Web">Servicio Desarrollo Web</option>
                            </select>
                            <label for="servicio">Servicio</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Email" type="text" name="email" id="email" value="'.$datos["email"].'">
                            <label for="email">Email</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-9 mb-3">
                        <div class="form-floating">
                            <input type="text" name="direccion" id="direccion" placeholder="Dirección" value="'.$datos["direccion"].'" class="form-control">
                            <label for="direccion">Dirección</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating">
                            <input type="text" name="cp" id="cp" placeholder="Código Postal" value="'.$datos["cp"].'" class="form-control">
                            <label for="cp">Código Postal</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <select class="form-control form-select" name="local" id="local">
                                <option value="'.$datos["local"].'" selected>Actual: '.$datos["local"].'</option>
                                <option class="text-bg-secondary" value="Barcelona">Barcelona</option>
                                <option class="text-bg-success" value="Mataró">Mataró</option>
                            </select>
                            <label for="local">Local</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                        <select class="form-control" name="razon" id="razon">
                                <option value="'.$datos["razon"].'" selected>Actual: '.ucfirst($datos["razon"]).'</option>
                                <option value="Sin especificar">-</option>
                                <option value="Marketing/RSS">Marketing/Redes Sociales</option>
                                <option value="Maps">Google/Apple Maps</option>
                                <option value="Flyer">Flyer</option>
                                <option value="Retorno">Retorno de cliente</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <label for="razon">Como nos encontró</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <select class="form-control" name="metodo" id="metodo">
                                <option value="'.$datos["metodo"].'" selected>Actual: '.ucfirst($datos["metodo"]).'</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Efectivo">Efectivo</option>
                            </select>
                            <label for="metodo">Metodo de pago</label>
                        </div>
                    </div>
                </div>
                    <h3 class="display-6 text-light text-center">Producto(s)</h3>';
                    if($datos["tipo"]=="venta"){
                        $prod = explode(";", $datos["desc"]);
                        $prec = explode(";", $datos["preciosVenta"]);
                        $cant = explode(";", $datos["cantidadVenta"]);
                        $i=1;
                        while($i<=count($prod)){
                            $k = $i-1;
                            echo '<div class="row" id="productos">
                                <div class="col-12 mb-3" id="col-input">
                                    <div class="input-group">
                                        <div class="form-floating w-50 p-input">
                                            <input type="text" id="prod'.$i.'" onkeyup="addPrice(this.value,'.$i.')" name="prod'.$i.'" class="form-control" value="'.$prod[$k].'" placeholder="">
                                        </div>
                                        <div class="form-floating w-25">
                                            <input type="number" step="0.01" onblur="findPrecioTotal()" class="form-control" value="'.$prec[$k].'" placeholder="Precio" name="prec'.$i.'" id="prec'.$i.'">
                                            <label for="prec1">Precio</label>
                                        </div>
                                        <div class="form-floating w-25">
                                            <input type="number" onblur="findPrecioTotal()" class="form-control" value="'.$cant[$k].'" placeholder="Cantidad" name="cant'.$i.'" id="cant'.$i.'">
                                            <label for="cant1">Cantidad</label>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            $i++;
                        }
                    } else {
                        echo '<div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <textarea rows="2" style="height:100%;" class="form-control" name="desc" id="desc">'.$datos["desc"].'</textarea>
                                <label for="desc">Descripción del producto</label>
                            </div>
                        </div>';
                    }
                ?>
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onblur="findTotal()" placeholder="Precio" type="number" step="0.01" name="precio" id="precio" value="<?php echo $datos["precio"] ?>" required>
                            <label for="precio">Precio €</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onkeyup="findTotal()" placeholder="Descuento" type="number" step="0.1" value=<?php echo $datos["descuento"] ?> name="descuento" id="descuento">
                            <label for="descuento">Descuento</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onblur="findTotal()" placeholder="Iva 21%" type="number" step="0.1" value=21 name="iva" id="iva" value="<?php echo $datos["iva"] ?>" required>
                            <label for="iva">Iva 21%</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onblur="findPrecio()" placeholder="Precio Final" step="0.01" type="number" name="precio-final" id="precio-final" value="<?php echo $datos["precio-final"] ?>" required>
                            <label for="precio-final">Precio Final €</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php if($datos["tipo"] == "servicio") { ?>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pendiente" id="pendiente1" value=0 <?php if($datos["pendiente"]===0) echo "checked"; ?>>
                            <label class="form-check-label text-light" for="pendiente1">
                                Pendiente
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pendiente" id="pendiente2" value=1 <?php if($datos["pendiente"]===1) echo "checked"; ?>>
                            <label class="form-check-label text-light" for="pendiente2">
                                Terminado
                            </label>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="<?php if($datos["tipo"] == "servicio") {echo"col-10";}else{echo"col-12";} ?>">
                        <input type="submit" name="editar-factura" class="btn btn-primary col-12 mx-auto" value="Guardar Cambios">
                    </div>
                </div>
            </form>
        </div>
        <script type="text/javascript">
            function addPrice(prod, num){
                $.ajax({
                    data: {"id": prod},
                    url: 'ajax_productos.php',
                    dataType: 'json',
                    success: function(data){
                        document.getElementById("prec"+num).value = data[0]["precio_venta"];
                        findPrecioTotal();
                    }
                });
            }
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
        </script>
    </body>
</html>