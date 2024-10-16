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