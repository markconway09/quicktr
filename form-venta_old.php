<form action="execute.php" target="_blank" method="POST" class="form-control p-4 bg-dark">
    <h1 class="display-5 text-light text-center mb-4">VENTA</h1>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <div class="form-floating">
                <select class="form-control form-select" name="local" id="local" required>
                    <option class="text-primary" value="Barcelona">Barcelona</option>
                    <option class="text-success" value="Mataró">Mataró</option>
                </select>
                <label for="local">Local</label>
            </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <div class="form-floating">
                <select class="form-control" name="metodo" id="metodo" required>
                    <option value="Tarjeta">Tarjeta/Bizum</option>
                    <option value="Efectivo">Efectivo</option>
                </select>
                <label for="metodo">Método de pago</label>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12" id="venta-desc">
            <div class="input-group mb-3" id="input-prod">
                <div class="form-floating w-50">
                    <input type="text" class="form-control" placeholder="Descripción" name="prod1" id="prod1">
                    <label for="prod1">Descripción del producto</label>
                </div>
                <div class="form-floating w-25">
                    <input type="number" step="0.01" onblur="findPrecioTotal()" class="form-control" placeholder="Precio" name="prec1" id="prec1">
                    <label for="prec1">Precio</label>
                </div>
                <div class="form-floating w-25">
                    <input type="number" onblur="findPrecioTotal()" class="form-control" placeholder="Cantidad" value=1 name="cant1" id="cant1">
                    <label for="cant1">Cantidad</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mx-auto mb-3">
            <button type="button" class="btn btn-secondary" onclick="addProd()">+ Añadir Producto</button>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-4 mb-3">
            <div class="form-floating">
                <input class="form-control" onblur="findTotal()" placeholder="Precio" type="number" step="0.01" name="precio" id="precio" required>
                <label for="precio">Precio €</label>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <div class="form-floating">
                <input class="form-control" onblur="findTotal()" placeholder="Iva 21%" type="number" step="0.1" value=21 name="iva" id="iva" required>
                <label for="iva">Iva 21%</label>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <div class="form-floating">
                <input class="form-control" onblur="findPrecio()" placeholder="Precio Final" step="0.01" type="number" name="precio-final" id="precio-final" required>
                <label for="precio-final">Precio Final €</label>
            </div>
        </div>
    </div>
    <div class="row">
        <input type="submit" name="guardar-venta" class="btn btn-success col-5 mx-auto" value="Guardar">
    </div>
</form>