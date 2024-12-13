<div class="container border my-4 pb-4 px-4 text-bg-dark rounded">
    <?php if($_SESSION["login"] == "admin"){ ?>
    <div class="row">
        <ul class="nav nav-tabs bg-dark mt-2">
            <?php
                $filters = [
                    "0" => "Diagnóstico",
                    "1" => "Aprobación",
                    "2" => "Reparación",
                    "3" => "Terminado",
                    "4" => "Entregado",
                    "5" => "Garantía",
                    "6" => "Devoluciones",
                ];
                $tooltip = [
                    "Pendiente de diagnosticar el problema y dar presupuesto.",
                    "Esperando la aprobación del cliente.",
                    "Ticket aprobado, pendiente de reparación.",
                    "Reparación finalizada, esperando al cliente.",
                    "Ticket cerrado, entregado al cliente.",
                    "",
                    ""
                ];
                // Default case for "Todo"
                echo '<li class="nav-item"><a class="nav-link text-light" href="?pag=list">Todo</a></li>';
                // Iterate through filters
                foreach ($filters as $key => $label) {
                    echo '<li class="nav-item"><a class="nav-link text-light" href="?pag=list&filter=' . $key . '"
                    data-bs-toggle="tooltip" data-bs-title="'.$tooltip[$key].'">' . $label . '</a></li>';
                }
            ?>
            <li class="nav-item"><a class="nav-link active" href="?pag=entregas">Entregas</a></li>
        </ul>
    </div>
    <?php } ?>
    <h2 class="display-5 mt-4 text-center">Entregas a tienda</h2>
    <hr>
<!-- FORMULARIO -->
    <form action="" method="POST">
        <div class="row">
            <div class="col-5">
                <div class="form-floating">
                    <input class="form-control" placeholder="Nombre" type="text" id="nombre" name="nombre" required>
                    <label for="nombre" class="text-dark">Nombre</label>
                </div>
            </div>
            <div class="col-7">
                <div class="input-group">
                    <div class="form-floating">
                        <select class="form-control" id="de" name="de" required>
                            <option value="" disabled selected>Seleccionar Ubicación</option>
                            <option value="Barcelona Oficina">Barcelona Oficina</option>
                            <option value="Barcelona Tienda">Barcelona Tienda</option>
                            <option value="Mataró">Mataró</option>
                            <option value="Compras">Compras</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <label for="de" class="text-dark">De</label>
                    </div>

                    <div class="form-floating">
                        <select class="form-control" id="a" name="a" required>
                            <option value="" disabled selected>Seleccionar Destino</option>
                            <option value="Barcelona Oficina">Barcelona Oficina</option>
                            <option value="Barcelona Tienda">Barcelona Tienda</option>
                            <option value="Mataró">Mataró</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <label for="a" class="text-dark">A</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2 mx-auto">
            <button type="submit" name="insert_entrega" class="btn btn-secondary" style="height: 100%;">Insertar</button>
        </div>
    </form>
<!-- END FORMULARIO -->
    <hr>
    <?php
    require_once 'controller/functions.php';


    // INSERT INTO ENTREGAS
    if(isset($_POST["insert_entrega"])){
        $pdo = connect();
        $stmt = $pdo->prepare("INSERT INTO entregas VALUES (null, :nombre, :de, :a, 0)");
        $stmt->bindParam(':nombre', $_POST["nombre"]);
        $stmt->bindParam(':de', $_POST["de"]);
        $stmt->bindParam(':a', $_POST["a"]);
        try {
            $stmt->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // END INSERT

    // CHANGE ESTADO
    if(isset($_POST["mas_estado"])){
        $estado = $_POST["estado"] + 1;
        if($estado <= 2){
            $pdo = connect();
            $stmt = $pdo->prepare("UPDATE entregas SET estado = :estado WHERE id = :id");
            $stmt->bindParam(':id', $_POST["id"]);
            $stmt->bindParam(':estado', $estado);
            try {
                $stmt->execute();
            } catch(PDOException $e){
                echo $e->getMessage();
            }
        }
    }
    if(isset($_POST["menos_estado"])){
        $estado = $_POST["estado"] - 1;
        if($estado >= 0) {
            $pdo = connect();
            $stmt = $pdo->prepare("UPDATE entregas SET estado = :estado WHERE id = :id");
            $stmt->bindParam(':id', $_POST["id"]);
            $stmt->bindParam(':estado', $estado);
            try {
                $stmt->execute();
            } catch(PDOException $e){
                echo $e->getMessage();
            }
        }
    }
    // END CHANGE

    $estados = ["PENDIENTE", "EN CAMINO", "ENTREGADO"];
    $colores = ["#f54f45", "#f5c94a", "#60e855"];

    $pdo = connect();
    $stmt = $pdo->prepare("SELECT * from entregas WHERE estado = 0 OR estado = 1 ORDER BY id DESC");
    try {
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Check if results are not empty
        if (count($results) > 0) {
            echo "<table border='1' class='table table-secondary'>";
            echo "<thead><tr><th>Nombre</th><th>De</th><th>A</th><th colspan=2>Estado</th></tr></thead>";
            echo "<tbody>";

            // Loop through results and display each row
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . htmlspecialchars($row['nombre']) . "</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . htmlspecialchars($row['ubicacion']) . "</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . htmlspecialchars($row['destino']) . "</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . $estados[$row['estado']] . "</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                echo "<input type='hidden' name='estado' value='" . $row['estado'] . "'>";
                echo "<button type='submit' name='menos_estado' class='btn btn-primary'>&lt;</button>&nbsp;";
                echo "<button type='submit' name='mas_estado' class='btn btn-primary'>&gt;</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        }
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    $stmt = $pdo->prepare("SELECT * from entregas WHERE estado = 2 ORDER BY id DESC LIMIT 5");
    try {
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Check if results are not empty
        if (count($results) > 0) {
            echo "<table border='1' class='table table-secondary opacity-50'>";
            echo "<thead><tr><th>Nombre</th><th>De</th><th>A</th><th colspan=2>Estado</th></tr></thead>";
            echo "<tbody>";

            // Loop through results and display each row
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . htmlspecialchars($row['nombre']) . "</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . htmlspecialchars($row['ubicacion']) . "</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . htmlspecialchars($row['destino']) . "</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>" . $estados[$row['estado']] . "</td>";
                echo "<td style='background:" . $colores[$row["estado"]] . "'>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                echo "<input type='hidden' name='estado' value='" . $row['estado'] . "'>";
                echo "<button type='submit' name='menos_estado' class='btn btn-primary'>&lt;</button>&nbsp;";
                echo "<button type='submit' name='mas_estado' class='btn btn-primary'>&gt;</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        }
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    ?>
        </tbody>
    </table>
</div>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>