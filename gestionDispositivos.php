<div class="container p-5 text-bg-dark">
    <h1 class="text-center">Gestión de Dispositivos</h1>
    <div class="d-flex justify-content-around mt-4 p-5">
        <div class="text-center p-5">
            <h2>Subir CSV Dispositivos</h2>
            <form action="controller/importModelos.php" target="_blank" method="post" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <input disabled type="file" name="csv_file" id="csv_file" accept=".csv" class="form-control-file" required>
                    <button disabled type="submit" class="btn btn-primary mt-2">Subir</button>
                </div>
            </form>
        </div>
        <div class="text-center p-5">
            <h2>Exportar CSV Dispositivos</h2>
            <form action="controller/exportModelos.php" target="_blank" method="post">
                <button type="submit" class="btn btn-success">Exportar</button>
            </form>
        </div>
    </div>
</div>