<?php
    if(!isset($_SESSION["login"])) header('Location: index.php');
    if($_SESSION["login"]!="admin") header('Location: index.php');

    // IMPORT FUNCTIONS
    require_once "controller/functions.php";
    $db = new Database();
    $pdo = $db->pdo;
    $stmt = $pdo->prepare("SELECT * FROM user");
    try {
        $stmt->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }

    if(isset($_POST["insertuser"])) {
        $stmt = $pdo->prepare("INSERT INTO `user` VALUES (null, :user, :pass, :loc, :tipo)");
        if(isset($_POST["insertuser"])){
            $user = $_POST["newuser"];
            $pass = password_hash($_POST["newpass"], PASSWORD_DEFAULT);
            $loc = $_POST["local"];
            $tipo = $_POST["tipo"];
        } else {
            $user = "repartidor";
            $pass = password_hash("Barcelona123", PASSWORD_DEFAULT);
            $loc = "Barcelona";
            $tipo = "repartidor";
        }
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':pass', $pass);
        $stmt->bindParam(':loc', $loc);
        $stmt->bindParam(':tipo', $tipo);
        try {
            $stmt->execute();
            echo '<meta http-equiv="refresh" content="0"/>';
        } catch(PDOException $e){
            logError($e->getMessage());
        }
    }
?>
<div class="container bg-dark p-2">
    <form action="" method="POST">
        <div class="d-flex mb-2">
            <input class="flex-fill" type="text" name="newuser" id="user" placeholder="Nombre usuario">
            <input class="flex-fill" type="password" name="newpass" id="pass" placeholder="Contraseña">
            <select class="flex-fill" name="local" id="local">
                <option value="">(Sin local asignado)</option>
                <option value="Barcelona">Barcelona</option>
                <option value="Mataró">Mataró</option>
            </select>
            <select class="flex-fill" name="tipo" id="tipo">
                <option value="dependiente">Dependiente</option>
                <option value="tecnico">Técnico</option>
                <option value="repartidor">Repartidor</option>
                <option value="admin">Admin</option>
            </select>
            <input type="submit" name="insertuser" class="fileButton" value="Añadir usuario">
        </div>
    </form>
    <table class="table table-dark table-striped table-hover text-bg-dark">
        <thead><tr><th scope="col">#</th><th scope="col">Nombre usuario</th><th scope="col">Local</th><th scope="col">Tipo</th></tr></thead>
        <?php
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        ?>

            <tr>
                <th scope="row"><?php echo $row["id"]; ?></th>
                <td><?php echo $row["username"]; ?></td>
                <td><?php echo $row["local"]; ?></td>
                <td><?php echo ucfirst($row["tipo"]); ?></td>
            </tr>
        
        <?php
        }
        ?>
    </table>
</div>