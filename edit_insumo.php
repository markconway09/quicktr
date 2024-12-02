<?php
if(!isset($_SESSION["login"])){
    include 'login.php';
    exit();
}
$datos=selectBD($_GET["id"]);
$servicio = explode(": ", $datos["servicio"]);
?>
    <form action="execute.php?id=<?php echo $_GET["id"] ?>" method="POST" class="form-control p-4 text-bg-dark">
        <a href="index.php?pag=list&id=<?php echo $datos["id"]; ?>" class="btn btn-secondary mb-4">Volver</a>
        <div class="row mb-3">
            <div class="col-12">
                <div class="form-floating">
                    <input disabled class="form-control" placeholder="Dispositivo" type="text" name="dispositivo" id="dispositivo" value="<?php echo $datos["nombre_dispositivo"]; ?>">
                    <label for="dispositivo">Dispositivo</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-3">
                <div class="form-floating">
                    <textarea rows="3" style="height:100%;" class="form-control" name="desc" id="desc"><?php echo $datos["desc"]; ?></textarea>
                    <label for="desc">Descripción del servicio</label>
                </div>
            </div>
        </div>
        <div class="row">
            <h2 class="display-5">Técnico</h2>
        </div>
        <div class="row">
            <div class="col-12 mb-3">
                <div class="form-floating">
                    <textarea rows="3" style="height:100%;" class="form-control" name="desc_tecnico" id="desc_tecnico"><?php echo $datos["desc_tecnico"]; ?></textarea>
                    <label for="desc_tecnico">Descripción técnico</label>
                </div>
            </div>
        </div>
        <?php if($_SESSION["login"]=="admin"): ?>
        <div class="row">
            <div class="col-12" id="insumo">
                <?php
                $ins = explode(";",$datos["insumo_desc"]);
                $pre = explode(";",$datos["insumo_precio"]);

                for($i=0;$i<count($ins);$i++){ ?>
                <div class="input-group mb-3">
                    <span class="input-group-text">Insumo</span>
                    <div class="form-floating">
                        <input class="form-control" placeholder="Descripción" type="text" name="insumo_desc<?php echo $i+1; ?>" id="insumo_desc<?php echo $i+1; ?>" value="<?php echo $ins[$i];?>">
                        <label for="insumo_desc">Descripción</label>
                    </div>
                    <div class="form-floating">
                        <input class="form-control" placeholder="Precio" type="number" step=.01 name="insumo_precio<?php echo $i+1; ?>" id="insumo_precio<?php echo $i+1; ?>" value="<?php echo isset($pre[$i])?$pre[$i]:0;?>">
                        <label for="insumo_precio">Precio</label>
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

    <script>
        function crear(){
            var insumo = document.getElementById("insumo");
            var clone = insumo.children[0].cloneNode(true);
            var num = insumo.children.length;
            clone.innerHTML='<span class="input-group-text">Insumo</span>'+
                        '<div class="form-floating">'+
                            '<input class="form-control" placeholder="Descripción" type="text" name="insumo_desc'+(num+1)+'" id="insumo_desc'+(num+1)+'">'+
                            '<label for="insumo_desc">Descripción</label>'+
                        '</div>'+
                        '<div class="form-floating">'+
                            '<input class="form-control" placeholder="Precio" type="number" step=.01 name="insumo_precio'+(num+1)+'" id="insumo_precio'+(num+1)+'" value=0>'+
                            '<label for="insumo_precio">Precio</label>'+
                        '</div>';
            insumo.appendChild(clone);
        }
    </script>