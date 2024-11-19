<?php
$datos=selectBD($_GET["id"]);
$servicio = explode(": ", $datos["servicio"]);
?>
        <!-- FORM -->
        <form action="?pag=list" method="POST" class="form-control p-4 bg-dark">
                <a href="index.php?pag=list&id=<?php echo $datos["id"]; ?>" class="btn btn-secondary">Volver</a>
                <h1 class="display-5 text-light text-center mb-4">Retorno de cliente</h1>
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
                    <input type="hidden" name="razon" value="Retorno">
                    <div class="col-12 col-md-3 mb-3">
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
                        <hr class="text-light pb-3">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-control form-select" name="servicio" id="servicio">
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
                                    <select class="form-control form-select" name="servicio2" id="servicio2">
                                    </select>
                                    <label for="servicio2">Servicio</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input class="form-control" placeholder="Dispositivo" type="text" name="dispositivo" id="dispositivo">
                                    <label for="dispositivo">Dispositivo</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <textarea rows="2" style="height:100%;" class="form-control" name="motivo" id="motivo"></textarea>
                                    <label for="motivo">Descripción del servicio</label>
                                </div>
                            </div>
                        </div>
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onblur="findTotal()" placeholder="Precio" type="number" step="0.01" name="precio" id="precio" value=0>
                            <label for="precio">Precio €</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onkeyup="findTotal()" placeholder="Descuento" type="number" step="0.1" value=0 name="descuento" id="descuento">
                            <label for="descuento">Descuento</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onblur="findTotal()" placeholder="Iva 21%" type="number" step="0.1" value=21 name="iva" id="iva">
                            <label for="iva">Iva 21%</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onblur="findPrecio()" placeholder="Precio Final" step="0.01" type="number" name="precio-final" id="precio-final" value=0>
                            <label for="precio-final">Precio Final €</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <input type="submit" name="nuevo" class="btn btn-primary col-12 mx-auto" value="Crear Ticket">
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