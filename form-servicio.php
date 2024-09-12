<form action="execute.php" target="_blank" method="POST" class="form-control p-4 bg-dark">
    <h1 class="display-5 text-light text-center mb-4">SERVICIO</h1>
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
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-control form-select" name="local" id="local" required>
                                <option class="text-primary" value="Barcelona">Barcelona</option>
                                <option class="text-success" value="Mataró">Mataró</option>
                            </select>
                            <label for="local">Local</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-control" name="metodo" id="metodo" required>
                                <option value="Tarjeta">Tarjeta/Bizum</option>
                                <option value="Efectivo">Efectivo</option>
                            </select>
                            <label for="metodo">Método de pago</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating">
                        <select class="form-control" name="razon" id="razon" required>
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
                            <select class="form-control" name="dept" id="dept" required>
                                <option value="hardware">Hardware</option>
                                <option value="web">Web</option>
                                <option value="redes">Redes</option>
                            </select>
                            <label for="dept">Departamento</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="form-floating mb-3">
                            <textarea rows="5" style="height:100%;" class="form-control" placeholder="Descripción" name="motivo" id="motivo"></textarea>
                            <label for="motivo">Descripción</label>
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
                    <input type="submit" name="guardar-servicio" class="btn btn-success col-5 mx-auto" value="Guardar">
                </div>
            </form>