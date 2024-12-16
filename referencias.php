<?php
    require_once 'controller/functions.php';
    $pdo = connect();
    $stmt = $pdo->prepare("SELECT `nombre`, `codigo_socio` FROM info_orden WHERE `codigo_socio` IS NOT NULL");
    $stmt->execute();
    
    if ($stmt->rowCount()) {
?>
    <table class="table table-dark table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Código Socio</th>
                <th>Veces Usado</th>
            </tr>
        </thead>
        <tbody>
<?php
        // Loop through the results and execute the second query to get the order count for each codigo_socio
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $nombre = $row['nombre'];
            $codigo_socio = $row['codigo_socio'];

            // Execute the second query to count orders with the matching codigo_usado
            $stmt2 = $pdo->prepare("SELECT COUNT(id) as veces_usado FROM info_orden WHERE codigo_usado = :cod");
            $stmt2->bindParam(':cod', $codigo_socio);
            $stmt2->execute();
            $fila = $stmt2->fetch(PDO::FETCH_ASSOC);
            $veces_usado = $fila['veces_usado'];

            // Display the results in a table row
            echo '<tr>';
            echo '<td>' . htmlspecialchars($nombre) . '</td>';
            echo '<td>' . htmlspecialchars($codigo_socio) . '</td>';
            echo '<td>' . htmlspecialchars($veces_usado) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo "No hay códigos usados.";
    }
?>