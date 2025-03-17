<div class="container p-5 text-bg-dark">
        <h1 class="text-center">Subir CSV Dispositivos</h1>
        <form action="controller/importModelos.php" target="_blank" method="post" enctype="multipart/form-data" class="text-center mt-4">
            <div class="form-group mb-3">
                <input type="file" name="file" id="file" accept=".csv" class="form-control-file" required>
                <button type="submit" class="btn btn-primary">Subir</button>
            </div>
        </form>
    </div>