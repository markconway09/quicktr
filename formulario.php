<?php
// LOGICA PARA DECIDIR QUÉ FORMULARIO MOSTRAR
    $action = 'controller/execute.php';
    $title = '<h1 class="display-5 text-light text-center mb-4">SERVICIO</h1>';
    $submit = '<input type="submit" name="guardar-servicio" class="w-100 btn btn-success col-5 mx-auto" value="Guardar">';
    if(isset($_GET["id"])) $backbtn = '<a href="index.php?pag=list&id='. $_GET["id"] .'" class="btn btn-secondary">Volver</a>';

    if(isset($_GET["form"])){
        switch($_GET["form"]){
            case "garantia":
                $action = '?pag=list';
                $title = $backbtn.'<h1 class="display-5 text-light text-center mb-4">Garantía para # '. $_GET["id"] .'</h1>';
                $submit = '<input type="hidden" name="id" value="'. $_GET["id"] .'">
                        <input type="submit" name="garantia" class="w-100 btn btn-primary col-12 mx-auto" value="Crear Ticket Garantia">';
                break;
            case "edit":
                $action = '?pag=list';
                $title = $backbtn.'<h1 class="display-5 text-light text-center mb-4">Editar # '. $_GET["id"] .'</h1>';
                $submit = '<input type="hidden" name="id" value="'. $_GET["id"] .'">
                        <input type="submit" name="editar-factura" class="w-100 btn btn-success col-5 mx-auto" value="Guardar Cambios">';
                break;
        }
    }

    $before = '<form action="'.$action.'" method="POST" class="form-control p-4 bg-dark border-secondary" enctype="multipart/form-data">'.$title;
    $after = '<div class="row"><div class="col-12">'.$submit.'</div></div></form>';
?>
                
<?php
    echo $before;
    include 'registro_cliente.php';
?>
    <hr class="text-light pb-3">
<?php
    include 'registro_servicio.php';
    echo $after;
?>