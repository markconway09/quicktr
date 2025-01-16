<?php
// LOGICA PARA DECIDIR QUÉ FORMULARIO MOSTRAR
    $action = 'controller/execute.php';
    $title = '<h1 class="display-5 text-light text-center mb-4">FORMULARIO</h1>';
    $hidden = '';
    // $submit = '<input type="submit" name="guardar-servicio" class="w-100 btn btn-success col-5 mx-auto" value="Guardar">';
    $submit = 'name="guardar-servicio"';
    if(isset($_GET["id"])) $backbtn = '<a href="list&id='. $_GET["id"] .'" class="btn btn-secondary">Volver</a>';

    if(isset($_GET["form"])){
        switch($_GET["form"]){
            case "garantia":
                $action = 'list';
                $title = $backbtn.'<h1 class="display-5 text-light text-center mb-4">Garantía para # '. $_GET["id"] .'</h1>';
                $hidden = '<input type="hidden" name="id" value="'. $_GET["id"] .'">';
                $submit = 'name="garantia"';
                break;
            case "edit":
                $action = 'list';
                $title = $backbtn.'<h1 class="display-5 text-light text-center mb-4">Editar # '. $_GET["id"] .'</h1>';
                $hidden = '<input type="hidden" name="id" value="'. $_GET["id"] .'">';
                $submit = 'name="editar-factura"';
                break;
        }
    }

    $before = ' <form action="'.$action.'" method="POST" class="form-control p-4 bg-dark border-secondary" enctype="multipart/form-data">'.$title;
    $after = '      <div class="row">
                        <div class="col-12">
                            '.$hidden.'
                            <button class="mx-auto save-ticket" '.$submit.'>
                                <div class="svg-wrapper-1">
                                    <div class="svg-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path fill="currentColor" d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"></path>
                                    </svg>
                                    </div>
                                </div>
                                <span>Guardar</span>
                            </button>
                        </div>
                    </div>
                </form>';
?>
                
<?php
    echo $before;
    include 'views/registro_cliente.php';
?>
    <hr class="text-light pb-3">
<?php
    include 'views/registro_servicio.php';
    echo $after;
?>