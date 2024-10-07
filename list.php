        <div class="container border my-4 px-4 py-2 bg-dark rounded">
            <?php
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
                    $estado = " - <span class='text-danger'><i class='bi bi-arrow-counterclockwise'></i> DEVUELTO</span>";
                } else if($row["tipo"]=="servicio") {
                    if($row["pendiente"] == 1){
                        $estado = " - <span style='color:#26FF17'><i class='bi bi-check-circle'></i> TERMINADO</span> <a href='execute.php?desCobrar=1&id=".$id."' class='btn btn-danger mb-1'>Deshacer</a>";
                    } else {
                        $estado = " - <span class='text-warning'><i class='bi bi-clock-history'></i> PENDIENTE</span> <a href='execute.php?cobrar=1&id=".$id."' class='btn btn-primary mb-1'>Cobrar</a>";

                    }
                }
                $servicio = "";$ins = "";
                if($row["tipo"] == "servicio"){
                    $ser = explode(": ", $row["servicio"]);
                    $servicio .= '<p class="card-text"><b>Tipo de servicio:</b> '.$ser[0].'</p>';
                    $servicio .= '<p class="card-text"><b>Servicio:</b> '.$ser[1].'</p>';

                    if($row["insumo_desc"]!=""){
                        $ins = '<li class="list-group-item"><b>Insumo:</b> '.$row["insumo_desc"].' ('.$row["insumo_precio"].'€) <a href="index.php?pag=edit_insumo&id='.$id.'" class="btn btn-primary">Editar</a></li>';
                    } else {
                        $ins = '<li class="list-group-item"><b>Insumo:</b> 0 <a href="index.php?pag=edit_insumo&id='.$id.'" class="btn btn-primary">Editar</a></li>';
                    }
                }
                echo '<div class="col-12">
                            <div class="card my-3">
                                <h5 class="card-header text-bg-secondary py-3">'.ucfirst($row["tipo"]).' # '.$row["id"].$estado.'</h5>
                                <div class="card-body">
                                    <p class="card-text"><b>Nombre:</b> '.$row["nombre"].'</p>
                                    <p class="card-text"><b>Documento:</b> '.$row["documento"].'</p>
                                    <p class="card-text"><b>Fecha (d/m/y):</b> '.$row["fecha"].'</p>
                                    <p class="card-text"><b>Fecha de pago:</b> '.$row["fecha_pago"].'</p>
                                    '.$servicio.'
                                    <p class="card-text"><b>Email:</b> '.$row["email"].'</p>
                                    <p class="card-text"><b>Dirección:</b> '.$row["direccion"]." - ".$row["cp"].'</p>
                                    <p class="card-text"><b>Teléfono:</b> <a href="https://wa.me//'.$row["telefono"].'" target="_blank">'.$row["telefono"].'<a></p>
                                    <hr>
                                    <p class="card-text">'.$desc.'</p>
                                    <p class="card-text"><b>Local:</b> '.$row["local"].'</p>
                                    <p class="card-text"><b>Cómo nos encontró:</b> '.$row["razon"].'</p>
                                    <p class="card-text"><b>Departamento:</b> '.$row["dept"].'</p>
                                    <p class="card-text"><b>Método de pago:</b> '.$row["metodo"].'</p>
                                    ';
                                    echo'
                                </div>
                                <hr> 
                                <ul class="list-group list-group-flush mb-3">
                                    '.$ins.'
                                    <li class="list-group-item"><b>Precio:</b> '.$row["precio"].'€ (- '.$row["descuento"].'%) (+ IVA '.$row["iva"].'%) = <b>'.$row["precio-final"].'€</b></li>
                                </ul>
                            </div>
                        </div>';
                echo '<div class="modal fade" id="elimModal" tabindex="-1" aria-labelledby="elimModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="elimModalLabel">Eliminar esta factura? (# '.$row["id"].')</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="GET" action="execute.php">
                                <input type="hidden" name="id" value="'.$row["id"].'">
                                <input class="btn btn-danger" type="submit" name="eliminar" id="eliminar" value="Eliminar">
                                <button type="button" class="btn btn-secondary"data-bs-dismiss="modal">Cancelar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>';
            echo '<div class="modal fade" id="enviarModal" tabindex="-1" aria-labelledby="enviarModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="enviarModalLabel">Enviar esta factura? (# '.$row["id"].')</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Enviar a: '.$row["email"].'</p>
                            <form method="GET" action="execute.php">
                                <input type="hidden" name="id" value="'.$row["id"].'">
                                <input class="btn btn-success" type="submit" name="enviar" id="enviar" value="Enviar">
                                <button type="button" class="btn btn-secondary"data-bs-dismiss="modal">Cancelar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>';
            exit();
            }
            ?>
            <div class="row">
                <div class="col-12 mt-2">
                    <form action="?pag=list" method="POST">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" placeholder="Buscar... (Tipo, Nombre, Servicio, Id...)" name="search" id="search" class="form-control my-2">
                                    <label for="search">Buscar... (Tipo, Nombre, Id...)</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <button type="submit" id="submit" class="btn btn-primary btn-block p-3 my-2"><i class="bi bi-search"></i> Buscar</button>
                                <form action="?pag=list" method="POST">
                                    <button name="search" value="pendiente" style="background-color:#ccab06; color:white" class="btn btn-block p-3"><i class='bi bi-clock-history'></i> Pendientes</button>
                                    <button name="search" value="terminado" class="btn btn-success btn-block p-3"><i class='bi bi-check-circle'></i> Terminados</button>
                                </form>
                                <?php
                                if(isset($_POST["search"])){
                                    echo '<a href="?pag=list" class="btn btn-secondary btn-block p-3"><i class="bi bi-x-circle"></i> Quitar filtro</a>';
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div></i>
            <?php
            if(!isset($_POST["search"])){
                $pdo = connect();
                $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden) ORDER BY i.id DESC");
                $stmt->execute();
            }else if($_POST["search"] == "pendiente" || $_POST["search"] == "terminado"){
                if($_POST["search"] == "pendiente") $search = 0;
                if($_POST["search"] == "terminado") $search = 1;
                $pdo = connect();
                $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden) WHERE `pendiente` = :search AND `tipo` = 'servicio' ORDER BY i.id DESC");
                $stmt->bindParam(':search', $search);
                $stmt->execute();
            }else{
                $pdo = connect();
                echo '<p class="display-5 text-light">Resultados para <i>\''.$_POST["search"].'\'</i></p>';
                $search = "%".$_POST["search"]."%";
                $stmt = $pdo->prepare("SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden)
                WHERE `tipo` LIKE :search OR i.id LIKE :search OR `nombre` LIKE :search OR `local` LIKE :search OR `servicio` LIKE :search
                ORDER BY i.id DESC");
                $stmt->bindParam(':search', $search);
                $stmt->execute();
            }
            $i = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                if($_SESSION["login"] != "admin" && $_SESSION["local"] != $row["local"]) continue;
                if($i==0) echo '<div class="row">';
                if(strlen($row["desc"])>25){
                    $desc = substr($row["desc"], 0, 25) . '...';
                }else{
                    $desc = $row["desc"];
                }
                $bg;
                switch($row["local"]){
                    case "Barcelona":
                        $bg = "text-bg-secondary";
                        break;
                    case "Mataró":
                        $bg = "bg-success-subtle";
                        break;
                    case "Badalona":
                        $bg = "bg-warning-subtle";
                        break;
                        
                    default:
                        $bg = "bg-light-subtle";
                }
                $dev = "";
                if(!empty($row["did"])) {
                    $bg = "text-bg-dark";
                    $dev = " - <span class='text-danger'><i class='bi bi-arrow-counterclockwise'></i> DEVUELTO</span>";
                } else if($row["tipo"]=="servicio") {
                    if($row["pendiente"] == 1){
                        $dev = " - <span style='color:#26FF17'><i class='bi bi-check-circle'></i> TERMINADO</span>";
                    } else {
                        $dev = " - <span class='text-warning'><i class='bi bi-clock-history'></i> PENDIENTE</span>";

                    }
                }
                echo '
                    <div class="col-lg-4 col-12">
                        <div class="card '.$bg.' my-3">
                            <h5 class="card-header py-3">'.ucfirst($row["tipo"]).' # '.$row["id"].'<br>'.$row["local"].$dev.'</h5>
                            <div class="card-body">
                                <p class="card-text"><b>Nombre:</b> '.$row["nombre"].'</p>
                                <p class="card-text"><b>Documento:</b> '.$row["documento"].'</p>
                                <p class="card-text"><b>Fecha (d/m/y):</b> '.$row["fecha"].'</p>
                                <p class="card-text"><b>Servicio:</b> '.explode(": ", $row["servicio"])[0].'</p>
                                <p class="card-text"><b>Email:</b> '.$row["email"].'</p>
                                <p class="card-text"><b>Teléfono:</b> '.$row["telefono"].'</p>
                                <p class="card-text"><b>Descripción:</b> '.$desc.'</p>
                            </div> 
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item '.$bg.'"><b>Precio:</b> '.$row["precio"].'€ (+ IVA '.$row["iva"].'%) = <b>'.$row["precio-final"].'€</b></li>
                                <li class="list-group-item '.$bg.'"><b>Descuento:</b> '.$row["descuento"].'%</b></li>
                            </ul>
                            <div class="card-body">
                                <a href="?pag=list&id='.$row["id"].'" class="btn btn-primary">Más Info <i class="bi bi-caret-right-fill"></i></a>
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