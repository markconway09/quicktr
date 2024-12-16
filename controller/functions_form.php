<?php
function insertarBDS($garantia = 0)
{
    // Initialize variables with default values
    $servicio = "No especificado";
    $pV = null;
    $cV = null;
    $doc = "No especificado";
    $local = "No especificado";
    $dir = "No especificado";
    $cp = "No especificado";
    $email = "No especificado";
    $tel = "";
    $nombre = "No especificado";
    $ins_d = "";
    $ins_p = 0;
    $metodo = "";
    $disp = "No especificado";
    $precio = 0;
    $descuento = 0;
    $iva = 0;
    $preciofinal = 0;
    $razon = "No especificado";
    $dept = "No especificado";
    $desc = "Sin descripción";
    $cod_ref = null;

    // Handle phone number with country code
    if (!empty($_POST["countryCode"])) {
        $cc = ($_POST["countryCode"][0] !== '+') ? '+' . $_POST["countryCode"] : $_POST["countryCode"];
        if (isset($_POST["tel"])) $tel = $cc . $_POST["tel"];
    } else {
        if (isset($_POST["tel"])) $tel = $_POST["tel"];
    }

    // Handle other POST variables
    if (!empty($_POST["servicio"]) && isset($_POST["servicio2"])) $servicio = $_POST["servicio"] . ": " . $_POST["servicio2"];
    $doc = $_POST["doc"] ?? $doc;
    $local = $_POST["local"] ?? $local;
    $razon = $_POST["razon"] ?? $razon;
    $dept = $_POST["dept"] ?? $dept;
    $desc = $_POST["motivo"] ?? $desc;
    $nombre = $_POST["nombre"] ?? $nombre;
    $dir = $_POST["direccion"] ?? $dir;
    $cp = $_POST["cp"] ?? $cp;
    $email = $_POST["email"] ?? $email;
    $ins_d = $_POST["insumo_desc"] ?? $ins_d;
    $ins_p = $_POST["insumo_precio"] ?? $ins_p;
    $metodo = $_POST["metodo"] ?? $metodo;
    $disp = $_POST["dispositivo"] ?? $disp;
    $precio = $_POST["precio"] ?? $precio;
    $descuento = $_POST["descuento"] ?? $descuento;
    $iva = $_POST["iva"] ?? $iva;
    $preciofinal = $_POST["precio-final"] ?? $preciofinal;
    if (isset($_POST["cod_ref"])) if ($_POST["cod_ref"] != "") $cod_ref = $_POST["cod_ref"];

    if (isset($_POST["socio"]) && !empty($_POST["nacimiento"])) {
        $nac = $_POST["nacimiento"];
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $codigo = '';
        for ($i = 0; $i < 10; $i++) {
            $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }
    } else {
        $nac = null;
        $codigo = null;
    }

    // Prepare database connection and insert query
    $pdo = connect();
    $stmt = $pdo->prepare("INSERT INTO info_orden 
        VALUES (null, :nom, :tel, :doc, :ser, :email, :direccion, :cp, :nac, :preciosV,
                :cantV, :precio, :descuento, :iva, :final, :ins_d, :ins_p, null, :disp,
                :descr, null, :loc, :fecha, null, :garantia, 0, :razon, :dept, :codigo, :cod_ref)");

    // Bind parameters
    $stmt->bindParam(':nom', $nombre);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':doc', $doc);
    $stmt->bindParam(':ser', $servicio);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':direccion', $dir);
    $stmt->bindParam(':cp', $cp);
    $stmt->bindParam(':nac', $nac);
    $stmt->bindParam(':preciosV', $pV);
    $stmt->bindParam(':cantV', $cV);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':descuento', $descuento);
    $stmt->bindParam(':iva', $iva);
    $stmt->bindParam(':final', $preciofinal);
    $stmt->bindParam(':ins_d', $ins_d);
    $stmt->bindParam(':ins_p', $ins_p);
    $stmt->bindParam(':disp', $disp);
    $stmt->bindParam(':descr', $desc);
    $stmt->bindParam(':loc', $local);
    $date = date('Y-m-d');
    $stmt->bindParam(':fecha', $date);
    $stmt->bindParam(':garantia', $garantia);
    $stmt->bindParam(':razon', $razon);
    $stmt->bindParam(':dept', $dept);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->bindParam(':cod_ref', $cod_ref);

    // Execute the statement and handle any errors
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        error_log($e->getMessage()); // Log error message
        error_log($stmt->queryString); // Log SQL query for debugging
        echo "An error occurred. Please try again later.";
    }

    return $pdo->lastInsertId(); // Return last inserted ID
}

function subirFirma($id, $path)
{
    // SUBIR FIRMA
    $folderPath = $path;
    $image_parts = explode(";base64,", $_POST['sign']);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $image_id = uniqid() . '.' . $image_type;
    $file = $folderPath . $image_id;
    $saveResult = file_put_contents($file, $image_base64);
    if ($saveResult) {
        $pdo = connect();
        $stmt = $pdo->prepare("INSERT INTO firma VALUES (null, :id, :archivo)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':archivo', $image_id);

        try {
            $stmt->execute(); // Insert record into the database
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

function insertarFotos($id = null)
{
    $pdo = connect();
    if($id == null && isset($_POST["id"])) $id = $_POST["id"];
    if(isset($_POST["desc"])){
        $stmt = $pdo->prepare("UPDATE info_orden SET `desc` = :d WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':d', $_POST["desc"]);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    $targetDir = "../fotos/";
    foreach ($_FILES['images']['name'] as $key => $name) {
        $fileTmpPath = $_FILES['images']['tmp_name'][$key];
        $fileName = basename($name);
        $targetFilePath = $targetDir . $fileName;
        // Move the uploaded file to the target directory
        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
            $stmt = $pdo->prepare("INSERT INTO foto (id_orden, archivo) VALUES (:id, :archivo)");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':archivo', $fileName);
            try {
                $stmt->execute();
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }
}
