        <div class="container border my-4 px-4 py-2 bg-dark rounded">
            <?php
            $pasos = ["Diagnóstico", "Aprobación", "Reparación", "Terminado", "Entregado"];
            $pasosLargo = ["Espera del diagnóstico", "Espera aprobación del cliente", "En Reparación", "Reparación terminada", "Entregado al cliente"];
            $colores = ["#f54254", "#e8a31a", "#2f852c", "#4472c4", "#adadad"];
            $localColor = ["blue", "red"];
            
            if(isset($_GET["id"])){
                $id = $_GET["id"];
                $row = selectBD($id);
                
                $desc = '<b>Descripción:</b> '.$row["desc"];
                $precios = "";
                $cant = "";
                ?>
                <div class="input-group mt-3">
                    <a href="?pag=list" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
                    <a href="execute.php?ticketservicio=1&id=<?php echo $id; ?>" target="_blank" class="btn btn-primary"><i class="bi bi-ticket-detailed"></i> Imprimir Ticket</a>
                    <a href="?pag=garantia&id=<?php echo $id; ?>" class="btn" 
                    style="background-color: #007c7a; color: white; transition: background-color 0.3s;" 
                    onmouseover="this.style.backgroundColor='#006d6b';" 
                    onmouseout="this.style.backgroundColor='#007c7a';">
                        <i class="bi bi-file-text"></i> Garantia
                    </a>
                    <button type="button" class="btn" 
                    style="background-color: #fd7e14; color: white; transition: background-color 0.3s;" 
                    onmouseover="this.style.backgroundColor='#e68a00';" 
                    onmouseout="this.style.backgroundColor='#fd7e14';" 
                    data-bs-toggle="modal" data-bs-target="#enviarModal">
                        <i class="bi bi-send"></i> Enviar
                    </button>
                    <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-success"><i class="bi bi-pencil-square"></i> Editar</a>
                    <?php if (!empty($row["did"])) { ?>
                        <a href="execute.php?deshacer=1&id=<?php echo $id; ?>" class="btn btn-danger"><i class="bi bi-arrow-counterclockwise"></i> Deshacer devolución</a>
                    <?php } else { ?>
                        <a href="execute.php?devolucion=1&id=<?php echo $id; ?>" class="btn btn-danger"><i class="bi bi-arrow-counterclockwise"></i> Devolución</a>
                    <?php } ?>
                    <?php if ($_SESSION["login"] == "admin") { ?>
                        <button type="button" class="btn" 
                        style="background-color: #c82333; color: white; transition: background-color 0.3s;" 
                        onmouseover="this.style.backgroundColor='#a71c2b';" 
                        onmouseout="this.style.backgroundColor='#c82333';" 
                        data-bs-toggle="modal" data-bs-target="#elimModal">
                            <i class="bi bi-trash"></i> Eliminar
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
                    $ser = explode(": ", $row["servicio"])[0];
                    $servicio .= '<p class="card-text"><b>Tipo de servicio:</b> '.$ser.'</p>';
                    $servicio .= '<p class="card-text"><b>Servicio:</b> '.explode(": ", $row["servicio"])[1].'</p>';
                } else{
                    $ser = "PENDIENTE MODIFICAR";
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
                if($_SESSION["login"] == "tecnico" || $_SESSION["login"] == "admin") $ins .= ' <a href="index.php?pag=edit_insumo&id='.$id.'" class="btn btn-primary">Editar</a>';
                $ins .= '</li>';
                $garantia = "";
                if($row["garantia"] != 0) $garantia = " | <i class='bi bi-file-text'></i> <a style='text-decoration:none;color:#FFA' href='?pag=list&id=".$row["garantia"]."'>GARANTÍA <i class='bi bi-arrow-right-short'></i></a>";
                echo '<div class="col-12">
                            <div class="card my-3">
                                <h5 class="card-header py-3" style="color:white;background-color:'.$colores[$row["estado"]].';">'.$ser.' # '.$row["id"].$garantia.'</h5>
                                <div class="card-body">
                                    '.$estado;?>
                                <br>
                                <?php if(($row["estado"] < 4 && $_SESSION["login"]=="tecnico")||($_SESSION["login"]=="dependiente"&&$row["estado"]==4)||($_SESSION["login"]=="dependiente"&&$row["estado"]==2)||($_SESSION["login"]=="dependiente"&&$row["estado"]==1)){ if(($row["estado"]-1)>=0){ ?><a href="execute.php?estado=<?php echo $row["estado"]-1 ?>&id=<?php echo $row["id"] ?>&pag=1" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]-1] ?>"><i class="bi bi-caret-left-fill"></i> <?php echo $pasos[$row["estado"]-1] ?></a><?php }} ?>
                                <?php if(($row["estado"] < 3 && $_SESSION["login"]=="tecnico")||($_SESSION["login"]=="dependiente"&&$row["estado"]==0)||($_SESSION["login"]=="dependiente"&&$row["estado"]==1)){ if(($row["estado"]+1)<5){ ?><a href="execute.php?estado=<?php echo $row["estado"]+1 ?>&id=<?php echo $row["id"] ?>&pag=1" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]+1] ?>"><?php echo $pasos[$row["estado"]+1] ?> <i class="bi bi-caret-right-fill"></i></a><?php }} ?>
                                <?php if($_SESSION["login"]=="admin"){ if(($row["estado"]-1)>=0){ ?><a href="execute.php?estado=<?php echo $row["estado"]-1 ?>&id=<?php echo $row["id"] ?>&pag=1" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]-1] ?>"><i class="bi bi-caret-left-fill"></i> <?php echo $pasos[$row["estado"]-1] ?></a><?php }} ?>
                                <?php if($_SESSION["login"]=="admin"){ if(($row["estado"]+1)<4){ ?><a href="execute.php?estado=<?php echo $row["estado"]+1 ?>&id=<?php echo $row["id"] ?>&pag=1" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]+1] ?>"><?php echo $pasos[$row["estado"]+1] ?> <i class="bi bi-caret-right-fill"></i></a><?php }} ?>
                                <?php if(($_SESSION["login"]=="admin"||$_SESSION["login"]=="dependiente")&&$row["estado"]==3){ ?>
                                    <form action="execute.php" method="get">
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
                                        </div>
                                        <button class="btn btn-secondary" type="submit" name="estado" value="4">Entregado/Cobrar</button>
                                    </form>
                                <?php } ?>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <p class="card-text"><b>Nombre:</b> <?php echo $row["nombre"] ?></p>
                                            <p class="card-text"><b>Documento:</b> <?php echo $row["documento"] ?></p>
                                            <p class="card-text"><b>Email:</b> <?php echo $row["email"]?></p>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <p class="card-text"><b>Dirección:</b> <?php echo $row["direccion"]." - ".$row["cp"] ?></p>
                                            <p class="card-text"><b>Teléfono:</b> <a href="https://wa.me//<?php echo $row["telefono"] ?>" target="_blank"><?php echo $row["telefono"]?><a></p>
                                            <p class="card-text"><b>Cómo nos encontró:</b> <?php echo $row["razon"] ?></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <p class="card-text"><b>Fecha (d/m/y):</b> <?php echo $row["fecha"] ?></p>
                                            <p class="card-text"><b>Fecha de pago:</b> <?php echo $row["fecha_pago"] ?></p>
                                            <p class="card-text"><b>Local:</b> <?php echo $row["local"] ?></p>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <?php echo $servicio ?>
                                            <p class="card-text"><b>Departamento:</b> <?php echo $row["dept"] ?></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <p class="card-text"><b>Dispositivo:</b> <?php echo $row["nombre_dispositivo"] ?></p>
                                            <p class="card-text"><?php echo $desc ?></p>
                                            <p class="card-text"><b>Método de pago:</b> <?php echo $row["metodo"] ?></p>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <?php if(!empty($row["firma"])) echo '<img src="'. $row["firma"] .'" alt="firma">';?>
                                        </div>
                                    </div>
                                </div>
                                <ul class="list-group list-group-flush mb-3">
                                    <?php echo $_SESSION["login"] != "dependiente" ? $ins : "<hr>" ?>
                                    <li class="list-group-item"><b>Precio:</b> <?php echo $row["precio"] ?>€ (- <?php echo $row["descuento"] ?>%) (+ IVA <?php echo $row["iva"] ?>%) = <b><?php echo $row["precio-final"] ?>€</b></li>
                                </ul>
                            </div>
                        </div>
            <div class="modal fade" id="elimModal" tabindex="-1" aria-labelledby="elimModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="elimModalLabel">Eliminar esta factura? (# <?php echo $row["id"] ?>)</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="GET" action="execute.php">
                                <input type="hidden" name="id" value="<?php echo $row["id"] ?>">
                                <input class="btn btn-danger" type="submit" name="eliminar" id="eliminar" value="Eliminar">
                                <button type="button" class="btn btn-secondary"data-bs-dismiss="modal">Cancelar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="enviarModal" tabindex="-1" aria-labelledby="enviarModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="enviarModalLabel">Enviar esta factura? (# <?php echo $row["id"] ?>)</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Enviar a: <?php echo $row["email"] ?></p>
                            <form method="GET" action="execute.php">
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
                <ul class="nav nav-tabs bg-dark">
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
                    ?>
                </ul>
            </div>
            <div class="row">
                <div class="col-12 mt-2">
                    <form action="?pag=list" method="POST">
                        <div class="row">
                            <div class="col-12 my-2">
                                <div class="input-group">
                                    <div class="form-floating">
                                        <input type="text" placeholder="Buscar... (Tipo, Nombre, Id...)" name="search" id="search" class="form-control ">
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
            if(!isset($_POST["search"])){
                if(!isset($_GET["filter"])){
                    $pdo = connect();
                    $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden) ORDER BY i.id DESC");
                    $stmt->execute();
                } else {
                    $pdo = connect();
                    if($_GET["filter"] == 5){
                        $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden) WHERE `garantia` != 0 AND d.id IS NULL ORDER BY i.id DESC");
                    }else if($_GET["filter"] == 6){
                        $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden) WHERE d.id IS NOT NULL ORDER BY i.id DESC");
                    }else{
                        $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden) WHERE `estado` = :estado AND d.id IS NULL ORDER BY i.id DESC");
                        $stmt->bindParam(':estado', $_GET["filter"]);
                    }
                    $stmt->execute();
                }
            }else {
                $pdo = connect();
                echo '<p class="display-5 text-light">Resultados para <i>\''.$_POST["search"].'\'</i></p>';
                $search = "%".$_POST["search"]."%";
                $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden)
                WHERE `nombre_dispositivo` LIKE :search OR i.id LIKE :search OR `nombre` LIKE :search OR `local` LIKE :search OR `servicio` LIKE :search
                ORDER BY i.id DESC");
                $stmt->bindParam(':search', $search);
                $stmt->execute();
            }
            $i = 0;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // User restrictions
                if ($_SESSION["local"] != null && $_SESSION["local"] != $row["local"]) continue;

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
                    $bg = "text-bg-dark";
                    $estado = " | <i class='bi bi-file-text'></i> <a style='text-decoration:none;color:#FFA' href='?pag=list&id=" . $row["garantia"] . "'>GARANTÍA <i class='bi bi-arrow-right-short'></i></a>";
                } else {
                    $estado = " | " . $pasos[$row["estado"]];
                }

                // Prepare service information
                if(!empty($row["servicio"])){
                    $serv = explode(": ", $row["servicio"])[0];
                }else{
                    $serv = "PENDIENTE MODIFICAR";
                }

                // Generate card HTML
                echo '
                    <div class="col-lg-4 col-12">
                        <div class="card ' . $bg . ' my-3">
                            <h5 class="card-header py-3" style="color:white;background-color:' . $colores[$row["estado"]] . ';">' . $serv . ' # ' . $row["id"] . '<br><div style="display:inline;margin-right:2px;border-left: 3px solid ' . $localColor[$row["local"] == "Barcelona" ? 0 : 1] . ';height: 5px;"></div>' . $row["local"] . $estado . '</h5>';

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
                echo '
                            </div>
                            <div class="card-body">
                                <p class="card-text"><b>Nombre:</b> ' . $row["nombre"] . '</p>
                                <p class="card-text"><b>Dispositivo:</b> ' . $row["nombre_dispositivo"] . '</p>
                                <p class="card-text"><b>Descripción:</b> ' . $desc . '</p>
                                <p class="card-text"><b>Fecha:</b> ' . $row["fecha"] . '</p>
                            </div> 
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item ' . $bg . '"><b>Precio:</b> ' . $row["precio"] . '€ (+ IVA ' . $row["iva"] . '%) = <b>' . $row["precio-final"] . '€</b></li>
                            </ul>
                            <div class="card-body">';

                // Action buttons based on user role and ticket state
                if (($_SESSION["login"] == "tecnico" && $row["estado"] < 4) || ($_SESSION["login"] == "dependiente" && in_array($row["estado"], [1, 2, 4]))) {
                    if (($row["estado"] - 1) >= 0) {
                        echo '<a href="execute.php?estado=' . ($row["estado"] - 1) . '&id=' . $row["id"] . '&pag=0" class="btn text-light" style="background-color:' . $colores[$row["estado"] - 1] . '"><i class="bi bi-caret-left-fill"></i> ' . $pasos[$row["estado"] - 1] . '</a>';
                    }
                }
                if (($_SESSION["login"] == "tecnico" && $row["estado"] < 3) || ($_SESSION["login"] == "dependiente" && $row["estado"] < 2)) {
                    if (($row["estado"] + 1) < 5) {
                        echo '<a href="execute.php?estado=' . ($row["estado"] + 1) . '&id=' . $row["id"] . '&pag=0" class="btn text-light" style="color:black; background-color:' . $colores[$row["estado"] + 1] . '">' . $pasos[$row["estado"] + 1] . ' <i class="bi bi-caret-right-fill"></i></a>';
                    }
                }
                if ($_SESSION["login"] == "admin") {
                    if (($row["estado"] - 1) >= 0) {
                        echo '<a href="execute.php?estado=' . ($row["estado"] - 1) . '&id=' . $row["id"] . '&pag=0" class="btn text-light" style="color:black; background-color:' . $colores[$row["estado"] - 1] . '"><i class="bi bi-caret-left-fill"></i> ' . $pasos[$row["estado"] - 1] . '</a>';
                    }
                    if (($row["estado"] + 1) < 5) {
                        echo '<a href="execute.php?estado=' . ($row["estado"] + 1) . '&id=' . $row["id"] . '&pag=0" class="btn text-light" style="color:black; background-color:' . $colores[$row["estado"] + 1] . '">' . $pasos[$row["estado"] + 1] . ' <i class="bi bi-caret-right-fill"></i></a>';
                    }
                }
                
                echo '
                                <br><a href="?pag=list&id=' . $row["id"] . '" class="btn btn-primary mt-2">Detalles <i class="bi bi-caret-right-fill"></i></a>
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
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        </script>