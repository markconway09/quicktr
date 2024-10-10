        <div class="container border my-4 px-4 py-2 bg-dark rounded">
            <?php
            $pasos = ["Diagnóstico", "Aprobación", "Reparación", "Terminado", "Entregado"];
            $pasosLargo = ["Espera del diagnóstico", "Espera aprobación del cliente", "En Reparación", "Reparación terminada", "Entregado"];
            $colores = ["#f54254", "#e8a31a", "#2f852c", "#4472c4", "#adadad"];
            $localColor = ["blue", "red"];
            
            if(isset($_GET["id"])){
                $id = $_GET["id"];
                echo '<a href="?pag=list" class="btn btn-secondary mx-2 mt-3"><i class="bi bi-arrow-left"></i> Volver</a>';
                $row = selectBD($id);
                if($row["tipo"]=="venta"){
                    $d = explode(";", $row["desc"]);
                    $p = explode(";", $row["preciosVenta"]);
                    $c = explode(";", $row["cantidadVenta"]);
                    $desc = '<b>Producto(s):</b> ';
                    for($k = 0; $k<count($d);){
                        $desc .= "<a href='/almacen/?search=".$d[$k]."' target='_blank'>" . $d[$k] . "</a> (".(isset($p[$k])?$p[$k]."€":"?")." x ".(isset($c[$k])?$c[$k]:"?").")";
                        $k++;
                        if(isset($d[$k])){
                            $desc .= ", ";
                        }
                    }
                } else{
                    $desc = '<b>Descripción:</b> '.$row["desc"];
                    $precios = "";
                    $cant = "";
                }
                if($row["tipo"]=="venta"){
                    echo '<a href="execute.php?ticketventa=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-ticket-detailed"></i> Ticket Efectivo</a>';
                    echo '<a href="execute.php?venta=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-receipt-cutoff"></i> Factura</a>';
                    echo '<a href="execute.php?ventasimp=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-receipt"></i> Factura Simplificada</a>';
                }else{
                    echo '<a href="execute.php?ticketservicio=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-ticket-detailed"></i> Ticket Efectivo</a>';
                    echo '<a href="execute.php?servicio=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-receipt-cutoff"></i> Factura</a>';
                    echo '<a href="execute.php?servsimp=1&id='.$id.'" target="_blank" class="btn btn-primary mx-2 mt-3"><i class="bi bi-receipt"></i> Factura Simplificada</a>';
                }
                echo '<button type="button" class="btn btn-success mx-2 mt-3" data-bs-toggle="modal" data-bs-target="#enviarModal"><i class="bi bi-send"></i> Enviar</button>';
                echo '<a href="edit.php?id='.$id.'" class="btn btn-success mx-2 mt-3"><i class="bi bi-pencil-square"></i> Editar</a>';
                if(!empty($row["did"])){
                    echo '<a href="execute.php?deshacer=1&id='.$id.'" class="btn btn-danger mx-2 mt-3"><i class="bi bi-arrow-counterclockwise"></i> Deshacer</a>';
                } else {
                    echo '<a href="execute.php?devolucion=1&id='.$id.'" class="btn btn-danger mx-2 mt-3"><i class="bi bi-arrow-counterclockwise"></i> Devolución</a>';
                }
                if($_SESSION["login"] == "admin"){
                    echo '<button type="button" class="btn btn-danger mx-2 mt-3" data-bs-toggle="modal" data-bs-target="#elimModal"><i class="bi bi-trash"></i> Eliminar</button>';
                }      
                $estado = "";
                if(!empty($row["did"])) {
                    $estado = "<span class='text-danger'><i class='bi bi-arrow-counterclockwise'></i> DEVUELTO</span>";
                } else {
                    $estado = $pasosLargo[$row["estado"]];
                }
                $servicio = "";$ins = "";
                if($row["tipo"] == "servicio"){
                    $ser = explode(": ", $row["servicio"]);
                    $servicio .= '<p class="card-text"><b>Tipo de servicio:</b> '.$ser[0].'</p>';
                    $servicio .= '<p class="card-text"><b>Servicio:</b> '.$ser[1].'</p>';

                    if($row["insumo_desc"]!=""){
                        $ins = '<li class="list-group-item"><b>Insumo:</b> '.$row["insumo_desc"].' ('.$row["insumo_precio"].'€)';
                    } else {
                        $ins = '<li class="list-group-item"><b>Insumo:</b> 0';
                    }
                    if($_SESSION["login"] == "tecnico" || $_SESSION["login"] == "admin") $ins .= ' <a href="index.php?pag=edit_insumo&id='.$id.'" class="btn btn-primary">Editar</a>';
                    $ins .= '</li>';
                }
                echo '<div class="col-12">
                            <div class="card my-3">
                                <h5 class="card-header py-3" style="color:white;background-color:'.$colores[$row["estado"]].';">'.ucfirst($row["tipo"]).' # '.$row["id"].'</h5>
                                <div class="card-body">
                                    '.$estado;?>
                                <br>
                                <?php if(($row["estado"] < 4 && $_SESSION["login"]=="tecnico")||($_SESSION["login"]=="dependiente"&&$row["estado"]==4)){ if(($row["estado"]-1)>=0){ ?><a href="execute.php?estado=<?php echo $row["estado"]-1 ?>&id=<?php echo $row["id"] ?>&pag=1" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]-1] ?>"><i class="bi bi-caret-left-fill"></i> <?php echo $pasos[$row["estado"]-1] ?></a><?php }} ?>
                                <?php if(($row["estado"] < 3 && $_SESSION["login"]=="tecnico")){ if(($row["estado"]+1)<5){ ?><a href="execute.php?estado=<?php echo $row["estado"]+1 ?>&id=<?php echo $row["id"] ?>&pag=1" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]+1] ?>"><?php echo $pasos[$row["estado"]+1] ?> <i class="bi bi-caret-right-fill"></i></a><?php }} ?>
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
                                    <p class="card-text"><b>Dispositivo:</b> <?php echo $row["nombre_dispositivo"] ?></p>
                                    <p class="card-text"><?php echo $desc ?></p>
                                    <p class="card-text"><b>Método de pago:</b> <?php echo $row["metodo"] ?></p>
                                </div>
                                <ul class="list-group list-group-flush mb-3">
                                    <?php echo $ins ?>
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
                    <li class="nav-item">
                        <?php
                            if(!isset($_GET["filter"])) {
                                echo '<a class="nav-link active" href="?pag=list">Todo</a>';
                            } else {
                                echo '<a class="nav-link text-light" href="?pag=list">Todo</a>';
                            }
                        ?>
                    </li>
                    <li class="nav-item">
                        <?php
                            if(isset($_GET["filter"])&&$_GET["filter"] == "0") {
                                echo '<a class="nav-link active" href="?pag=list&filter=0">Diagnóstico</a>';
                            } else {
                                echo '<a class="nav-link text-light" href="?pag=list&filter=0">Diagnóstico</a>';
                            }
                        ?>
                    </li>
                    <li class="nav-item">
                        <?php
                            if(isset($_GET["filter"])&&$_GET["filter"] == "1") {
                                echo '<a class="nav-link active" href="?pag=list&filter=1">Aprobación</a>';
                            } else {
                                echo '<a class="nav-link text-light" href="?pag=list&filter=1">Aprobación</a>';
                            }
                        ?>
                    </li>
                    <li class="nav-item">
                        <?php
                            if(isset($_GET["filter"])&&$_GET["filter"] == "2") {
                                echo '<a class="nav-link active" href="?pag=list&filter=2">Reparación</a>';
                            } else {
                                echo '<a class="nav-link text-light" href="?pag=list&filter=2">Reparación</a>';
                            }
                        ?>
                    </li>
                    <li class="nav-item">
                        <?php
                            if(isset($_GET["filter"])&&$_GET["filter"] == "3") {
                                echo '<a class="nav-link active" href="?pag=list&filter=3">Terminado</a>';
                            } else {
                                echo '<a class="nav-link text-light" href="?pag=list&filter=3">Terminado</a>';
                            }
                        ?>
                    </li>
                    <li class="nav-item">
                        <?php
                            if(isset($_GET["filter"])&&$_GET["filter"] == "4") {
                                echo '<a class="nav-link active" href="?pag=list&filter=4">Entregado</a>';
                            } else {
                                echo '<a class="nav-link text-light" href="?pag=list&filter=4">Entregado</a>';
                            }
                        ?>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-12 mt-2">
                    <form action="?pag=list" method="POST">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" placeholder="Buscar... (Tipo, Nombre, Id...)" name="search" id="search" class="form-control my-2">
                                    <label for="search">Buscar... (Tipo, Nombre, Id...)</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <button type="submit" id="submit" class="btn btn-primary btn-block p-3 my-2"><i class="bi bi-search"></i> Buscar</button>
                                <?php if(isset($_POST["search"])){?>
                                    <a href="?pag=list" class="btn btn-secondary btn-block mx-3 p-3"><i class="bi bi-x-circle"></i> Quitar filtro</a>
                                <?php } ?>
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
                    $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden) WHERE `estado` = :estado ORDER BY i.id DESC");
                    $stmt->bindParam(':estado', $_GET["filter"]);
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
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    // RESTRICCIONES POR USUARIO //
                // DEPENDIENTES SOLO VEN TICKETS DE SU LOCAL
                if($_SESSION["login"] != "admin" && ($_SESSION["local"]!=null&&$_SESSION["local"] != $row["local"])) continue;
                // TECNICO SOLO VE PENDIENTES Y TERMINADOS
                //if($_SESSION["login"] == "tecnico" && $row["estado"] == 4) continue;
                
                if($i==0) echo '<div class="row">';
                if(strlen($row["desc"])>25){
                    $desc = substr($row["desc"], 0, 25) . '...';
                }else{
                    $desc = $row["desc"];
                }
                $bg = "text-bg-secondary";
                $dev = "";
                if(!empty($row["did"])) {
                    $bg = "text-bg-dark";
                    $dev = " | <span class='text-danger'><i class='bi bi-arrow-counterclockwise'></i> DEVUELTO</span>";
                } else {
                    $dev = " | ".$pasos[$row["estado"]];
                }
                $serv = explode(": ", $row["servicio"]);
                echo '
                    <div class="col-lg-4 col-12">
                        <div class="card '.$bg.' my-3">
                            <h5 class="card-header py-3" style="color:white;background-color:'.$colores[$row["estado"]].';">'.ucfirst($serv[0]).' # '.$row["id"].'<br><div style="display:inline;margin-right:2px;border-left: 3px solid '.$localColor[$row["local"]=="Barcelona"?0:1].';height: 5px;"></div>'.$row["local"].$dev.'</h5>';
                            ?>
                            <div class="text-center pt-2">
                                <b><?php echo $pasosLargo[$row["estado"]]; ?></b><br>
                                <div class="col-2 pt-2 d-inline-block" style="background-color:<?php echo $row["estado"]>=0?$colores[0]:"white"; ?>"></div>
                                <div class="col-2 pt-2 d-inline-block" style="background-color:<?php echo $row["estado"]>=1?$colores[1]:"white"; ?>"></div>
                                <div class="col-2 pt-2 d-inline-block" style="background-color:<?php echo $row["estado"]>=2?$colores[2]:"white"; ?>"></div>
                                <div class="col-2 pt-2 d-inline-block" style="background-color:<?php echo $row["estado"]>=3?$colores[3]:"white"; ?>"></div>
                                <div class="col-2 pt-2 d-inline-block" style="background-color:<?php echo $row["estado"]>=4?$colores[4]:"white"; ?>"></div>
                            </div>
                            <div class="text-center mx-auto">
                                <i class="bi bi-caret-up-fill px-4" <?php if($row["estado"]!=0){ ?> style="color:#6d747d" <?php } ?>></i>
                                <i class="bi bi-caret-up-fill px-4" <?php if($row["estado"]!=1){ ?> style="color:#6d747d" <?php } ?>></i>
                                <i class="bi bi-caret-up-fill px-4" <?php if($row["estado"]!=2){ ?> style="color:#6d747d" <?php } ?>></i>
                                <i class="bi bi-caret-up-fill px-4" <?php if($row["estado"]!=3){ ?> style="color:#6d747d" <?php } ?>></i>
                                <i class="bi bi-caret-up-fill px-4" <?php if($row["estado"]!=4){ ?> style="color:#6d747d" <?php } ?>></i>
                            </div>
                            <?php
                            echo'
                            <div class="card-body">
                                <p class="card-text"><b>Nombre:</b> '.$row["nombre"].'</p>
                                <p class="card-text"><b>Dispositivo:</b> '.$row["nombre_dispositivo"].'</p>
                                <p class="card-text"><b>Descripción:</b> '.$desc.'</p>
                            </div> 
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item '.$bg.'"><b>Precio:</b> '.$row["precio"].'€ (+ IVA '.$row["iva"].'%) = <b>'.$row["precio-final"].'€</b></li>
                            </ul>
                            <div class="card-body">';
                                ?>
                                <?php if(($row["estado"] < 4 && $_SESSION["login"]=="tecnico")||($_SESSION["login"]=="dependiente"&&$row["estado"]==4)){ if(($row["estado"]-1)>=0){ ?><a href="execute.php?estado=<?php echo $row["estado"]-1 ?>&id=<?php echo $row["id"] ?>&pag=0" class="btn text-light" style="background-color:<?php echo $colores[$row["estado"]-1] ?>"><i class="bi bi-caret-left-fill"></i> <?php echo $pasos[$row["estado"]-1] ?></a><?php }} ?>
                                <?php if(($row["estado"] < 3 && $_SESSION["login"]=="tecnico")){ if(($row["estado"]+1)<5){ ?><a href="execute.php?estado=<?php echo $row["estado"]+1 ?>&id=<?php echo $row["id"] ?>&pag=0" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]+1] ?>"><?php echo $pasos[$row["estado"]+1] ?> <i class="bi bi-caret-right-fill"></i></a><?php }} ?>
                                <?php if($_SESSION["login"]=="admin"){ if(($row["estado"]-1)>=0){ ?><a href="execute.php?estado=<?php echo $row["estado"]-1 ?>&id=<?php echo $row["id"] ?>&pag=0" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]-1] ?>"><i class="bi bi-caret-left-fill"></i> <?php echo $pasos[$row["estado"]-1] ?></a><?php }} ?>
                                <?php if($_SESSION["login"]=="admin"){ if(($row["estado"]+1)<5){ ?><a href="execute.php?estado=<?php echo $row["estado"]+1 ?>&id=<?php echo $row["id"] ?>&pag=0" class="btn text-light" style="color:black; background-color:<?php echo $colores[$row["estado"]+1] ?>"><?php echo $pasos[$row["estado"]+1] ?> <i class="bi bi-caret-right-fill"></i></a><?php }} ?>
                                <?php
                            echo '
                                <br><a href="?pag=list&id='.$row["id"].'" class="btn btn-primary mt-2">Detalles <i class="bi bi-caret-right-fill"></i></a>
                            </div>
                        </div>
                    </div>';
                if($i==2){
                    $i=0;
                    echo '</div>';
                }else{
                    $i++;
                }
            }
            ?>
        </div>