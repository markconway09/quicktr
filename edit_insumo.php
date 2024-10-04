<?php
if(!isset($_SESSION["login"])){
    include 'login.php';
    exit();
}
$datos=selectBD($_GET["id"]);
$servicio = explode(": ", $datos["servicio"]);

if(isset($_POST["editar_insumo"])){
    $pdo = connect();
    $stmt = $pdo->prepare("UPDATE `info_orden` SET `insumo_desc` = :insumo_d, `insumo_precio` = :insumo_p, `desc` = :de WHERE `info_orden`.`id` = :id");
    $stmt->bindParam(':id', $_GET["id"]);
    $stmt->bindParam(':de', $_POST["desc"]);
    $stmt->bindParam(':insumo_d', $_POST["insumo_desc"]);
    $stmt->bindParam(':insumo_p', $_POST["insumo_precio"]);
    try {
        $stmt->execute();
    } catch (PDOException $e){
        echo '<p class="text-light">'.$e->getMessage().'</p>';
    }
    
    header('Location: index.php?pag=list&id='.$_GET["id"]);
}
?>
<div class="container form-control p-4 bg-dark">
    <form action="" method="POST" class="form-control p-4 bg-dark">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="form-floating">
                    <textarea rows="2" style="height:100%;" class="form-control" name="desc" id="desc"><?php echo $datos["desc"]; ?></textarea>
                    <label for="desc">Descripción del servicio</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="input-group">
                <span class="input-group-text">Insumo</span>
                    <div class="form-floating">
                        <input class="form-control" placeholder="Descripción" type="text" name="insumo_desc" id="insumo_desc" value="<?php echo $datos["insumo_desc"];?>">
                        <label for="insumo_desc">Descripción</label>
                    </div>
                    <div class="form-floating">
                        <input class="form-control" placeholder="Precio" type="number" step=.01 name="insumo_precio" id="insumo_precio" value="<?php echo $datos["insumo_precio"];?>">
                        <label for="insumo_precio">Precio</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <input type="submit" name="editar_insumo" class="btn btn-primary col-4 mx-auto" value="Guardar Cambios">
        </div>
    </form>
</div>