<?php
// LOGICA POR SI SE TIENE QUE RELLENAR ALGUNOS CAMPOS
if(isset($_GET["id"])){
    $datos=selectBD($_GET["id"]);
    $servicio = explode(": ", $datos["servicio"]);
}
$rellenar = false;
if(isset($_GET["form"])){
    if($_GET["form"] == "nuevo" || $_GET["form"] == "garantia" || $_GET["form"] == "edit") $rellenar = true;
}

?>

<div class="row">
    <div class="col-12 mb-3">
        <div class="form-floating" style="z-index: 10;">
            <!-- The select select_code that Choices.js will enhance -->
            <select onchange="selectCliente(this.value)" id="cliente_select" class="form-select" name="cliente_select">
                <option value="">Nombre Cliente</option> <!-- Placeholder option -->
            </select>
        </div>
    </div>
</div>
<hr class="text-light mb-4">
<div class="row">
    <div class="col-12 col-md-7 mb-3">
        <div class="form-floating">
            <input class="form-control" placeholder="Nombre" type="text" name="nombre" id="nombre"
                <?php if($rellenar) : ?>
                    value="<?php echo $datos["nombre"];?>"
                <?php endif ?>
            >
            <label for="nombre">Nombre</label>
        </div>
    </div>
    <div class="col-12 col-md-5 mb-3">
        <div class="form-floating">
            <input class="form-control" placeholder="DNI/NIF/NIE" type="text" name="doc" id="doc"
                <?php if($rellenar) : ?>
                    value="<?php echo $datos["documento"];?>"
                <?php endif ?>
            >
            <label for="doc">DNI/NIF/NIE</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-md-5 mb-3">
        <div class="input-group">
            <?php if($rellenar) : ?>
                <div class="form-floating">
                    <input class="form-control" placeholder="Teléfono" type="tel" name="tel" id="tel" value="<?php echo $datos["telefono"];?>">
                    <label for="tel">Teléfono</label>
                </div>
            <?php endif ?>
            <?php if(!$rellenar) : ?>
                <div class="form-floating">
                    <?php
                    include 'countrycodes.php';
                    ?>
                </div>

                <div class="form-floating">
                    <input class="form-control" placeholder="Teléfono" type="tel" name="tel" id="tel">
                    <label for="tel">Teléfono</label>
                </div>
            <?php endif ?>
        </div>
    </div>
    <div class="col-12 col-md-7 mb-3">
        <div class="form-floating">
            <input class="form-control" placeholder="Email" type="email" name="email" id="email"
                <?php if($rellenar) : ?>
                    value="<?php echo $datos["email"];?>"
                <?php endif ?>
            >
            <label for="email">Email</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-md-4 mb-3">
        <div class="form-floating">
            <select class="form-control form-select" name="local" id="local">
                <?php
                if (!is_null($_SESSION["local"])) {
                ?>
                    <option value="<?php echo $_SESSION["local"]; ?>"><?php echo $_SESSION["local"]; ?></option>
                <?php
                } else {
                ?>
                    <option value="Barcelona">Barcelona</option>
                    <option value="Mataró">Mataró</option>
                <?php
                }
                ?>
            </select>
            <label for="local">Local</label>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3">
        <div class="form-floating">
            <select class="form-control form-select" name="razon" id="razon">
                <option value="Sin especificar" selected>-Selecciona una opción-</option>
                <option value="Instagram">Instagram</option>
                <option value="Facebook">Facebook</option>
                <option value="Wallapop">Wallapop</option>
                <option value="Milanuncios">Milanuncios</option>
                <option value="Google">Google</option>
                <option value="Maps">Google/Apple Maps</option>
                <option value="Recomendación">Recomendación</option>
                <option value="Flyer">Flyer</option>
                <option value="Retorno">Retorno de cliente</option>
                <option value="Otros">Otros</option>
            </select>
            <label for="razon">Como nos encontró</label>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3">
        <div class="form-floating">
            <select class="form-control form-select" name="dept" id="dept">
                <option value="hardware">Hardware</option>
                <option value="web">Web</option>
                <option value="redes">Redes</option>
            </select>
            <label for="dept">Departamento</label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-3">
        <div class="form-check form-switch">
            <input onchange="registroSocio()" type="checkbox" name="socio" id="socio" class="form-check-input">
            <label for="socio" class="text-light">Registrar Socio</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-md-3 my-3 d-none" id="regSocio">
        <div class="form-floating">
            <input class="form-control" placeholder="Fecha de nacimiento" type="date" name="nacimiento" id="nacimiento">
            <label for="nacimiento">Fecha de nacimiento</label>
        </div>
    </div>
</div>

<script>
    // REGISTRO SOCIO
    function registroSocio() {
        let s = $("#regSocio");
        if (s.hasClass("d-none")) {
            s.removeClass("d-none");
            s.removeAttr("required");
            s.val("");
        } else {
            s.addClass("d-none");
            s.attr("required", true);
        }
    }

    function selectCliente(id){
        var nombre = $("#nombre");
        var doc = $("#doc");
        var tel = $("#tel");
        var email = $("#email");
        var cliente;
        $.ajax({
            url: 'controller/ajax_query.php', // The PHP file that returns data
            type: 'GET', // Method of the request
            data: { call: 0 },
            dataType: 'json', // Expected data type (JSON)
            success: function(data) {
                data.forEach(function(item) {
                    if(id == item["id"]){
                        cliente = item;
                    }
                });
                if (cliente) { // If a matching client is found, update input fields
                    nombre.val(cliente.nombre);
                    doc.val(cliente.documento);
                    tel.val(cliente.telefono);
                    email.val(cliente.email);
                } else {
                    // Clear the form if no client is found
                    nombre.val("");
                    doc.val("");
                    tel.val("");
                    email.val("");
                    console.warn("Cliente not found. Form cleared.");
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + ' - ' + error);
            }
        });
    }

    // ON READY
    $(document).ready(function() {
        // CLIENTE REGISTRADO
        const select_cliente = document.getElementById("cliente_select");
        const availableItems = [];
        const availableId = [];
        $.ajax({
            url: 'controller/ajax_query.php', // The PHP file that returns data
            type: 'GET', // Method of the request
            data: { call: 0 },
            dataType: 'json', // Expected data type (JSON)
            success: function(data) {
                data.forEach(function(item) {
                    if(item["nombre"] != "" && item["nombre"] != "-"){
                        availableItems.push(item["nombre"]);
                        availableId.push(item["id"]);
                    }
                });

                for (let i = 0; i < availableItems.length; i++) {
                    var opt = document.createElement('option');
                    opt.value = availableId[i];
                    opt.innerHTML = availableItems[i];
                    select_cliente.appendChild(opt);
                }
                const choices = new Choices(select_cliente, {
                    searchEnabled: true, // Enable searching
                    removeItemButton: true, // Enable remove button for selected items
                    searchResultLimit: 10, // Limit the number of search results
                    searchFloor: 1, // Start searching after 1 character
                    maxItemCount: 5, // Max number of items allowed in the select
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + ' - ' + error);
            }
        });
    });
</script>