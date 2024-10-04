<?php
if(!isset($_SESSION["login"])){
    include 'login.php';
    exit();
}
$datos=selectBD($_GET["id"]);
$servicio = explode(": ", $datos["servicio"]);

if(isset($_POST["editar_insumo"])){
    editarInsumo();
}
?>
<div class="container form-control p-4 bg-dark">
    <form action="" method="POST" class="form-control p-4 bg-dark">
        <a href="index.php?pag=list&id=<?php echo $datos["id"]; ?>" class="btn btn-secondary mb-4">Volver</a>
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