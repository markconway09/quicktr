<?php
    if(!isset($_SESSION["login"])) header('Location: index.php');
    if($_SESSION["login"]=="user") header('Location: index.php');

    // IMPORT FUNCTIONS
    require_once "controller/functions.php";
    $db = new Database();
    $pdo = $db->pdo;
    $stmt = $pdo->prepare("SELECT * FROM InfoOrden ORDER BY id DESC");
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
?>
<a href="controller/exportOrden.php" target="_blank" class="btn btn-primary mb-2">Exportar CSV <i class="bi bi-cloud-download"></i></a>
<table class="table table-dark table-striped text-bg-dark">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nombre</th>
            <th scope="col">Teléfono</th>
            <th scope="col">Documento</th>
            <th scope="col">Email</th>
            <th scope="col">Dispositivo</th>
            <th scope="col">Servicio</th>
            <th scope="col">Insumo</th>
            <th scope="col">Precio</th>
            <th scope="col">Metodo</th>
            <th scope="col">Descripción</th>
            <th scope="col">Local</th>
            <th scope="col">Fecha Entrada</th>
            <th scope="col">Fecha Pago</th>
            <th scope="col">Estado</th>
        </tr>
    </thead>
    <?php
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    ?>

        <tr>
            <th scope="row"><?php echo $row["id"]; ?></th>
            <td><?php echo $row["nombre"]; ?></td>
            <td><?php echo $row["telefono"]; ?></td>
            <td><?php echo $row["documento"]; ?></td>
            <td><?php echo $row["email"]; ?></td>
            <td><?php echo $row["nombre_dispositivo"]; ?></td>
            <td><?php echo $row["servicio"]; ?></td>
            <td><?php echo $row["insumo_precio"]; ?></td>
            <td><?php echo $row["precio-final"]; ?></td>
            <td><?php echo $row["metodo"]; ?></td>
            <td><?php echo $row["desc"]; ?></td>
            <td><?php echo $row["local"]; ?></td>
            <td><?php echo $row["fecha"]; ?></td>
            <td><?php echo $row["fecha_pago"]; ?></td>
            <td><?php echo $row["estado"]; ?></td>
        </tr>
    
    <?php
    }
    ?>
</table>