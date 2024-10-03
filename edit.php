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
        $servicio = explode(": ", $datos["servicio"]);
        ?>
        <!-- FORM -->
        <div class="container my-4">
        <form action="" method="POST" class="form-control p-4 bg-dark">
                <a href="index.php?pag=list&id=<?php echo $datos["id"]; ?>" class="btn btn-secondary mb-4">Volver</a>
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Nombre" type="text" name="nombre" id="nombre" value="<?php echo $datos["nombre"];?>">
                            <label for="nombre">Nombre</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Teléfono" type="tel" name="tel" id="tel" value="<?php echo $datos["telefono"];?>">
                            <label for="tel">Teléfono</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="DNI/NIF/NIE" type="text" name="doc" id="doc" value="<?php echo $datos["documento"];?>">
                            <label for="doc">DNI/NIF/NIE</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating">
                            <select class="form-control form-select" name="local" id="local">
                                <option value="<?php echo $datos["local"];?>" selected>Actual: <?php echo $datos["local"];?></option>
                                <?php
                                if(!is_null($_SESSION["local"])) {
                                    ?>
                                    <option value="<?php echo $_SESSION["local"]; ?>"><?php echo $_SESSION["local"]; ?></option>
                                    <?php
                                } else {
                                ?>
                                <option value="Barcelona">Barcelona</option>
                                <option value="Mataró">Mataró</option>
                                <?php
                                }
                                ?>
                            </select>
                            <label for="local">Local</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Email" type="text" name="email" id="email" value="<?php echo $datos["email"];?>">
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating">
                            <select class="form-control form-select" name="metodo" id="metodo">
                                <option value="<?php echo $datos["metodo"];?>" selected>Actual: <?php echo ucfirst($datos["metodo"]);?></option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Efectivo">Efectivo</option>
                            </select>
                            <label for="metodo">Metodo de pago</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-9 mb-3">
                        <div class="form-floating">
                            <input type="text" name="direccion" id="direccion" placeholder="Dirección" value="<?php echo $datos["direccion"];?>" class="form-control">
                            <label for="direccion">Dirección</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating">
                            <input type="text" name="cp" id="cp" placeholder="Código Postal" value="<?php echo $datos["cp"];?>" class="form-control">
                            <label for="cp">Código Postal</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-control form-select" name="razon" id="razon">
                                <option value="<?php echo $datos["razon"];?>" selected>Actual: <?php echo ucfirst($datos["razon"]);?></option>
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
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-control form-select" name="dept" id="dept">
                                <option value="<?php echo $datos["dept"];?>" selected>Actual: <?php echo ucfirst($datos["dept"]);?></option>
                                <option value="hardware">Hardware</option>
                                <option value="web">Web</option>
                                <option value="redes">Redes</option>
                            </select>
                            <label for="dept">Departamento</label>
                        </div>
                    </div>
                </div>
                <?php
                    if($datos["tipo"]=="venta"){
                        echo '<h3 class="display-6 text-light text-center">Producto(s)</h3>';
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
                    } else { ?>
                        <hr class="text-light pb-3">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-control form-select" name="servicio" id="servicio" required>
                                        <option value="<?php echo $servicio[0];?>">Actual: <?php echo $servicio[0];?></option>
                                        <option value="Reparación Móvil">Reparación Móvil</option>
                                        <option value="Reparación Ordenador">Reparación Ordenador</option>
                                        <option value="Reparación Consola">Reparación Consola</option>
                                        <option value="Reparación Tablet">Reparación Tablet</option>
                                        <option value="Mantenimiento Otros">Mantenimiento Otros</option>
                                        <option value="Servicio Desarrollo Web">Servicio Desarrollo Web</option>
                                    </select>
                                    <label for="servicio">Tipo de servicio</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-control form-select" name="servicio2" id="servicio2" required>
                                        <option value="<?php echo $servicio[1];?>" selected>Actual: <?php echo $servicio[1];?></option>
                                    </select>
                                    <label for="servicio2">Servicio</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <textarea rows="2" style="height:100%;" class="form-control" name="desc" id="desc"><?php echo $datos["desc"]; ?></textarea>
                                    <label for="desc">Descripción del servicio</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="input-group">
                                <span class="input-group-text">Insumo</span>
                                    <div class="form-floating">
                                        <input class="form-control" placeholder="Descripción" type="text" name="insumo_desc" id="insumo_desc" value="<?php echo $datos["insumo_desc"];?>">
                                        <label for="insumo_desc">Descripción</label>
                                    </div>
                                    <div class="form-floating">
                                        <input class="form-control" placeholder="Precio" type="number" step=.01 name="insumo_precio" id="insumo_precio" value="<?php echo $datos["insumo_precio"];?>">
                                        <label for="insumo_precio">Precio</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
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

                function cambiarServicios(tipo){
                    var actual = $("#servicio2 option:selected").text().split(": ")[1];
                    console.log(actual);
                    switch(tipo){
                        case "Reparación Móvil":
                            var newOptions = {
                                "Otros": "",
                                "Cambio de pantalla": "",
                                "Reparación de tapa": "",
                                "Reparación de flex de carga": "",
                                "Reparación de altavoces y microfonos": "",
                                "Desbloqueo de teléfonos": "",
                                "Recuperacion de datos": "",
                                "Reparación de daños por agua": "",
                                "Reemplazo de carcasa": "",
                                "Reparación de botones": "",
                                "Reparación de Bluetooh y Wi-Fi": "",
                                "Reparación de sensores": "",
                                "Reemplazo de SIM y bandejas": "",
                                "Instalación de aplicaciones": "",
                                "Reparación de problemas de sobrecalentamiento": "",
                                "Restauración de fabrica": "",
                                "Reparación de camaras frontales y traseras": "",
                                "Reparación de problemas de carga inalambrica": "",
                                "Desinfección del dispositivo": ""
                            };
                            break;
                        case "Reparación Ordenador":
                            var newOptions = {
                                "Otros": "",
                                "Reparación de pantalla": "",
                                "Reparación de teclado": "",
                                "Reparación de placa de la torre": "",
                                "Reparación de software": "",
                                "Reparación de altavoces y microfonos": "",
                                "Recuperacion de datos": "",
                                "Reparación de daños por agua": "",
                                "Actualizacion de hardware": "",
                                "Reparación de Bluetooh y Wi-Fi": "",
                                "Desinfección del dispositivo": ""
                            };
                            break;
                        case "Reparación Consola":
                            var newOptions = {
                                "Otros": "",
                                "Mantenimiento": "",
                                "Mantenimiento preventivo": "",
                                "Asesoramiento sobre accesorios": "",
                                "Servicios de personalización": ""
                            };
                            break;
                        case "Reparación Tablet":
                            var newOptions = {
                                "Otros": "",
                                "Cambio de pantalla": "",
                                "Reparación de tapa": "",
                                "Reparación de altavoces y microfonos": "",
                                "Recuperacion de datos": "",
                                "Reparación de daños por agua": "",
                                "Reemplazo de carcasa": "",
                                "Reparación de botones": "",
                                "Reparación de Bluetooh y Wi-Fi": "",
                                "Reparación de sensores": "",
                                "Instalación de aplicaciones": "",
                                "Reparación de problemas de sobrecalentamiento": "",
                                "Restauración de fabrica": "",
                                "Reparación de camaras frontales y traseras": "",
                                "Desinfección del dispositivo": ""
                            };
                            break;
                        case "Mantenimiento Otros":
                            var newOptions = {
                                "Otros": "",
                                "Mantenimiento": "",
                                "Mantenimiento preventivo": "",
                                "Asesoramiento sobre accesorios": "",
                                "Servicios de personalización": ""
                            };
                            break;
                        case "Servicio Desarrollo Web":
                            var newOptions = {
                                "Otros": "",
                                "Creación Página Web": "",
                                "Mantenimiento Página Web": ""
                            };
                            break;
                    }
                    var $el = $("#servicio2");
                    $el.empty(); // remove old options
                    $.each(newOptions, function(key,value) {
                        let opt = actual == key?"<option selected></option>":"<option></option>";
                        $el.append($(opt)
                        .attr("value", key).text(key));
                    });
                }

                $('#servicio').on('change', function() {
                    cambiarServicios(this.value)
                });

                $( document ).ready(function() {
                    cambiarServicios("Reparación Móvil");
                });
        </script>
    </body>
</html>