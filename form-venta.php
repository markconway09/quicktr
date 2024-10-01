    <form action="execute.php" target="_blank" method="POST" class="form-control p-4 bg-dark">
        <h1 class="display-5 text-light text-center mb-4">VENTA</h1>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <div class="form-floating">
                    <select class="form-control form-select" name="metodo" id="metodo" required>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Efectivo">Efectivo</option>
                    </select>
                    <label for="metodo">Método de pago</label>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <div class="form-floating">
                    <select class="form-control form-select" name="local" id="local" required>
                        <?php
                            if(!is_null($_SESSION["local"])) {
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
        </div>
        <div class="row" id="productos">
            <h3 class="display-6 text-light text-center">Producto(s)</h3>
            <div class="col-12 mb-3" id="col-input">
                <div class="input-group">
                    <div class="form-floating w-50 p-input"></div>
                    <div class="form-floating w-25">
                        <input type="number" step="0.01" onblur="findPrecioTotal()" class="form-control" placeholder="Precio" name="prec1" id="prec1">
                        <label for="prec1">Precio</label>
                    </div>
                    <div class="form-floating w-25">
                        <input type="number" value=1 onblur="findPrecioTotal()" class="form-control" placeholder="Cantidad" name="cant1" id="cant1">
                        <label for="cant1">Cantidad</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mx-auto mb-3">
                <button type="button" class="btn btn-secondary" onclick="crearDatalist(1)">+ Añadir Producto</button>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-4 mb-3">
                <div class="form-floating">
                    <input class="form-control" onkeyup="findTotal()" placeholder="Precio" type="number" step="0.01" name="precio" id="precio" required>
                    <label for="precio">Precio €</label>
                </div>
            </div>
            <div class="col-12 col-md-2 mb-3">
                <div class="form-floating">
                    <input class="form-control" onkeyup="findTotal()" placeholder="Descuento" type="number" step="0.1" value=0 name="descuento" id="descuento">
                    <label for="descuento">Descuento</label>
                </div>
            </div>
            <div class="col-12 col-md-2 mb-3">
                <div class="form-floating">
                    <input class="form-control" onkeyup="findTotal()" placeholder="Iva 21%" type="number" step="0.1" value=21 name="iva" id="iva" required>
                    <label for="iva">Iva 21%</label>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <div class="form-floating">
                    <input class="form-control" onkeyup="findPrecio()" placeholder="Precio Final" step="0.01" type="number" name="precio-final" id="precio-final" required>
                    <label for="precio-final">Precio Final €</label>
                </div>
            </div>
        </div>
        <div class="row">
            <input type="submit" name="guardar-venta" class="btn btn-success col-5 mx-auto" value="Guardar">
        </div>
    </form>
<script type="text/javascript">

    $(document).ready(function(){
        crearDatalist(0);
    });

    var i = 1;

    function crearDatalist(loc) {
        $.ajax({
            url: 'ajax_productos.php',
            dataType: 'json',
            success: function(data){
                crear(data, loc);
            }
        });
    }

    function crear(datos, loc){
        if(loc!=0) i++;
        var input = '<input list="prod'+i+'" onkeyup="addPrice(this.value,'+i+')" name="prod'+i+'" class="form-control" placeholder="">';
        var label = '<label for="prod'+i+'">Producto</label>';
        var datalist = '<datalist id="prod'+i+'" class="form-control d-none" required>';
        
        for(let i=0;i<(datos.length-1);i++){
            datalist += '<option value="'+datos[i]["id"]+': '+datos[i]["nombre"]+'"></option>';
        }
        datalist += '</optgroup></select>';
        
        var list = input + label + datalist;

        if(loc == 0){
            document.getElementsByClassName("p-input")[0].innerHTML = list;
        } else {
            var prod = document.getElementById("productos");
            var clone = document.getElementById("col-input").cloneNode(true);
            clone.innerHTML='<div class="input-group">'+
                            '<div class="form-floating w-50 p-input">'+
                            list+
                            '</div>'+
                            '<div class="form-floating w-25">'+
                                '<input type="number" step="0.01" onblur="findPrecioTotal()" class="form-control" placeholder="Precio" name="prec'+i+'" id="prec'+i+'">'+
                                '<label for="prec'+i+'">Precio</label>'+
                            '</div>'+
                            '<div class="form-floating w-25">'+
                                '<input type="number" value=1 onblur="findPrecioTotal()" class="form-control" placeholder="Cantidad" name="cant'+i+'" id="cant'+i+'">'+
                                '<label for="cant'+i+'">Cantidad</label>'+
                            '</div>'+
                        '</div>';
            prod.appendChild(clone);
        }
    }

    function addPrice(prod, num){
        $.ajax({
            data: {"id": prod},
            url: 'ajax_productos.php',
            dataType: 'json',
            success: function(data){
                document.getElementById("prec"+num).value = data[0]["precio_venta"];
                findPrecioTotal();
            }
        });
    }
</script>