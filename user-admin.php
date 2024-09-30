<?php
    if(!isset($_SESSION["login"])) header('Location: index.php');
    if($_SESSION["login"]=="user") header('Location: index.php');

    // IMPORT FUNCTIONS
    require_once "functions.php";
    $pdo = connect();
    $stmt = $pdo->prepare("SELECT * FROM user");
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
?>
<button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#newUser">+ Nuevo Usuario</button>
<table class="table table-dark table-striped text-bg-dark">
    <thead><tr><th scope="col">#</th><th scope="col">Nombre usuario</th><th scope="col">Contraseña</th><th scope="col">Local</th><th scope="col">Tipo</th></tr></thead>
    <?php
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    ?>

        <tr>
            <th scope="row"><?php echo $row["id"]; ?></th>
            <td><?php echo $row["username"]; ?></td>
            <td>
                <button class="btn btn-sm btn-secondary"
                        data-bs-toggle="modal"
                        data-bs-target="#passModal"
                        data-bs-whatever="<?php echo $row["username"]; ?>">
                    Cambiar
                </button>
            </td>
            <td><?php echo $row["local"]; ?></td>
            <td><?php echo $row["id"]===1?"Admin":"Usuario"; ?></td>
        </tr>
    
    <?php
    }
    ?>
</table>

<!-- Modal Edit -->
<div class="modal fade" id="passModal" tabindex="-1" aria-labelledby="passModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="passModalLabel">Cambiar Contraseña</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input class="form-control" placeholder="Username" type="text" name="pass" id="pass">
        <input class="form-control" placeholder="Password" type="password" name="pass" id="pass">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal New User -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="userModalLabel">Nuevo Usuario</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>