<?php
// INITIALIZE DB CONNECTION
$db = new Database();

include_once "functions_ticket.php";

// FETCH TICKET INFO
if (isset($_GET["id"])) $ticket = $db->fetchId($_GET["id"]);
else $ticket = $db->fetchId($id);
?>

<!--  -->
<!-- {START} MENU OPCIONES -->
<div class="ticketMenu input-group sticky-top mt-3 p-2 bg-dark d-flex" style="top: 70px; z-index: 1;">
    <a href="list" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Volver</span>
    </a>
    <a href="document.php?tipo=ticket&id=<?php echo $ticket->id; ?>" target="_blank" class="menuItem btn btn-primary flex-fill">
        <i class="bi bi-receipt-cutoff"></i> <span class="d-none d-sm-inline">Imprimir Ticket</span>
    </a>
    <a href="document.php?tipo=factura&id=<?php echo $ticket->id; ?>" target="_blank" class="menuItem btn flex-fill"
        style="background-color: #2596be; color: white; transition: background-color 0.3s;"
        onmouseover="this.style.backgroundColor='#277f9e';"
        onmouseout="this.style.backgroundColor='#2596be';">
        <i class="bi bi-file-ruled"></i></i> <span class="d-none d-sm-inline">Imprimir Factura</span>
    </a>
    <a href="formulario&form=garantia&id=<?php echo $ticket->id; ?>" class="menuItem btn flex-fill"
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
    <?php if ($ticket->estado == 5) { ?>
        <a href="list&id=<?php echo $ticket->id; ?>&estado=0" class="menuItem btn btn-danger flex-fill">
            <i class="bi bi-arrow-counterclockwise"></i> <span class="d-none d-sm-inline">Deshacer devolución</span>
        </a>
    <?php } else { ?>
        <a href="list&id=<?php echo $ticket->id; ?>&estado=5" class="menuItem btn btn-danger flex-fill">
            <i class="bi bi-arrow-counterclockwise"></i> <span class="d-none d-sm-inline">Devolución</span>
        </a>
    <?php } ?>
    <?php if ($_SESSION["login"] == "admin") { ?>
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#historialModal">
            <i class="bi bi-clock-history"></i> <span class="d-none d-sm-inline">Historial</span>
        </button>
    <?php } ?>
</div>
<!-- {END} MENU OPCIONES -->
<!--  -->

<?php
$estado = "";
if (!empty($ticket->did)) {
    $estado = "<span class='text-danger'><i class='bi bi-arrow-counterclockwise'></i> DEVUELTO</span>";
} else {
    $estado = $ticket->pasosLargo[$ticket->estado];
}
$ins = "";
if ($ticket->insumo_desc != "") {
    $insumos = explode(";", $ticket->insumo_desc);
    $precios = explode(";", $ticket->insumo_precio);
    $ins = "<li class='list-group-item'><b>Insumo:</b></li>";
    for ($k = 0; $k < count($insumos);) {
        $ins .= '<li class="list-group-item mx-3"><b>' . $insumos[$k] . '</b> (' . $precios[$k] . '€)';
        $k++;
    }
} else {
    $ins = '<li class="list-group-item"><b>Insumo:</b> 0';
}
$ins .= '</li>';
$garantia = "";
if ($ticket->garantia != 0) $garantia = " | <i class='bi bi-file-text'></i> <a style='text-decoration:none;color:#FFA' href='list&id=" . $ticket->garantia . "'>GARANTÍA PARA #" . $ticket->garantia . "</a>";
echo '<div class="col-12">
        <div class="card my-3">
            <h5 class="card-header py-3" style="color:white;background-color:' . $ticket->colores[$ticket->estado] . ';">' . $ticket->servicio . ' # ' . $ticket->id . $garantia . '</h5>
            <div class="card-body">
                ' . $estado;
// PROGRESS BAR
// Example value for estado
$estado = isset($ticket->estado) ? $ticket->estado + 1 : 0;
// Define the total number of states
$totalStates = 5;
// Calculate the percentage based on the estado
$percentage = ($estado / $totalStates) * 100;
?>
<div class="progress mt-3">
    <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%;
                                                background-color:<?php echo $ticket->colores[$ticket->estado]; ?>"
        aria-valuenow="<?php echo $estado; ?>" aria-valuemin="0" aria-valuemax="<?php echo $totalStates; ?>">
        <?php echo $estado; ?> / <?php echo $totalStates; // Display current state out of total 
                                    ?>
    </div>
</div>
<br>
<!-- CONDICION PARA REPARTIDOR -->
<?php if ($_SESSION["login"] != "repartidor") { ?>
<?php
echo '<div id="cardStepBtns" class="input-group px-2 mx-auto mb-2">';
$disableLeft = true;
$disableRight = true;

if ($ticket->estado > 0) {
    $disableLeft = false;
}

if ($ticket->estado < 3) {
    $disableRight = false;
}

// Left button (state -1)
echo '
        <a href="list&id=' . $ticket->id . '&estado=' . ($ticket->estado - 1) . '" 
        class="stepButton rounded ' . ($disableLeft ? 'disabled' : '') . '" 
        style="pointer-events: ' . ($disableLeft ? 'none' : 'auto') . ';">
            <span class="button__text">' . ($disableLeft ? '' : $ticket->pasos[$ticket->estado - 1]) . '</span>
            <span class="button__icon" style="color: white; background-color:' . ($disableLeft ? 'grey' : $ticket->colores[$ticket->estado - 1]) . '">
                <i class="bi bi-arrow-left"></i>
            </span>
        </a>';

// Right button (state +1)
echo '
        <a href="list&id=' . $ticket->id . '&estado=' . ($ticket->estado + 1) . '" 
        class="stepButton rounded ' . ($disableRight ? 'disabled' : '') . '" 
        style="pointer-events: ' . ($disableRight ? 'none' : 'auto') . ';">
            <span class="button__text">' . ($disableRight ? '' : $ticket->pasos[$ticket->estado + 1]) . '</span>
            <span class="button__icon" style="color: white; background-color:' . ($disableRight ? 'grey' : $ticket->colores[$ticket->estado + 1]) . '">
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>';
// Close the input group
echo '</div>';
?>
<?php if ((($_SESSION["login"] == "admin" || $_SESSION["login"] == "dependiente")) && $ticket->estado != 4) { ?>
    <!-- BOTON MODAL COBRAR/CERRAR TICKET -->
    <button data-bs-toggle="modal" data-bs-target="#cobrarModal" class="mx-auto cssbuttons-io-button bg-secondary">Entregado/Cobrar<div class="icon"><i class="bi bi-cash text-dark"></i></div></button>
<?php } ?>

<hr>
<form action="" method="POST">
    <div class="row">
        <h3 class="display-4 mb-4">Datos Cliente</h3>
        <div class="col-md-6 col-12">
            <p class="card-text">
                <b>Nombre:</b>
                <span>
                    <?php echo !empty($ticket->nombre) ? $ticket->nombre : "No especificado"; ?>
                    <button class="btn p-0" onclick="editField(this.parentElement, '<?php echo $ticket->nombre; ?>', 'nombre')"><i class="bi bi-pencil-square"></i></button>
                </span>
            </p>
            <p class="card-text">
                <b>Documento:</b>
                <span>
                    <?php echo !empty($ticket->documento) ? $ticket->documento : "No especificado"; ?>
                    <button class="btn p-0" onclick="editField(this.parentElement, '<?php echo $ticket->documento; ?>', 'documento')"><i class="bi bi-pencil-square"></i></button>
                </span>
            </p>
            <p class="card-text">
                <b>Email:</b>
                <span>
                    <?php echo !empty($ticket->email) ? $ticket->email : "No especificado"; ?>
                    <button class="btn p-0" onclick="editField(this.parentElement, '<?php echo $ticket->email; ?>', 'email')"><i class="bi bi-pencil-square"></i></button>
                </span>
            </p>
            <p class="card-text">
                <b>Fecha Nacimiento:</b> <?php echo !empty($ticket->fecha_nacimiento) ? $ticket->fecha_nacimiento : "No especificado"; ?>
            </p>
            <p class="card-text"><b>Código:</b> <?php echo !empty($ticket->codigo_socio) ? $ticket->codigo_socio : "No es socio"; ?></p>
        </div>
        <div class="col-md-6 col-12">
            <p class="card-text">
                <b>Dirección:</b>
                <span>
                    <?php echo !empty($ticket->direccion) ? $ticket->direccion : "No especificado"; ?>
                    <button class="btn p-0" onclick="editField(this.parentElement, '<?php echo $ticket->direccion; ?>', 'direccion')"><i class="bi bi-pencil-square"></i></button>
                </span>
            </p>
            <p class="card-text">
                <b>Código Postal:</b>
                <span>
                    <?php echo !empty($ticket->cp) ? $ticket->cp : "No especificado"; ?>
                    <button class="btn p-0" onclick="editField(this.parentElement, '<?php echo $ticket->cp; ?>', 'cp')"><i class="bi bi-pencil-square"></i></button>
                </span>
            </p>
            <p class="card-text">
                <b>Teléfono:</b>
                <span>
                    <?php if (!empty($ticket->telefono)) { ?> <a href="https://wa.me/<?php echo $ticket->telefono ?>" target="_blank"><?php echo $ticket->telefono ?></a>
                    <?php } else {
                        echo "No especificado";
                    } ?>
                    <button class="btn p-0" onclick="editField(this.parentElement, '<?php echo $ticket->telefono; ?>', 'telefono')"><i class="bi bi-pencil-square"></i></button>
                </span>
            </p>
            <p class="card-text"><b>Cómo nos encontró:</b> <?php echo $ticket->razon ?></p>
        </div>
    </div>
    <div class="row mt-4 d-none" id="guardarCambiosBtn">
        <div class="col-12 text-center">
            <input type="hidden" name="id" value="<?php echo $ticket->id; ?>">
            <input type="submit" name="guardarCliente" class="btn btn-primary" value="Guardar Cambios">
        </div>
    </div>

</form>
<hr>
<div class="row">
    <h3 class="display-4 mb-4">Detalles</h3>
    <div class="col-md-6 col-12">
        <p class="card-text"><b>Fecha de entrada:</b> <?php echo $ticket->fecha ?></p>
        <p class="card-text"><b>Fecha de pago:</b> <?php echo !empty($ticket->fecha_pago) ? $ticket->fecha_pago : "No pagado"; ?></p>
        <p class="card-text"><b>Método de pago:</b> <?php echo !empty($ticket->metodo) ? $ticket->metodo : "No pagado"; ?></p>
    </div>
    <div class="col-md-6 col-12">
        <p class="card-text"><b>Local:</b> <?php echo $ticket->local ?></p>
        <p class="card-text"><b>Departamento:</b> <?php echo $ticket->dept ?></p>
        <p class="card-text"><b>Código Usado:</b> <?php echo $ticket->codigo_usado ?></p>
    </div>
</div>
<hr>
<!-- END CONDICION PARA REPARTIDOR -->
<?php } ?>
<div class="row">
    <h3 class="display-4 mb-4">Servicio</h3>
    <div class="col-md-6 col-12">
        <?php $ser = explode(": ", $ticket->servicio); ?>
        <p class="card-text"><b>Tipo de servicio:</b> <?php echo $ser[0]; ?></p>
        <p class="card-text"><b>Servicio:</b> <?php echo $ser[1]; ?></p>
        <p class="card-text"><b>Dispositivo:</b> <?php echo $ticket->nombre_dispositivo ?></p>
        <p class="card-text"><b>Descripción:</b> <?php echo $ticket->desc; ?></p>
        <p class="card-text"><b>Descripción Técnico:</b> <?php echo !empty($ticket->desc_tecnico) ? $ticket->desc_tecnico : "Sin descripción" ?></p>
        <br>
        
<!-- CONDICION REPARTIDOR -->
<?php if ($_SESSION["login"] != "repartidor") { ?>
        <button type="button" class="fileButton" data-bs-toggle="modal" data-bs-target="#insumoModal">
            <?php if ($_SESSION["login"] == "admin" || $_SESSION["login"] == "tecnico") {
                echo 'Editar insumo y descripción técnico';
            } else {
                echo 'Editar descripción';
            } ?>
        </button>
<!-- END CONDICION PARA REPARTIDOR -->
<?php } ?>
    </div>
    
<?php if ($_SESSION["login"] != "repartidor") { ?>
    <div class="col-md-6 col-12">
        <b>Firma:</b><br>
        <?php if (!empty($ticket->firma)): ?>
            <img class="w-100 h-100" height="250px" src="<?php echo "firmas/" . $ticket->firma; ?>" alt="firma">
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
                <?php if (empty($ticket->firma)) { ?>
                    <button onclick="loadSignature()" type="button" class="fileButton d-inline" data-bs-toggle="modal" data-bs-target="#firmaModal">
                        <i class="bi bi-pen"></i>
                        Añadir Firma
                    </button>
                <?php } ?>
            </p>
        <?php endif; ?>
    </div>
<?php } ?>
</div>

<?php if ($_SESSION["login"] != "tecnico") { ?>
    <hr>
    <div class="row">
        <span class="fs-4 mb-2">Insumo(s)</span>
        <?php
        // INSUMOS

        // Define estados and colores arrays
        $estados = ["", "PENDIENTE", "EN CAMINO", "ENTREGADO"];
        $colores = ["white", "#ed7279", "#f0de92", "#8bfa82"];

        // Fetch insumos data
        $insumos = $db->fetchInsumos($ticket->id);

        // Check if there are any insumos to display
        if (count($insumos) > 0) {
            echo "<table border='1' class='table table-secondary'>";
            echo "<thead><tr><th>Nombre</th><th>Precio</th><th>Local</th><th>Fecha</th><th>Estado</th></tr></thead>";
            echo "<tbody>";

            // Loop through results and display each row
            foreach ($insumos as $row) {
                echo "<tr>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . htmlspecialchars($row['nombre']) . "</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . htmlspecialchars($row['precio']) . " €</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . htmlspecialchars($row['local']) . "</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'><small class='text-muted'>" . htmlspecialchars($row['fecha']) . "</small></td>";
                if($row["estado"] > 0) {
                    echo "<td style='background:" . $colores[$row["estado"]] . "'>" . $estados[$row['estado']] . "</td>";
                }else {
                    echo "
                    <td style='background:" . $colores[$row["estado"]] . "'>
                    <form style='display:inline-block' action='' method='post'>
                        <input type='hidden' name='id' value='".$row["id"]."'>
                        <button type='submit' class='btn btn-primary' name='pedirInsumo'>Pedir</button>
                    </form>
                    </td>";
                }
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No hay insumos para mostrar.</p>";
        }
        ?>
    </div>
<!-- CONDICION REPARTIDOR -->
<?php if ($_SESSION["login"] != "repartidor") { ?>
    <hr>
    <form action="" method="POST">
        <div class="row">
            <div class="col-12 col-md-4 mb-3">
                <div class="form-floating">
                    <input class="form-control" onkeyup="findTotal(); document.getElementById('submitPrecio').removeAttribute('disabled')" placeholder="Precio" type="number" step="0.01" name="precio" id="precio"
                        value="<?php echo $ticket->precio; ?>">
                    <label for="precio">Precio €</label>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <div class="input-group">
                    <div class="form-floating">
                        <input class="form-control" onkeyup="findTotal(); document.getElementById('submitPrecio').removeAttribute('disabled')" placeholder="Descuento" type="number" step="0.1" name="descuento" id="descuento"
                            value="<?php echo $ticket->descuento; ?>">
                        <label for="descuento">Descuento</label>
                    </div>
                    <div class="form-floating">
                        <input class="form-control" onkeyup="findTotal(); document.getElementById('submitPrecio').removeAttribute('disabled')" placeholder="Iva 21%" type="number" step="0.1" name="iva" id="iva"
                            value="<?php echo $ticket->iva; ?>">
                        <label for="iva">Iva 21%</label>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <div class="form-floating">
                    <input class="form-control" onkeyup="findPrecio(); document.getElementById('submitPrecio').removeAttribute('disabled')" placeholder="Precio Final" step="0.01" type="number" name="precio_final" id="precio-final"
                        value="<?php echo $ticket->precio_final; ?>">
                    <label for="precio-final">Precio Final €</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center">
                <input type="hidden" name="id" value="<?php echo $ticket->id; ?>">
                <input id="submitPrecio" type="submit" disabled name="guardarPrecio" class="btn btn-primary" value="Guardar Precio">
            </div>
        </div>
    </form>
<!-- END CONDICION REPARTIDOR -->
<?php } ?>
<?php } ?>
<hr>
<!-- CONDICION REPARTIDOR -->
<?php if ($_SESSION["login"] != "repartidor") { ?>
<div class="row">
    <div class="col-12 mb-3">
        <button type="button" class="fileButton" data-bs-toggle="modal" data-bs-target="#fotos">
            <i class="bi bi-images" style="font-size: large;"></i>
            Añadir fotos
        </button>
    </div>
</div>
<!-- END CONDICION REPARTIDOR -->
<?php } ?>
<div class="row">
    <div class="gallery">
        <?php
        $pdo = $db->pdo;
        $fotos = $pdo->prepare("SELECT * FROM `foto` WHERE id_orden = :id");
        $fotos->bindParam(':id', $_GET["id"]);
        try {
            $fotos->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $rowCount = $fotos->rowCount();
        if ($rowCount > 0) {
            while ($img = $fotos->fetch(PDO::FETCH_ASSOC)) {
                if (file_exists('fotos/' . $img["archivo"])) {
                    echo '
                                                    <a href="fotos/' . $img["archivo"] . '" target="_blank">
                                                        <img src="fotos/' . $img["archivo"] . '" alt="Foto' . $img["id"] . '">
                                                    </a>
                                                    ';
                } else {
                    echo "<p>(Foto '" . $img["archivo"] . "' eliminada)</p>";
                }
            }
        }
        ?>
    </div>
</div>

<!-- /////////////////////////////////////////////// -->

<!-- IMAGENES -->
<div class="modal fade" id="fotos" tabindex="-1" aria-labelledby="fotosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fotosLabel">SUBIR FOTOS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row px-5 mb-3">
                        <div class="col-12">
                            <input type="hidden" name="id" value="<?php echo $ticket->id; ?>">
                            <input type="file" id="imageUpload" accept="image/*" name="images[]" multiple class="form-control form-control-lg">
                            <br>
                            <div class="form-floating">
                                <textarea rows="5" style="height:100%;" class="form-control form-control-lg" placeholder="Actualizar descripción" name="desc" id="desc"><?php echo $ticket->desc; ?></textarea>
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

<!-- FIRMA -->
<div class="modal" id="firmaModal" tabindex="-1" aria-labelledby="firmaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="firmaModalLabel">FIRMA CLIENTE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?php echo $ticket->id; ?>">
                    <div class="row px-md-5 mb-3">
                        <div class="col-12">
                            <h1 class="display-5 text-center mb-4">FIRMA</h1>
                            <div class="form-control text-center">
                                <div id="signature"></div>
                                <input type="hidden" name="sign" id="sign">
                                <button class="btn btn-secondary btn-lg" id="clear">Borrar</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <input type="submit" name="guardar-firma" class="btn btn-success btn-lg col-5 mx-auto" value="Enviar">
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
                <h1 class="modal-title fs-5" id="enviarModalLabel">Enviar esta factura? (# <?php echo $ticket->id ?>)</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Enviar a: <?php echo $ticket->email ?></p>
                <form method="GET" action="list">
                    <input type="hidden" name="id" value="<?php echo $ticket->id ?>">
                    <input class="btn btn-success" type="submit" name="enviar" id="enviar" value="Enviar">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- INSUMO/DESC -->
<div class="modal modal-lg fade" id="insumoModal" tabindex="-1" aria-labelledby="insumoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="insumoModalLabel">Editar detalles servicio (# <?php echo $ticket->id ?>)</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" class="p-4">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-floating">
                                <input class="form-control" placeholder="Dispositivo" type="text" name="dispositivo" id="dispositivo" value="<?php echo $ticket->nombre_dispositivo; ?>">
                                <label for="dispositivo">Dispositivo</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <textarea rows="3" style="height:100%;" class="form-control" name="desc" id="desc"><?php echo $ticket->desc; ?></textarea>
                                <label for="desc">Descripción del servicio</label>
                            </div>
                        </div>
                    </div>
                    <?php if ($_SESSION["login"] != "dependiente"): ?>
                        <div class="row">
                            <h2 class="display-5">Técnico</h2>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <textarea rows="3" style="height:100%;" class="form-control" name="desc_tecnico" id="desc_tecnico"><?php echo $ticket->desc_tecnico; ?></textarea>
                                    <label for="desc_tecnico">Descripción técnico</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" id="insumo">
                                <?php
                                $insumos = $db->fetchInsumos($ticket->id);
                                foreach ($insumos as $i => $row) {
                                ?>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Insumo</span>
                                        <div class="form-floating">
                                            <input class="form-control" placeholder="Descripción" type="text" name="insumo_desc<?php echo $i + 1; ?>" id="insumo_desc<?php echo $i + 1; ?>" value="<?php echo $row["nombre"]; ?>">
                                            <label for="insumo_desc">Descripción</label>
                                        </div>

                                        <div class="form-floating">
                                            <input <?php if ($_SESSION["login"] == "tecnico") {
                                                        echo "readonly";
                                                    } ?> class="form-control" placeholder="Precio" type="number" step=.01 name="insumo_precio<?php echo $i + 1; ?>" id="insumo_precio<?php echo $i + 1; ?>" value="<?php echo isset($row["precio"]) ? $row["precio"] : 0; ?>">
                                            <label for="insumo_precio">Precio</label>
                                            <input type="hidden" name="insumo_estado<?php echo $i + 1; ?>" value="<?php echo $row["estado"]; ?>">
                                        </div>
                                    </div>
                                <?php
                                }
                                if (!$insumos) {
                                ?>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Insumo</span>
                                        <div class="form-floating">
                                            <input class="form-control" placeholder="Descripción" type="text" name="insumo_desc1" id="insumo_desc1">
                                            <label for="insumo_desc">Descripción</label>
                                        </div>

                                        <div class="form-floating">
                                            <input <?php if ($_SESSION["login"] == "tecnico") {
                                                        echo "readonly";
                                                    } ?> class="form-control" placeholder="Precio" type="number" step=.01 name="insumo_precio1" id="insumo_precio1">
                                            <label for="insumo_precio">Precio</label>
                                            <input type="hidden" name="insumo_estado1" value="0">
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mx-auto">
                                <button type="button" class="btn btn-secondary" onclick="crear()">+ Añadir Insumo</button>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <input type="submit" name="editar_insumo" class="btn btn-primary col-4 mx-auto" value="Guardar Cambios">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- COBRAR -->
<div class="modal fade" id="cobrarModal" tabindex="-1" aria-labelledby="cobrarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="cobrarModalLabel">Cerrar Ticket (# <?php echo $ticket->id ?>)</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="pag" value="1">
                    <input type="hidden" name="id" value="<?php echo $ticket->id ?>">
                    <p>
                        <label for="fecha_pago">Fecha de pago</label>
                        <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" value="<?php echo date("Y-m-d") ?>">
                    </p>
                    <p>
                        <label for="fotoFinal">Foto final</label>
                        <input type="file" id="fotoFinal" accept="image/*" name="images[]" capture="environment" multiple class="form-control" required>
                        <input type="checkbox" name="noFoto" id="noFoto" onclick="document.getElementById('fotoFinal').toggleAttribute('required')"> <label for="noFoto">Sin foto final</label>
                    </p>
                    <p>
                        <label for="precio">Precio final</label>
                        <input disabled type="number" name="precio" id="precio" class="form-control" value="<?php echo $ticket->precio_final; ?>">
                    </p>
                    <!-- METODO DE PAGO -->
                    <div class="radio-buttons-container">
                        <div class="radio-button mx-auto">
                            <input name="metodo" id="radio2" class="radio-button__input" type="radio" value="Tarjeta" checked>
                            <label for="radio2" class="radio-button__label">
                                <span class="radio-button__custom"></span>

                                Tarjeta
                            </label>
                        </div>
                        <div class="radio-button mx-auto">
                            <input name="metodo" id="radio1" class="radio-button__input" type="radio" value="Efectivo">
                            <label for="radio1" class="radio-button__label">
                                <span class="radio-button__custom"></span>

                                Efectivo
                            </label>
                        </div>
                    </div>
                    <button class="cssbuttons-io-button bg-secondary mx-auto" type="submit" name="estado" value="4">Entregado/Cobrar<div class="icon"><i class="bi bi-cash text-dark"></i></div></button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- HISTORIAL -->
<div class="modal modal-lg fade" id="historialModal" tabindex="-1" aria-labelledby="historialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="historialModalLabel">Historial de cambios (# <?php echo $ticket->id ?>)</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                $pdo = $db->pdo;
                $stmt = $pdo->prepare("SELECT * FROM historial_cambios WHERE `id_orden` = :id ORDER BY `id` DESC");
                $stmt->bindParam(":id", $_GET["id"]);
                $stmt->execute();
                if ($stmt->rowCount()) {
                ?>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>#</th>
                                <th>Cambio</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Loop through the results and execute the second query to get the order count for each codigo_socio
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                            // Display the results in a table row
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row["usuario"]) . '</td>';
                            echo '<td><a style="color:rgb(37,190,212)" href="list&id=' . $row["id_orden"] . '">' . htmlspecialchars($row["id_orden"]) . '</a></td>';
                            echo '<td>' . htmlspecialchars($row["descripcion"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["fecha"]) . '</td>';
                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo "No hay cambios aún.";
                    }
                        ?>
            </div>
        </div>
    </div>
</div>
<script>
    function crear() {
        var insumo = document.getElementById("insumo");
        var clone = insumo.children[0].cloneNode(true);
        var num = insumo.children.length;
        clone.innerHTML = '<span class="input-group-text">Insumo</span>' +
            '<div class="form-floating">' +
            '<input class="form-control" placeholder="Descripción" type="text" name="insumo_desc' + (num + 1) + '" id="insumo_desc' + (num + 1) + '">' +
            '<label for="insumo_desc">Descripción</label>' +
            '</div>' +
            '<div class="form-floating">' +
            '<input <?php if ($_SESSION["login"] == "tecnico") {
                        echo "readonly";
                    } ?> class="form-control" placeholder="Precio" type="number" step=.01 name="insumo_precio' + (num + 1) + '" id="insumo_precio' + (num + 1) + '" value=0>' +
            '<label for="insumo_precio">Precio</label>' +
            '<input type="hidden" name="insumo_estado' + (num + 1) + '" value="0">'+
            '</div>';
        insumo.appendChild(clone);
    }

    var loadSign = true;
    // jSignature
    function loadSignature() {
        if (loadSign) {
            loadSign = false;
            var $sigdiv = $("#signature").jSignature();
            var height = $(".jSignature").width() / 2.5;
            $(".jSignature").height(height);
            $(".jSignature").attr("height", height);
            $("#clear").click(borrar());
            borrar();
        }

        function borrar() {
            event.preventDefault(); // Prevent form submission
            $sigdiv.jSignature("reset");
        }

        function saveSignature() {
            event.preventDefault(); // Prevent form submission
            var data = $sigdiv.jSignature("getData");
            console.log(data);
            $("#sign").val(data); // Store it in a hidden field
        }

        // Trigger save on mouseup
        $("#signature").on("mouseup", function() {
            saveSignature();
        });

        // Optional: Trigger save on touchend for mobile support
        $("#signature").on("touchend", function() {
            saveSignature();
        });
    };

    function editField(parent, data, name) {
        parent.innerHTML = "<input type='text' name='" + name + "' value='" + data + "'>";
        var btn = document.getElementById("guardarCambiosBtn");
        btn.classList = "row mt-4";
    }
    // CÁLCULOS IVA
    function findTotal() {
        var precio = parseFloat(document.getElementById('precio').value);
        var iva = parseFloat(document.getElementById('iva').value);
        var final = parseFloat(document.getElementById('precio-final').value);
        var descuento = parseFloat(document.getElementById('descuento').value);
        let calc = precio - ((precio * descuento) / 100);
        calc = calc + (calc * (iva / 100));
        document.getElementById('precio-final').value = calc.toFixed(2);
    }

    function findPrecio() {
        var precio = parseFloat(document.getElementById('precio').value);
        var iva = parseFloat(document.getElementById('iva').value);
        var final = parseFloat(document.getElementById('precio-final').value);
        let calc = (final / (100 + iva)) * 100;
        document.getElementById('precio').value = calc.toFixed(2);
    }
</script>