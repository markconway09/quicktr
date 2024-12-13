        <div class="container border my-4 px-4 bg-dark rounded">
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
                <div class="input-group sticky-top mt-3 py-2 bg-dark d-flex" style="top: 50px; z-index: 1;">
                    <a href="?pag=list" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Volver</span>
                    </a>
                    <a href="?pag=formulario&form=nuevo&id=<?php echo $id; ?>" class="btn flex-fill" 
                    style="background-color: #6c0892; color: white; transition: background-color 0.3s;" 
                    onmouseover="this.style.backgroundColor='#550673';" 
                    onmouseout="this.style.backgroundColor='#6c0892';">
                        <i class="bi bi-plus"></i> <span class="d-none d-sm-inline">Autorellenar</span>
                    </a>
                    <a href="controller/execute.php?ticketservicio=1&id=<?php echo $id; ?>" target="_blank" class="btn btn-primary flex-fill">
                        <i class="bi bi-receipt-cutoff"></i> <span class="d-none d-sm-inline">Imprimir Ticket</span>
                    </a>
                    <a href="controller/execute.php?servicio=1&id=<?php echo $id; ?>" target="_blank" class="btn flex-fill"
                    style="background-color: #2596be; color: white; transition: background-color 0.3s;" 
                    onmouseover="this.style.backgroundColor='#277f9e';" 
                    onmouseout="this.style.backgroundColor='#2596be';">
                        <i class="bi bi-receipt-cutoff"></i> <span class="d-none d-sm-inline">Imprimir Factura</span>
                    </a>
                    <a href="?pag=formulario&form=garantia&id=<?php echo $id; ?>" class="btn flex-fill" 
                    style="background-color: #007c7a; color: white; transition: background-color 0.3s;" 
                    onmouseover="this.style.backgroundColor='#006d6b';" 
                    onmouseout="this.style.backgroundColor='#007c7a';">
                        <i class="bi bi-file-text"></i> <span class="d-none d-sm-inline">Garantía</span>
                    </a>
                    <button type="button" class="btn flex-fill" 
                            style="background-color: #fd7e14; color: white; transition: background-color 0.3s;" 
                            onmouseover="this.style.backgroundColor='#e68a00';" 
                            onmouseout="this.style.backgroundColor='#fd7e14';" 
                            data-bs-toggle="modal" data-bs-target="#enviarModal">
                        <i class="bi bi-envelope-at"></i> <span class="d-none d-sm-inline">Reenviar</span>
                    </button>
                    <a href="?pag=formulario&form=edit&id=<?php echo $id; ?>" class="btn btn-success flex-fill">
                        <i class="bi bi-pencil-square"></i> <span class="d-none d-sm-inline">Editar</span>
                    </a>
                    <?php if (!empty($row["did"])) { ?>
                        <a href="controller/execute.php?deshacer=1&id=<?php echo $id; ?>" class="btn btn-danger flex-fill">
                            <i class="bi bi-arrow-counterclockwise"></i> <span class="d-none d-sm-inline">Deshacer devolución</span>
                        </a>
                    <?php } else { ?>
                        <a href="controller/execute.php?devolucion=1&id=<?php echo $id; ?>" class="btn btn-danger flex-fill">
                            <i class="bi bi-arrow-counterclockwise"></i> <span class="d-none d-sm-inline">Devolución</span>
                        </a>
                    <?php } ?>
                    <?php if ($_SESSION["login"] == "admin") { ?>
                        <button type="button" class="btn flex-fill" 
                                style="background-color: #c82333; color: white; transition: background-color 0.3s;" 
                                onmouseover="this.style.backgroundColor='#a71c2b';" 
                                onmouseout="this.style.backgroundColor='#c82333';" 
                                data-bs-toggle="modal" data-bs-target="#elimModal">
                            <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Eliminar</span>
                        </button>
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
                if($row["garantia"] != 0) $garantia = " | <i class='bi bi-file-text'></i> <a style='text-decoration:none;color:#FFA' href='?pag=list&id=".$row["garantia"]."'>GARANTÍA <i class='bi bi-arrow-right-short'></i></a>";
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
                                <?php if(($row["estado"] < 4 && $_SESSION["login"]=="tecnico")||($_SESSION["login"]=="dependiente"&&$row["estado"]==4)||($_SESSION["login"]=="dependiente"&&$row["estado"]==2)||($_SESSION["login"]=="dependiente"&&$row["estado"]==1)){ if(($row["estado"]-1)>=0){ ?><a href="controller/execute.php?estado=<?php echo $row["estado"]-1 ?>&id=<?php echo $row["id"] ?>&pag=1" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]-1] ?>"><i class="bi bi-caret-left-fill"></i> <?php echo $pasos[$row["estado"]-1] ?></a><?php }} ?>
                                <?php if(($row["estado"] < 3 && $_SESSION["login"]=="tecnico")||($_SESSION["login"]=="dependiente"&&$row["estado"]==0)||($_SESSION["login"]=="dependiente"&&$row["estado"]==1)){ if(($row["estado"]+1)<5){ ?><a href="controller/execute.php?estado=<?php echo $row["estado"]+1 ?>&id=<?php echo $row["id"] ?>&pag=1" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]+1] ?>"><?php echo $pasos[$row["estado"]+1] ?> <i class="bi bi-caret-right-fill"></i></a><?php }} ?>
                                <?php if($_SESSION["login"]=="admin"){ if(($row["estado"]-1)>=0){ ?><a href="controller/execute.php?estado=<?php echo $row["estado"]-1 ?>&id=<?php echo $row["id"] ?>&pag=1" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]-1] ?>"><i class="bi bi-caret-left-fill"></i> <?php echo $pasos[$row["estado"]-1] ?></a><?php }} ?>
                                <?php if($_SESSION["login"]=="admin"){ if(($row["estado"]+1)<4){ ?><a href="controller/execute.php?estado=<?php echo $row["estado"]+1 ?>&id=<?php echo $row["id"] ?>&pag=1" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]+1] ?>"><?php echo $pasos[$row["estado"]+1] ?> <i class="bi bi-caret-right-fill"></i></a><?php }} ?>
                                <?php if((($_SESSION["login"]=="admin"||$_SESSION["login"]=="dependiente"))&&$row["estado"]!=4){ ?>
                                    <form action="controller/execute.php" class="m-2" method="get">
                                        <input type="hidden" name="pag" value="1">
                                        <input type="hidden" name="id" value="<?php echo $row["id"] ?>">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="metodo" id="metodoTarjeta" value="Tarjeta" checked>
                                            <label class="form-check-label" for="metodoTarjeta">
                                                Tarjeta
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="metodo" id="metodoEfectivo" value="Efectivo">
                                            <label class="form-check-label" for="metodoEfectivo">
                                                Efectivo
                                            </label>
                                        </div><br>
                                        <button class="btn btn-secondary" type="submit" name="estado" value="4">Entregado/Cobrar</button>
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
                                            <?php if($_SESSION["login"] == "admin") echo ' <br><a href="index.php?pag=edit_insumo&id='.$id.'" class="btn btn-primary">Editar insumo y descripción técnico</a>'; ?>
                                            <?php if($_SESSION["login"] == "tecnico") echo ' <br><a href="index.php?pag=edit_insumo&id='.$id.'" class="btn btn-primary">Editar descripción técnico</a>'; ?>
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
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fotos">Añadir fotos</a>
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
<!-- ELIMINAR -->
            <div class="modal fade" id="elimModal" tabindex="-1" aria-labelledby="elimModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="elimModalLabel">Eliminar esta factura? (# <?php echo $row["id"] ?>)</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="GET" action="controller/execute.php">
                                <input type="hidden" name="id" value="<?php echo $row["id"] ?>">
                                <input class="btn btn-danger" type="submit" name="eliminar" id="eliminar" value="Eliminar">
                                <button type="button" class="btn btn-secondary"data-bs-dismiss="modal">Cancelar</button>
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
            <div class="row">
                <ul class="nav nav-tabs bg-dark mt-2">
                    <?php
                        $filters = [
                            "0" => "Diagnóstico",
                            "1" => "Aprobación",
                            "2" => "Reparación",
                            "3" => "Terminado",
                            "4" => "Entregado",
                            "5" => "Garantía",
                            "6" => "Devoluciones",
                        ];

                        $tooltip = [
                            "Pendiente de diagnosticar el problema y dar presupuesto.",
                            "Esperando la aprobación del cliente.",
                            "Ticket aprobado, pendiente de reparación.",
                            "Reparación finalizada, esperando al cliente.",
                            "Ticket cerrado, entregado al cliente.",
                            "",
                            ""
                        ];
                        
                        // Default case for "Todo"
                        echo '<li class="nav-item"><a class="nav-link ' . (!isset($_GET["filter"]) ? 'active' : 'text-light') . '" href="?pag=list">Todo</a></li>';

                        // Iterate through filters
                        foreach ($filters as $key => $label) {
                            $activeClass = (isset($_GET["filter"]) && $_GET["filter"] == $key) ? 'active' : 'text-light';
                            echo '<li class="nav-item"><a class="nav-link ' . $activeClass . '" href="?pag=list&filter=' . $key . '"
                            data-bs-toggle="tooltip" data-bs-title="'.$tooltip[$key].'">' . $label . '</a></li>';
                        }
                        if($_SESSION["login"] == "admin" || $_SESSION["login"] == "repartidor"){
                    ?>
                    <li class="nav-item"><a class="nav-link" href="?pag=entregas">Entregas</a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="row">
                <div class="col-12 mt-2">
                    <form action="?pag=list" method="POST">
                        <div class="row">
                            <div class="col-12 my-2">
                                <div class="input-group">
                                    <div class="form-floating">
                                        <input type="text" placeholder="Buscar... (Dispositivo, Id, Servicio...)" name="search" id="search" class="form-control ">
                                        <label for="search">Buscar... (Dispositivo, Id, Servicio...)</label>
                                    </div>
                                    <button type="submit" id="submit" class="btn btn-primary p-3"><i class="bi bi-search"></i> Buscar</button>
                                    <?php if(isset($_POST["search"])){?>
                                        <a href="?pag=list" class="btn btn-secondary btn-block p-3"><i class="bi bi-x-circle"></i> Quitar filtro</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div></i>

            <?php
            // Get current page number
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $cardsPerPage = $_SESSION["pag"];
            $offset = ($page - 1) * $cardsPerPage;
            $query_end = " ORDER BY i.id DESC LIMIT $cardsPerPage OFFSET $offset";
            if($cardsPerPage == 0) $query_end = " ORDER BY i.id DESC";

            // Database connection
            $pdo = connect();
            $sql = "SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, fecha as `date` FROM info_orden i
                    LEFT JOIN devolucion d ON (i.id = d.id_orden)";

            $localQuery = "";
            if ($_SESSION["local"] != null){
                $localQuery = " AND `local` = '".$_SESSION["local"]."'";
            }

            // Create the query based on filters
            if(!isset($_POST["search"])){
                if(!isset($_GET["filter"])){
                    if($_SESSION["local"] != null){
                        $stmt = $pdo->prepare($sql."WHERE `local` = '".$_SESSION["local"]."'".$query_end);
                    } else {
                        $stmt = $pdo->prepare($sql.$query_end);
                    }
                } else {
                    if($_GET["filter"] == 5){
                        $stmt = $pdo->prepare($sql." WHERE `garantia` != 0 AND d.id IS NULL $localQuery $query_end");
                    } else if($_GET["filter"] == 6){
                        $stmt = $pdo->prepare($sql." WHERE d.id IS NOT NULL $localQuery $query_end");
                    } else {
                        $stmt = $pdo->prepare($sql." WHERE `estado` = :estado AND d.id IS NULL $localQuery $query_end");
                        $stmt->bindParam(':estado', $_GET["filter"]);
                    }
                }
            } else {
                echo '<p class="display-5 text-light">Resultados para <i>\''.$_POST["search"].'\'</i></p>';
                $search = "%".$_POST["search"]."%";
                $stmt = $pdo->prepare($sql." WHERE `nombre_dispositivo` LIKE :search OR i.id LIKE :search OR
                `nombre` LIKE :search OR `local` LIKE :search OR `servicio` LIKE :search $localQuery ORDER BY i.id DESC");
                $stmt->bindParam(':search', $search);
            }

            // Execute the query to fetch results
            $stmt->execute();

            // Fetch the total number of rows (separate query for count)
            $totalQuery = "SELECT COUNT(*) FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden)";
            if (isset($_GET["filter"])) {
                if ($_GET["filter"] == 5) {
                    $totalQuery .= " WHERE `garantia` != 0 AND d.id IS NULL $localQuery";
                } else if ($_GET["filter"] == 6) {
                    $totalQuery .= " WHERE d.id IS NOT NULL $localQuery";
                } else {
                    $totalQuery .= " WHERE `estado` = :estado AND d.id IS NULL $localQuery";
                }
            } else {
                if($_SESSION["local"] != null) $totalQuery .= "WHERE `local` = '".$_SESSION["local"]."'";
            }
            $totalStmt = $pdo->prepare($totalQuery);
            if (isset($_GET["filter"]) && $_GET["filter"] != 5 && $_GET["filter"] != 6) {
                $totalStmt->bindParam(':estado', $_GET["filter"]);
            }
            $totalStmt->execute();
            $totalRows = $totalStmt->fetchColumn();
            if($cardsPerPage != 0) $totalPages = ceil($totalRows / $cardsPerPage);
            else $totalPages = 0;
            ?>
            <?php

            // Pagination controls
            if(!isset($_POST["search"])){
                $filter = "";
                if (isset($_GET["filter"])) {
                    $filter = "&filter=" . $_GET["filter"];
                }

                // Calculate total range dynamically
                $from = ($offset + 1) > $totalRows ? $totalRows : ($offset + 1);
                $to = ($offset + $cardsPerPage) > $totalRows ? $totalRows : ($offset + $cardsPerPage);
                if($cardsPerPage == 0) $to = $totalRows;

                echo "<div class='text-light'><i>Mostrando $from - $to de $totalRows</i>&nbsp;";
                ?>
                <select id="pagSelect" onchange="updatePages(this.value)">
                    <option value=9 <?php echo $_SESSION["pag"]==9?'selected':''?>>9</option>
                    <option value=15 <?php echo $_SESSION["pag"]==15?'selected':''?>>15</option>
                    <option value=30 <?php echo $_SESSION["pag"]==30?'selected':''?>>30</option>
                    <option value=0 <?php echo $_SESSION["pag"]==0?'selected':''?>>Todo</option>
                </select></div>
                <?php
                if($cardsPerPage != 0){
                    echo '<div class="pagination d-flex">';

                    // Previous Page Button
                    if ($page > 1) {
                        echo '<div class="w-100 p-1"><a href="?pag=list' . $filter . '&page=' . ($page - 1) . '" class="w-100 btn btn-secondary">Anterior</a></div>';
                    } else {
                        echo '<div class="w-100 p-1"><a class="disabled w-100 btn btn-secondary">Anterior</a></div>';
                    }

                    // Adjust the range dynamically
                    $totalButtons = 5; // Total number of buttons (including current page)
                    $startPage = max(1, $page - 2);
                    $endPage = $startPage + $totalButtons - 1;

                    // If endPage exceeds totalPages, shift the range back
                    if ($endPage > $totalPages) {
                        $endPage = $totalPages;
                        $startPage = max(1, $endPage - $totalButtons + 1);
                    }

                    // Generate Page Buttons
                    for ($i = $startPage; $i <= $endPage; $i++) {
                        if ($i == $page) {
                            // Current Page (disabled)
                            echo '<div class="p-1"><a class="disabled btn btn-primary w-100">' . $i . '</a></div>';
                        } else {
                            // Other Pages
                            echo '<div class="p-1"><a href="?pag=list' . $filter . '&page=' . $i . '" class="btn btn-secondary w-100">' . $i . '</a></div>';
                        }
                    }

                    // Next Page Button
                    if ($page < $totalPages) {
                        echo '<div class="w-100 p-1"><a href="?pag=list' . $filter . '&page=' . ($page + 1) . '" class="w-100 btn btn-secondary">Siguiente</a></div>';
                    } else {
                        echo '<div class="w-100 p-1"><a class="disabled w-100 btn btn-secondary">Siguiente</a></div>';
                    }

                    echo '</div>'; // Close pagination div
                }
            }

            // Start looping over the rows
            $i = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // User restrictions
                //if ($_SESSION["local"] != null && $_SESSION["local"] != $row["local"]) continue;
            
                // Card layout start
                if ($i == 0) echo '<div class="row">';
            
                // Prepare description
                $desc = strlen($row["desc"]) > 22 ? substr($row["desc"], 0, 22) . '...' : $row["desc"];
                
                // Determine background and status
                $bg = "text-bg-secondary";
                $estado = "";
                if (!empty($row["did"])) {
                    $bg = "text-bg-dark";
                    $estado = " | <i class='bi bi-arrow-counterclockwise'></i> DEVUELTO";
                } elseif ($row["garantia"] != 0) {
                    $estado = " | <i class='bi bi-file-text'></i> <a style='text-decoration:none;color:#FFA' href='?pag=list&id=" . $row["garantia"] . "'>GARANTÍA <i class='bi bi-arrow-right-short'></i></a>";
                } else {
                    $estado = " | " . $pasos[$row["estado"]];
                }
            
                // Prepare service information
                if (!empty($row["servicio"])) {
                    $serv = explode(": ", $row["servicio"])[0];
                } else {
                    $serv = "PENDIENTE MODIFICAR";
                }

                $statusColor = !empty($row["did"])?"black":$colores[$row["estado"]];
                
                // Generate card HTML
                echo '
                    <div class="col-lg-4 col-12">
                        <div class="card ' . $bg . ' my-3">
                            <h5 class="card-header py-3" style="color:white;background-color:' . $statusColor . ';">' . $serv . ' # ' . $row["id"] . '<br><div style="display:inline;margin-right:2px;border-left: 3px solid ' . $localColor[$row["local"] == "Barcelona" ? 0 : 1] . ';height: 5px;"></div>' . $row["local"] . $estado . '</h5>';
            
                // Display status steps
                echo '
                            <div class="text-center pt-2">
                                <b>' . $pasosLargo[$row["estado"]] . '</b><br>';
                foreach ($colores as $key => $color) {
                    echo '<div class="col-2 pt-2 d-inline-block" style="background-color:' . ($row["estado"] >= $key ? $color : "white") . '"></div>';
                }
                echo '
                            </div>
                            <div class="text-center mx-auto">';
                for ($j = 0; $j <= 4; $j++) {
                    echo '<i class="bi bi-caret-up-fill px-4" style="color:' . ($row["estado"] != $j ? 'rgba(0,0,0,0)' : 'inherit') . '"></i>';
                }
                $pastDate = new DateTime($row["date"]);
                $now = new DateTime();
                $daysPassed = $now->diff($pastDate)->days;

                $index = $daysPassed > 4 ? 4 : $daysPassed;
                $colD = $row["estado"] < 4 ? (empty($row["did"]) ? $colorDias[$index] : "white") : "white";
                echo '
                            </div>
                            <div class="card-body">
                                <p class="card-text"><b>Nombre:</b> ' . $row["nombre"] . '</p>
                                <p class="card-text"><b>Dispositivo:</b> ' . $row["nombre_dispositivo"] . '</p>
                                <p class="card-text"><b>Descripción:</b> ' . $desc . '</p>
                                <p class="card-text date-highlight">' . $row["fecha"] . ' · <span style="color:'.$colD.'">hace ' . $daysPassed . ' día(s)</span></p>
                            </div> 
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item ' . $bg . '"><b>Precio:</b> ' . $row["precio"] . '€ (+ IVA ' . $row["iva"] . '%) = <b>' . $row["precio-final"] . '€</b></li>
                            </ul>
                            <div class="card-body">';
            
                // Action buttons based on user role and ticket state
                echo '<div class="w-100">'; // Start full-width container for buttons
                    // Create an input group for the buttons
                    echo '<div class="input-group mb-2">';
                    // Left button (state -1)
                    if (($_SESSION["login"] == "tecnico" && $row["estado"] < 4) || ($_SESSION["login"] == "dependiente" && in_array($row["estado"], [1, 2, 4]))) {
                        if (($row["estado"] - 1) >= 0) {
                            echo '<a href="controller/execute.php?estado=' . ($row["estado"] - 1) . '&id=' . $row["id"] . '&pag=0" class="btn text-light rounded-0 w-50" style="background-color:' . $colores[$row["estado"] - 1] . '"><i class="bi bi-caret-left-fill"></i> ' . $pasos[$row["estado"] - 1] . '</a>';
                        } else {
                            echo '<div class="btn text-light w-50" style="background-color:transparent; opacity:0;"></div>'; // Placeholder
                        }
                    } else {
                        echo '<div class="btn text-light w-50" style="background-color:transparent; opacity:0;"></div>'; // Placeholder
                    }
                    // Right button (state +1)
                    if (($_SESSION["login"] == "tecnico" && $row["estado"] < 3) || ($_SESSION["login"] == "dependiente" && $row["estado"] < 2)) {
                        if (($row["estado"] + 1) < 5) {
                            echo '<a href="controller/execute.php?estado=' . ($row["estado"] + 1) . '&id=' . $row["id"] . '&pag=0" class="btn text-light rounded-0 w-50" style="color:black; background-color:' . $colores[$row["estado"] + 1] . '">' . $pasos[$row["estado"] + 1] . ' <i class="bi bi-caret-right-fill"></i></a>';
                        } else {
                            echo '<div class="btn text-light w-50" style="background-color:transparent; opacity:0;"></div>'; // Placeholder
                        }
                    } else {
                        echo '<div class="btn text-light w-50" style="background-color:transparent; opacity:0;"></div>'; // Placeholder
                    }
                    // Admin buttons
                    if ($_SESSION["login"] == "admin") {
                        if (($row["estado"] - 1) >= 0) {
                            echo '<a href="controller/execute.php?estado=' . ($row["estado"] - 1) . '&id=' . $row["id"] . '&pag=0" class="btn text-light rounded-0 w-50" style="color:black; background-color:' . $colores[$row["estado"] - 1] . '"><i class="bi bi-caret-left-fill"></i> ' . $pasos[$row["estado"] - 1] . '</a>';
                        } else {
                            echo '<div class="btn text-light w-50" style="background-color:transparent; opacity:0;"></div>'; // Placeholder
                        }
                        if (($row["estado"] + 1) < 5) {
                            echo '<a href="controller/execute.php?estado=' . ($row["estado"] + 1) . '&id=' . $row["id"] . '&pag=0" class="btn text-light rounded-0 w-50" style="color:black; background-color:' . $colores[$row["estado"] + 1] . '">' . $pasos[$row["estado"] + 1] . ' <i class="bi bi-caret-right-fill"></i></a>';
                        } else {
                            echo '<div class="btn text-light w-50" style="background-color:transparent; opacity:0;"></div>'; // Placeholder
                        }
                    }
                    // Close the input group
                    echo '</div>'; // End of input group
                    echo '
                                <a href="?pag=list&id=' . $row["id"] . '" class="btn btn-primary rounded-0 w-100"><i class="bi bi-info-circle"></i> Detalles</a>
                            </div>'; // Close full-width container for buttons
                    echo '
                            </div>
                        </div>
                    </div>';
                
                // Close row after 3 columns
                if ($i == 2) {
                    echo '</div>';
                    $i = 0;
                } else {
                    $i++;
                }
            }

            ?>
        </div>
        <script>
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            function updatePages(selectedValue) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "index.php?pag=list", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send("valuePag=" + encodeURIComponent(selectedValue));
            }
        </script>