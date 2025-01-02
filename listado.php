<div class="row">
    <ul class="nav nav-tabs bg-dark mt-2" id="list-tabs">
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
        echo '<li class="nav-item"><a class="nav-link ' . (!isset($_GET["filter"]) ? 'active' : 'text-light') . '" href="list">Todo</a></li>';

        // Iterate through filters
        foreach ($filters as $key => $label) {
            $activeClass = (isset($_GET["filter"]) && $_GET["filter"] == $key) ? 'active' : 'text-light';
            echo '<li class="nav-item"><a class="nav-link ' . $activeClass . '" href="list?filter=' . $key . '"
                            data-bs-toggle="tooltip" data-bs-title="' . $tooltip[$key] . '">' . $label . '</a></li>';
        }
        ?>
    </ul>
    <div class="container-fluid bg-dark mt-2 p-2" id="list-select">
        <form method="GET" action="list">
            <select class="form-select text-light bg-dark border-light" name="filter" onchange="this.form.submit()">
                <option value="" <?php echo !isset($_GET["filter"]) ? 'selected' : ''; ?>>Todo</option>
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

                foreach ($filters as $key => $label) {
                    $selected = (isset($_GET["filter"]) && $_GET["filter"] == $key) ? 'selected' : '';
                    echo '<option value="' . $key . '" ' . $selected . ' title="' . $tooltip[$key] . '">' . $label . '</option>';
                }
                ?>
            </select>
        </form>
    </div>

</div>
<div class="row">
    <div class="col-12 mt-2">
        <form action="list" method="POST">
            <div class="row">
                <div class="col-12 my-2">
                    <div class="searchBox mb-1">
                        <input class="searchInput w-100" type="text" name="search" placeholder="Buscar... (Dispositivo, Id, Servicio...)">
                        <button class="searchButton" type="submit">
                            <i class="bi bi-search" style="font-size: large;"></i>
                        </button>
                    </div>
                    <?php if (isset($_POST["search"])) { ?>
                        <a href="list" style="text-decoration: none; border-radius: 50px;" class="bg-secondary mx-auto logout noselect">
                            <span class="text">Quitar Filtro</span>
                            <span class="icon" style="border-color: white;">
                                <i class="text-light bi bi-x-circle"></i>
                            </span>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
// Get current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$cardsPerPage = $_SESSION["pag"];
$offset = ($page - 1) * $cardsPerPage;
$query_end = " ORDER BY i.id DESC LIMIT $cardsPerPage OFFSET $offset";
if ($cardsPerPage == 0) $query_end = " ORDER BY i.id DESC";

// Database connection
$pdo = connect();
$sql = "SELECT *, i.id as id, d.id as did, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, fecha as `date` FROM info_orden i
                    LEFT JOIN devolucion d ON (i.id = d.id_orden)";

$localQuery = "";
if ($_SESSION["local"] != null) {
    $localQuery = " AND `local` = '" . $_SESSION["local"] . "'";
}

// Create the query based on filters
if (!isset($_POST["search"])) {
    if (!isset($_GET["filter"])) {
        if ($_SESSION["local"] != null) {
            $stmt = $pdo->prepare($sql . "WHERE `local` = '" . $_SESSION["local"] . "'" . $query_end);
        } else {
            $stmt = $pdo->prepare($sql . $query_end);
        }
    } else {
        if ($_GET["filter"] == 5) {
            $stmt = $pdo->prepare($sql . " WHERE `garantia` != 0 AND d.id IS NULL $localQuery $query_end");
        } else if ($_GET["filter"] == 6) {
            $stmt = $pdo->prepare($sql . " WHERE d.id IS NOT NULL $localQuery $query_end");
        } else {
            $stmt = $pdo->prepare($sql . " WHERE `estado` = :estado AND d.id IS NULL $localQuery $query_end");
            $stmt->bindParam(':estado', $_GET["filter"]);
        }
    }
} else {
    echo '<p class="display-5 text-light">Resultados para <i>\'' . $_POST["search"] . '\'</i></p>';
    $search = "%" . $_POST["search"] . "%";
    $stmt = $pdo->prepare($sql . " WHERE `nombre_dispositivo` LIKE :search OR i.id LIKE :search OR
                `nombre` LIKE :search OR `local` LIKE :search OR `servicio` LIKE :search $localQuery ORDER BY i.id DESC");
    $stmt->bindParam(':search', $search);
}

// Execute the query to fetch results
$stmt->execute();

// Fetch the total number of rows (separate query for count)
$totalQuery = "SELECT COUNT(*) FROM info_orden i LEFT JOIN devolucion d ON (i.id = d.id_orden)";
if (isset($_GET["filter"])) {
    if ($_GET["filter"] == 5) {
        $totalQuery .= " WHERE `garantia` != 0 AND d.id IS NULL $localQuery";
    } else if ($_GET["filter"] == 6) {
        $totalQuery .= " WHERE d.id IS NOT NULL $localQuery";
    } else {
        $totalQuery .= " WHERE `estado` = :estado AND d.id IS NULL $localQuery";
    }
} else {
    if ($_SESSION["local"] != null) $totalQuery .= "WHERE `local` = '" . $_SESSION["local"] . "'";
}
$totalStmt = $pdo->prepare($totalQuery);
if (isset($_GET["filter"]) && $_GET["filter"] != 5 && $_GET["filter"] != 6) {
    $totalStmt->bindParam(':estado', $_GET["filter"]);
}
$totalStmt->execute();
$totalRows = $totalStmt->fetchColumn();
if ($cardsPerPage != 0) $totalPages = ceil($totalRows / $cardsPerPage);
else $totalPages = 0;
?>
<?php

// Pagination controls
if (!isset($_POST["search"])) {
    $filter = "";
    if (isset($_GET["filter"])) {
        $filter = "&filter=" . $_GET["filter"];
    }

    // Calculate total range dynamically
    $from = ($offset + 1) > $totalRows ? $totalRows : ($offset + 1);
    $to = ($offset + $cardsPerPage) > $totalRows ? $totalRows : ($offset + $cardsPerPage);
    if ($cardsPerPage == 0) $to = $totalRows;
?>
    <!-- SELECCION DE TICKETS POR PAGINA Y LOCAL A MOSTRAR -->
    <div class="row">
        <div class="col-12 mt-2 mb-4">
            <label for="pagSelect" class="text-light"><?php echo "<i>Mostrando $from - $to de $totalRows</i>&nbsp;"; ?></label>
            <select id="pagSelect" onchange="updatePages(this.value)">
                <option value=9 <?php echo $_SESSION["pag"] == 9 ? 'selected' : '' ?>>9</option>
                <option value=15 <?php echo $_SESSION["pag"] == 15 ? 'selected' : '' ?>>15</option>
                <option value=30 <?php echo $_SESSION["pag"] == 30 ? 'selected' : '' ?>>30</option>
                <option value=0 <?php echo $_SESSION["pag"] == 0 ? 'selected' : '' ?>>Todo</option>
            </select>
            &nbsp;
            <?php if ($_SESSION["login"] == "tecnico" || $_SESSION["login"] == "admin") { ?>
                <label for="sessionSelect" class="text-light">Local: </label>
                <select id="sessionSelect" onchange="updateSession(this.value)">
                    <option value="Todo" <?php echo $_SESSION["local"] == null ? 'selected' : '' ?>>Todo</option>
                    <option value="Barcelona" <?php echo $_SESSION["local"] == "Barcelona" ? 'selected' : '' ?>>Barcelona</option>
                    <option value="Mataró" <?php echo $_SESSION["local"] == "Mataró" ? 'selected' : '' ?>>Mataró</option>
                </select>
            <?php } ?>
        </div>
    </div>
<?php
    if ($cardsPerPage != 0) {
        echo '<div class="pagination d-flex">';

        // Previous Page Button
        if ($page > 1) {
            echo '<div class="w-100 p-1"><a href="list' . $filter . '&page=' . ($page - 1) . '" class="w-100 btn btn-secondary"><i class="bi bi-arrow-left"></i></a></div>';
        } else {
            echo '<div class="w-100 p-1"><a class="disabled w-100 btn btn-secondary"><i class="bi bi-arrow-left"></i></a></div>';
        }

        // Adjust the range dynamically
        $totalButtons = 5; // Total number of buttons (including current page)
        $startPage = max(1, $page - 2);
        $endPage = $startPage + $totalButtons - 1;

        // If endPage exceeds totalPages, shift the range back
        if ($endPage > $totalPages) {
            $endPage = $totalPages;
            $startPage = max(1, $endPage - $totalButtons + 1);
        }

        // Generate Page Buttons
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $page) {
                // Current Page (disabled)
                echo '<div class="p-1"><a class="disabled btn btn-primary w-100">' . $i . '</a></div>';
            } else {
                // Other Pages
                echo '<div class="p-1"><a href="list' . $filter . '&page=' . $i . '" class="btn btn-secondary w-100">' . $i . '</a></div>';
            }
        }

        // Next Page Button
        if ($page < $totalPages) {
            echo '<div class="w-100 p-1"><a href="list' . $filter . '&page=' . ($page + 1) . '" class="w-100 btn btn-secondary"><i class="bi bi-arrow-right"></i></a></div>';
        } else {
            echo '<div class="w-100 p-1"><a class="disabled w-100 btn btn-secondary"><i class="bi bi-arrow-right"></i></a></div>';
        }

        echo '</div>'; // Close pagination div
    }
}

// Start looping over the rows
$i = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // User restrictions
    //if ($_SESSION["local"] != null && $_SESSION["local"] != $row["local"]) continue;

    // Card layout start
    if ($i == 0) echo '<div class="row">';

    // Prepare description
    $desc = strlen($row["desc"]) > 22 ? substr($row["desc"], 0, 22) . '...' : $row["desc"];

    // Determine background and status
    $bg = "text-bg-light";
    $estado = "";
    if (!empty($row["did"])) {
        $bg = "text-bg-dark";
        $estado = " | <i class='bi bi-arrow-counterclockwise'></i> DEVUELTO";
    } elseif ($row["garantia"] != 0) {
        $bg = "text-bg-secondary";
        $estado = " | <i class='bi bi-file-text'></i> <a style='text-decoration:none;color:#FFA' href='list&id=" . $row["garantia"] . "'>GARANTÍA <i class='bi bi-arrow-right-short'></i></a>";
    } else {
        $estado = " | " . $pasos[$row["estado"]];
    }

    // Prepare service information
    if (!empty($row["servicio"])) {
        $serv = explode(": ", $row["servicio"])[0];
    } else {
        $serv = "PENDIENTE MODIFICAR";
    }

    $statusColor = !empty($row["did"]) ? "black" : $colores[$row["estado"]];

    // Generate card HTML
    echo '
                    <div class="col-lg-4 col-12">
                        <div class="card ' . $bg . ' mb-2 rounded">
                            <div class="card-header pt-3 text-center" style="border-bottom: 5px solid ' . $localColor[$row["local"] == "Barcelona" ? 0 : 1] . '; color:white;background-color:' . $statusColor . ';">
                                <h5>' . $serv . ' # ' . $row["id"] . '<br>' . $row["local"] . $estado . '</h5>
                            </div>';
    // Display status steps
    echo '
                            <div class="text-center pt-2">
                                <b>' . $pasosLargo[$row["estado"]] . '</b><br>';
    foreach ($colores as $key => $color) {
        echo '<div class="col-2 pt-2 d-inline-block" style="background-color:' . ($row["estado"] >= $key ? $color : "white") . '"></div>';
    }
    echo '
                            </div>
                            <div class="text-center mx-auto">';
    for ($j = 0; $j <= 4; $j++) {
        echo '<i class="bi bi-caret-up-fill px-4" style="color:' . ($row["estado"] != $j ? 'rgba(0,0,0,0)' : 'inherit') . '"></i>';
    }
    echo '</div>';

    // Action buttons based on user role and ticket state
    echo '<div class="w-100">'; // Start full-width container for buttons
    echo '<div class="input-group px-2 mb-2">';

    $disableLeft = true;
    $disableRight = true;

    if ($row["estado"] > 0) {
        $disableLeft = false;
    }

    if ($row["estado"] < 4) {
        $disableRight = false;
    }

    // Left button (state -1)
    echo '
                <a href="controller/execute.php?estado=' . ($row["estado"] - 1) . '&id=' . $row["id"] . '&pag=0" 
                class="stepButton rounded ' . ($disableLeft ? 'disabled' : '') . '" 
                style="pointer-events: ' . ($disableLeft ? 'none' : 'auto') . ';">
                    <span class="button__text">' . ($disableLeft ? '' : $pasos[$row["estado"] - 1]) . '</span>
                    <span class="button__icon" style="color: white; background-color:' . ($disableLeft ? 'grey' : $colores[$row["estado"] - 1]) . '">
                        <i class="bi bi-arrow-left"></i>
                    </span>
                </a>';

    // Right button (state +1)
    echo '
                <a href="controller/execute.php?estado=' . ($row["estado"] + 1) . '&id=' . $row["id"] . '&pag=0" 
                class="stepButton rounded ' . ($disableRight ? 'disabled' : '') . '" 
                style="pointer-events: ' . ($disableRight ? 'none' : 'auto') . ';">
                    <span class="button__text">' . ($disableRight ? '' : $pasos[$row["estado"] + 1]) . '</span>
                    <span class="button__icon" style="color: white; background-color:' . ($disableRight ? 'grey' : $colores[$row["estado"] + 1]) . '">
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </a>';

    // Close the input group
    echo '</div></div>';

    $pastDate = new DateTime($row["date"]);
    $now = new DateTime();
    $daysPassed = $now->diff($pastDate)->days;

    $index = $daysPassed > 4 ? 4 : $daysPassed;
    $colD = $row["estado"] < 4 ? (empty($row["did"]) ? $colorDias[$index] : "white") : "white";
    echo '
                            <div class="card-body">
                                <p class="card-text"><b>Nombre:</b> ' . $row["nombre"] . '</p>
                                <p class="card-text"><b>Dispositivo:</b> ' . $row["nombre_dispositivo"] . '</p>
                                <p class="card-text"><b>Descripción:</b> ' . $desc . '</p>
                                <p class="card-text date-highlight text-light">' . $row["fecha"] . ' · <span style="color:' . $colD . '">hace ' . $daysPassed . ' día(s)</span></p>
                            </div> 
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item ' . $bg . '"><b>Precio:</b> ' . $row["precio"] . '€ (+ IVA ' . $row["iva"] . '%) = <b>' . $row["precio-final"] . '€</b></li>
                            </ul>
                            <div class="card-body">';


    // echo '<a href="list&id=' . $row["id"] . '" class="infoTicket btn btn-primary rounded w-100"><i class="bi bi-info-circle"></i> Detalles</a>';
    echo '<a href="list&id=' . $row["id"] . '" class="cssbuttons-io-button w-50 mx-auto">Detalles<div class="icon"><i class="bi bi-arrow-right"></i></div></a>';
    echo '</div>
                        </div>
                    </div>';

    // Close row after 3 columns
    if ($i == 2) {
        echo '</div>';
        $i = 0;
    } else {
        $i++;
    }
}

?>