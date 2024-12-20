<?php
            if(isset($_POST["nuevo"])){
                insertarBDS();
            }
            if (isset($_POST["garantia"])) {
                insertarBDS($_POST["id"]);
            }
            if(isset($_POST["editar-factura"])){
                editarEntrada($_POST["id"]);
            }

            // CHANGE CARDS PER PAGE
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['valuePag'])) {
                    $_SESSION['pag'] = $_POST['valuePag']; // Update the session variable
                }
            }
            
            $pasos = ["Diagnóstico", "Aprobación", "Reparación", "Terminado", "Entregado"];
            $pasosLargo = ["Espera del diagnóstico", "Espera aprobación del cliente", "En Reparación", "Reparación terminada", "Entregado al cliente"];
            $colores = ["#f54254", "#e8a31a", "#2f852c", "#4472c4", "#adadad"];
            $localColor = ["blue", "red"];
            $colorDias = ["white", "#f5dcdc", "#f5b1b1", "#f36767", "#f13535"];
            
            if(isset($_GET["id"])){
                $id = $_GET["id"];
                $row = selectBD($id);
                
                $desc = '<b>Descripción:</b> '.$row["desc"];
                $precios = "";
                $cant = "";
                ?>
                <div class="ticketMenu input-group sticky-top mt-3 p-2 bg-dark d-flex" style="top: 70px; z-index: 1;">
                    <a href="list" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Volver</span>
                    </a>
                    <a href="controller/execute.php?ticketservicio=1&id=<?php echo $id; ?>" target="_blank"  class="menuItem btn btn-primary flex-fill">
                        <i class="bi bi-receipt-cutoff"></i> <span class="d-none d-sm-inline">Imprimir Ticket</span>
                    </a>
                    <a href="controller/execute.php?servicio=1&id=<?php echo $id; ?>" target="_blank" class="menuItem btn flex-fill"
                    style="background-color: #2596be; color: white; transition: background-color 0.3s;" 
                    onmouseover="this.style.backgroundColor='#277f9e';" 
                    onmouseout="this.style.backgroundColor='#2596be';">
                        <i class="bi bi-file-ruled"></i></i> <span class="d-none d-sm-inline">Imprimir Factura</span>
                    </a>
                    <a href="formulario&form=garantia&id=<?php echo $id; ?>" class="menuItem btn flex-fill" 
                    style="background-color: #007c7a; color: white; transition: background-color 0.3s;" 
                    onmouseover="this.style.backgroundColor='#006d6b';" 
                    onmouseout="this.style.backgroundColor='#007c7a';">
                        <i class="bi bi-file-text"></i> <span class="d-none d-sm-inline">Garantía</span>
                    </a>
                    <button type="button" class="menuItem btn flex-fill" 
                            style="background-color: #fd7e14; color: white; transition: background-color 0.3s;" 
                            onmouseover="this.style.backgroundColor='#e68a00';" 
                            onmouseout="this.style.backgroundColor='#fd7e14';" 
                            data-bs-toggle="modal" data-bs-target="#enviarModal">
                        <i class="bi bi-envelope-at"></i> <span class="d-none d-sm-inline">Reenviar</span>
                    </button>
                    <a href="formulario&form=edit&id=<?php echo $id; ?>" class="menuItem btn btn-success flex-fill">
                        <i class="bi bi-pencil-square"></i> <span class="d-none d-sm-inline">Editar</span>
                    </a>
                    <?php if (!empty($row["did"])) { ?>
                        <a href="controller/execute.php?deshacer=1&id=<?php echo $id; ?>" class="menuItem btn btn-danger flex-fill">
                            <i class="bi bi-arrow-counterclockwise"></i> <span class="d-none d-sm-inline">Deshacer devolución</span>
                        </a>
                    <?php } else { ?>
                        <a href="controller/execute.php?devolucion=1&id=<?php echo $id; ?>" class="menuItem btn btn-danger flex-fill">
                            <i class="bi bi-arrow-counterclockwise"></i> <span class="d-none d-sm-inline">Devolución</span>
                        </a>
                    <?php } ?>
                </div>
                <?php
                $estado = "";
                if(!empty($row["did"])) {
                    $estado = "<span class='text-danger'><i class='bi bi-arrow-counterclockwise'></i> DEVUELTO</span>";
                } else {
                    $estado = $pasosLargo[$row["estado"]];
                }
                $servicio = "";$ins = "";
                if(!empty($row["servicio"])){
                    $ser = explode(": ", $row["servicio"]);
                    $servicio .= '<p class="card-text"><b>Tipo de servicio:</b> '.$ser[0].'</p>';
                    $servicio .= '<p class="card-text"><b>Servicio:</b> '.$ser[1].'</p>';
                } else{
                    $ser = "Sin especificar";
                    $servicio .= '<p class="card-text"><b>Tipo de servicio:</b> </p>';
                    $servicio .= '<p class="card-text"><b>Servicio:</b> </p>';
                }
                if($row["insumo_desc"]!=""){
                    $insumos = explode(";", $row["insumo_desc"]);
                    $precios = explode(";", $row["insumo_precio"]);
                    $ins="<li class='list-group-item'><b>Insumo:</b></li>";
                    for($k = 0; $k<count($insumos);){
                        $ins .= '<li class="list-group-item mx-3"><b>'.$insumos[$k].'</b> ('.$precios[$k].'€)';
                        $k++;
                        if(isset($d[$k])){
                            $desc .= ", ";
                        }
                    }
                } else {
                    $ins = '<li class="list-group-item"><b>Insumo:</b> 0';
                }
                $ins .= '</li>';
                $garantia = "";
                if($row["garantia"] != 0) $garantia = " | <i class='bi bi-file-text'></i> <a style='text-decoration:none;color:#FFA' href='list&id=".$row["garantia"]."'>GARANTÍA <i class='bi bi-arrow-right-short'></i></a>";
                echo '<div class="col-12">
                            <div class="card my-3">
                                <h5 class="card-header py-3" style="color:white;background-color:'.$colores[$row["estado"]].';">'.$row["servicio"].' # '.$row["id"].$garantia.'</h5>
                                <div class="card-body">
                                    '.$estado;
                                // PROGRESS BAR
                                // Example value for estado
                                $estado = isset($row["estado"]) ? $row["estado"]+1 : 0;
                                // Define the total number of states
                                $totalStates = 5;
                                // Calculate the percentage based on the estado
                                $percentage = ($estado / $totalStates) * 100;
                                ?>
                                <div class="progress mt-3">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%;
                                                background-color:<?php echo $colores[$row["estado"]]; ?>"
                                        aria-valuenow="<?php echo $estado; ?>" aria-valuemin="0" aria-valuemax="<?php echo $totalStates; ?>">
                                        <?php echo $estado; ?> / <?php echo $totalStates; // Display current state out of total ?>
                                    </div>
                                </div>
                                <br>
                                <?php
                echo '<div id="cardStepBtns" class="input-group px-2 mb-2">';
                $disableLeft = true;
                $disableRight = true;

                if($row["estado"] > 0) {
                    $disableLeft = false;
                }
                
                if($row["estado"] < 4) {
                    $disableRight = false;
                }

                // Left button (state -1)
                echo '
                <a href="controller/execute.php?estado=' . ($row["estado"] - 1) . '&id=' . $row["id"] . '&pag=0" 
                class="stepButton rounded ' . ($disableLeft ? 'disabled' : '') . '" 
                style="pointer-events: ' . ($disableLeft ? 'none' : 'auto') . ';">
                    <span class="button__text">' . ($disableLeft ? '' : $pasos[$row["estado"] - 1]) . '</span>
                    <span class="button__icon" style="color: white; background-color:' . ($disableLeft ? 'grey' : $colores[$row["estado"] - 1]) . '">
                        <i class="bi bi-arrow-left"></i>
                    </span>
                </a>';

                // Right button (state +1)
                echo '
                <a href="controller/execute.php?estado=' . ($row["estado"] + 1) . '&id=' . $row["id"] . '&pag=0" 
                class="stepButton rounded ' . ($disableRight ? 'disabled' : '') . '" 
                style="pointer-events: ' . ($disableRight ? 'none' : 'auto') . ';">
                    <span class="button__text">' . ($disableRight ? '' : $pasos[$row["estado"] + 1]) . '</span>
                    <span class="button__icon" style="color: white; background-color:' . ($disableRight ? 'grey' : $colores[$row["estado"] + 1]) . '">
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </a>';
                // Close the input group
                echo '</div>';
                                ?>
                                <?php if((($_SESSION["login"]=="admin"||$_SESSION["login"]=="dependiente"))&&$row["estado"]!=4){ ?>
                                    <form action="controller/execute.php" class="m-2" method="get">
                                        <input type="hidden" name="pag" value="1">
                                        <input type="hidden" name="id" value="<?php echo $row["id"] ?>">
                                        <!-- METODO DE PAGO -->
                                        <div class="radio-buttons-container">
                                            <div class="radio-button">
                                                <input name="metodo" id="radio2" class="radio-button__input" type="radio" value="Tarjeta" checked>
                                                <label for="radio2" class="radio-button__label">
                                                    <span class="radio-button__custom"></span>
                                                        
                                                        Tarjeta
                                                </label>
                                            </div>
                                            <div class="radio-button">
                                                <input name="metodo" id="radio1" class="radio-button__input" type="radio" value="Efectivo">
                                                <label for="radio1" class="radio-button__label">
                                                    <span class="radio-button__custom"></span>
                                                    
                                                    Efectivo
                                                </label>
                                            </div>
                                        </div>

                                        <button class="cssbuttons-io-button bg-secondary" type="submit" name="estado" value="4">Entregado/Cobrar<div class="icon"><i class="bi bi-cash text-dark"></i></div></button>
                                    </form>
                                <?php } ?>
                                    <hr>
                                    <div class="row">
                                        <h3 class="display-4 mb-4">Datos Cliente</h3>
                                        <div class="col-md-6 col-12">
                                            <p class="card-text"><b>Nombre:</b> <?php echo !empty($row["nombre"])?$row["nombre"]:"No especificado"; ?></p>
                                            <p class="card-text"><b>Documento:</b> <?php echo !empty($row["documento"])?$row["documento"]:"No especificado"; ?></p>
                                            <p class="card-text"><b>Email:</b> <?php echo !empty($row["email"])?$row["email"]:"No especificado"; ?></p>
                                            <p class="card-text"><b>Fecha Nacimiento:</b> <?php echo !empty($row["fecha_nacimiento"])?$row["fecha_nacimiento"]:"No especificado"; ?></p>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <p class="card-text"><b>Dirección:</b> <?php echo !empty($row["direccion"])?$row["direccion"]." - ".$row["cp"]:"No especificado"; ?></p>
                                            <p class="card-text"><b>Teléfono:</b> <?php if(!empty($row["telefono"])) { ?> <a href="https://wa.me/<?php echo $row["telefono"] ?>" target="_blank"><?php echo $row["telefono"]?></a><?php } else {echo "No especificado";} ?></p>
                                            <p class="card-text"><b>Cómo nos encontró:</b> <?php echo $row["razon"] ?></p>
                                            <p class="card-text"><b>Código:</b> <?php echo !empty($row["codigo_socio"])?$row["codigo_socio"]:"No es socio"; ?></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <h3 class="display-4 mb-4">Detalles</h3>
                                        <div class="col-md-6 col-12">
                                            <p class="card-text"><b>Fecha de entrada:</b> <?php echo $row["fecha"] ?></p>
                                            <p class="card-text"><b>Fecha de pago:</b> <?php echo !empty($row["fecha_pago"])?$row["fecha_pago"]:"No pagado"; ?></p>
                                            <p class="card-text"><b>Método de pago:</b> <?php echo !empty($row["metodo"])?$row["metodo"]:"No pagado"; ?></p>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <p class="card-text"><b>Local:</b> <?php echo $row["local"] ?></p>
                                            <p class="card-text"><b>Departamento:</b> <?php echo $row["dept"] ?></p>
                                            <p class="card-text"><b>Código Usado:</b> <?php echo $row["codigo_usado"] ?></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <h3 class="display-4 mb-4">Servicio</h3>
                                        <div class="col-md-6 col-12">
                                            <?php echo $servicio ?>
                                            <p class="card-text"><b>Dispositivo:</b> <?php echo $row["nombre_dispositivo"] ?></p>
                                            <p class="card-text"><?php echo $desc ?></p>
                                            <p class="card-text"><b>Descripción Técnico:</b> <?php echo !empty($row["desc_tecnico"])?$row["desc_tecnico"]:"Sin descripción" ?></p>
                                            <?php if($_SESSION["login"] == "admin") echo ' <br><a href="edit_insumo&id='.$id.'" class="fileButton w-50">Editar insumo y descripción técnico</a>'; ?>
                                            <?php if($_SESSION["login"] == "tecnico") echo ' <br><a href="edit_insumo&id='.$id.'" class="fileButton w-50">Editar descripción técnico</a>'; ?>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <b>Firma:</b><br>
                                            <?php if (!empty($row["firma"])): ?>
                                                <img class="w-100 h-100" height="250px" src="<?php echo "firmas/".$row["firma"]; ?>" alt="firma">
                                            <?php else: ?>
                                                <p class="card-text">
                                                    No hay firma disponible.
                                                    <br>
                                                    Escanea el QR:
                                                    <br>
                                                    <br>
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://quicktr.es/formulario/form-firma.php?id=<?php echo $_GET["id"]; ?>" alt="QR">
                                                    <br>
                                                    <br>o dale clic al siguiente botón para 
                                                    <?php if (empty($row["firma"])){ ?>
                                                    <a class="btn btn-primary" href="form-firma.php?id=<?php echo $_GET["id"]; ?>" target="_blank">Añadir firma</a>
                                                    <?php } ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    
                                    <ul class="list-group list-group-flush mb-3">
                                        <?php echo $_SESSION["login"] == "admin" ? $ins : "<hr>" ?>
                                        <li class="list-group-item"><b>Precio:</b> <?php echo $row["precio"] ?>€ (- <?php echo $row["descuento"] ?>%) (+ IVA <?php echo $row["iva"] ?>%) = <b><?php echo $row["precio-final"] ?>€</b></li>
                                    </ul>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <button type="button" class="fileButton" data-bs-toggle="modal" data-bs-target="#fotos">
                                            <i class="bi bi-images" style="font-size: large;"></i>    
                                            Añadir fotos
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="gallery">
                                        <?php
                                        $pdo = connect();
                                        $fotos = $pdo->prepare("SELECT * FROM `foto` WHERE id_orden = :id");
                                        $fotos->bindParam(':id', $_GET["id"]);
                                        try {
                                            $fotos->execute();
                                        } catch(PDOException $e){
                                            echo $e->getMessage();
                                        }
                                        $rowCount = $fotos->rowCount();
                                        if ($rowCount > 0) {
                                            while ($img = $fotos->fetch(PDO::FETCH_ASSOC)) {
                                                if (file_exists('fotos/'. $img["archivo"])) {
                                                    echo '
                                                    <a href="fotos/'. $img["archivo"] .'" target="_blank">
                                                        <img src="fotos/'. $img["archivo"] .'" alt="Foto'. $img["id"] .'">
                                                    </a>
                                                    ';
                                                } else {
                                                    echo "<p>(Foto '".$img["archivo"]."' eliminada)</p>";
                                                }
                                            }
                                        }
                                        ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
<!-- IMAGENES -->
            <div class="modal fade" id="fotos" tabindex="-1" aria-labelledby="fotosLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="fotosLabel">SUBIR FOTOS</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="controller/execute.php" method="POST" enctype="multipart/form-data">
                                <div class="row px-5 mb-3">
                                    <div class="col-12">
                                        <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                                        <input type="file" id="imageUpload" accept="image/*" name="images[]" multiple class="form-control form-control-lg">
                                        <br>
                                        <div class="form-floating">
                                            <textarea rows="5" style="height:100%;" class="form-control form-control-lg" placeholder="Actualizar descripción" name="desc" id="desc"><?php echo $row["desc"]; ?></textarea>
                                            <label for="desc">Actualizar descripción</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <input type="submit" name="guardar-fotos" class="btn btn-success btn-lg col-5 mx-auto" value="Enviar">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
<!-- ENVIAR -->
            <div class="modal fade" id="enviarModal" tabindex="-1" aria-labelledby="enviarModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="enviarModalLabel">Enviar esta factura? (# <?php echo $row["id"] ?>)</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Enviar a: <?php echo $row["email"] ?></p>
                            <form method="GET" action="controller/execute.php">
                                <input type="hidden" name="id" value="<?php echo $row["id"] ?>">
                                <input class="btn btn-success" type="submit" name="enviar" id="enviar" value="Enviar">
                                <button type="button" class="btn btn-secondary"data-bs-dismiss="modal">Cancelar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            exit();
            }
            ?>