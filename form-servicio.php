<form action="execute.php" target="_blank" method="POST" class="form-control p-4 bg-dark">
    <h1 class="display-5 text-light text-center mb-4">SERVICIO</h1>
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Nombre" type="text" name="nombre" id="nombre">
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
                                <input class="form-control" placeholder="Teléfono" type="tel" name="tel" id="tel">
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
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating">
                            <select class="form-control form-select" name="local" id="local">
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
                            <input class="form-control" placeholder="Email" type="email" name="email" id="email">
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating">
                            <select class="form-control form-select" name="metodo" id="metodo">
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Efectivo">Efectivo</option>
                            </select>
                            <label for="metodo">Método de pago</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                        <select class="form-control form-select" name="razon" id="razon">
                                <option value="Sin especificar" selected>-</option>
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
                                <option value="Reparación Móvil" selected>Reparación Móvil</option>
                                <option value="Reparación Ordenador">Reparación Ordenador</option>
                                <option value="Reparación Consola">Reparación Consola</option>
                                <option value="Reparación Tablet">Reparación Tablet</option>
                                <option value="Mantenimiento Otros">Mantenimiento Otros</option>
                                <option value="Servicio Desarrollo Web">Servicio Desarrollo Web</option>
                            </select>
                            <label for="servicio">Tipo de Servicio</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-control form-select" name="servicio2" id="servicio2">
                                <option value="">Selecciona un tipo</option>
                            </select>
                            <label for="servicio2">Servicio</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea rows="5" style="height:100%;" class="form-control" placeholder="Descripción" name="motivo" id="motivo"></textarea>
                            <label for="motivo">Descripción</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="input-group">
                        <span class="input-group-text">Insumo</span>
                            <div class="form-floating">
                                <input class="form-control" placeholder="Descripción" type="text" name="insumo_desc" id="insumo_desc">
                                <label for="insumo_desc">Descripción</label>
                            </div>
                            <div class="form-floating">
                                <input class="form-control" placeholder="Precio" type="number" step=.01 name="insumo_precio" id="insumo_precio">
                                <label for="insumo_precio">Precio</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onkeyup="findTotal()" placeholder="Precio" type="number" step="0.01" name="precio" id="precio" required>
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
                            <input class="form-control" onkeyup="findTotal()" placeholder="Iva 21%" type="number" step="0.1" value=21 name="iva" id="iva" required>
                            <label for="iva">Iva 21%</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-floating">
                            <input class="form-control" onkeyup="findPrecio()" placeholder="Precio Final" step="0.01" type="number" name="precio-final" id="precio-final" required>
                            <label for="precio-final">Precio Final €</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" name="guardar-servicio" class="btn btn-success col-5 mx-auto" value="Guardar">
                </div>
            </form>

            <script>
                function cambiarServicios(tipo){
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
                    $el.append($("<option></option>")
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