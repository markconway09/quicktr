<?php
class Database
{
    public $pdo;
    private $_host = "localhost";
    private $_dbname = "quicktrc_formulario";
    private $_user = "uvzcmq8ynnon4";
    private $_pass = "quicktr2024";

    public function __construct()
    {
        $db = "mysql:host=$this->_host;dbname=$this->_dbname";
        $this->pdo = new PDO($db, "$this->_user", "$this->_pass");
    }

    public function fetchId($id)
    {
        $q = "SELECT *, i.id as id, f.id as id_firma, archivo as firma FROM info_orden i
                LEFT JOIN `firma` f ON (i.id = f.id_orden) WHERE i.id = :id";
        $stmt = $this->pdo->prepare($q);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $ticket = new Ticket();
            foreach ($data as $key => $value) {
                $key = str_replace("-", "_", $key);
                if (property_exists($ticket, $key)) {
                    $ticket->$key = $value;
                }
            }
            return $ticket;
        }

        return null;
    }

    public function fetchAll()
    {
        $q = "SELECT *, o.id as id, d.id as did, f.id as fid, s.archivo as firma, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, fecha as `date`
                FROM `info_orden` o
                LEFT JOIN `devolucion` d ON (d.id_orden = o.id)
                LEFT JOIN `factura` f ON (f.id_orden = o.id)
                LEFT JOIN `firma` s ON (s.id_orden = o.id)";
        $stmt = $this->pdo->prepare($q);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return [];
        }

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tickets = [];

        foreach ($results as $data) {
            $ticket = new Ticket();
            foreach ($data as $key => $value) {
                if (property_exists($ticket, $key)) {
                    $ticket->$key = $value;
                }
            }
            $tickets[] = $ticket;
        }

        return $tickets;
    }

    public function insertTicket(Ticket $ticket)
    {
        $sql = "INSERT INTO info_orden SET 
            id = ?,
            nombre = ?,
            telefono = ?,
            documento = ?,
            servicio = ?,
            email = ?,
            direccion = ?,
            cp = ?,
            fecha_nacimiento = ?,
            precio = ?,
            descuento = ?,
            iva = ?,
            `precio-final` = ?,
            insumo_desc = ?,
            insumo_precio = ?,
            metodo = ?,
            `nombre_dispositivo` = ?,
            `desc` = ?,
            `desc_tecnico` = ?,
            `local` = ?,
            fecha = ?,
            fecha_pago = ?,
            garantia = ?,
            estado = ?,
            razon = ?,
            dept = ?,
            codigo_socio = ?,
            codigo_usado = ?";


        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([
                null,
                $ticket->nombre,
                $ticket->telefono,
                $ticket->documento,
                $ticket->servicio,
                $ticket->email,
                $ticket->direccion,
                $ticket->cp,
                $ticket->fecha_nacimiento,
                $ticket->precio,
                $ticket->descuento,
                $ticket->iva,
                $ticket->precio_final,
                $ticket->insumo_desc,
                $ticket->insumo_precio,
                $ticket->metodo,
                $ticket->nombre_dispositivo,
                $ticket->desc,
                $ticket->desc_tecnico,
                $ticket->local,
                $ticket->fecha,
                $ticket->fecha_pago,
                $ticket->garantia,
                $ticket->estado,
                $ticket->razon,
                $ticket->dept,
                $ticket->codigo_socio,
                $ticket->codigo_usado
            ]);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            logError($e->getMessage());
        }
    }

    public function isDuplicate(Ticket $ticket)
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare("
            SELECT nombre, nombre_dispositivo 
            FROM info_orden 
            WHERE fecha = :fecha AND nombre = :nombre AND nombre_dispositivo = :disp
        ");
        $stmt->bindParam(':fecha', $ticket->fecha);
        $stmt->bindParam(':nombre', $ticket->nombre);
        $stmt->bindParam(':disp', $ticket->nombre_dispositivo);

        try {
            $stmt->execute();
            // Fetch the first matching record
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log or handle the error appropriately
            logError($e->getMessage());
            return false;
        }
    }

    public function insertPhotos($id, $photos)
    {
        $targetDir = "fotos/";
        foreach ($photos['name'] as $key => $name) {
            $fileTmpPath = $photos['tmp_name'][$key];
            $fileName = basename($name);
            $targetFilePath = $targetDir . $fileName;
            // Move the uploaded file to the target directory
            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                $stmt = $this->pdo->prepare("INSERT INTO foto (id_orden, archivo) VALUES (:id, :archivo)");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':archivo', $fileName);
                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    logError($e->getMessage());
                }
            }
        }
    }

    public function insertSignature($id, $sig)
    {
        // SUBIR FIRMA
        $folderPath = "firmas/";
        $image_parts = explode(";base64,", $sig);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $image_id = uniqid() . '.' . $image_type;
        $file = $folderPath . $image_id;
        $saveResult = file_put_contents($file, $image_base64);
        if ($saveResult) {
            $pdo = $this->pdo;
            $stmt = $pdo->prepare("INSERT INTO firma VALUES (null, :id, :archivo)");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':archivo', $image_id);

            try {
                $stmt->execute(); // Insert record into the database
            } catch (PDOException $e) {
                logError($e->getMessage());
            }
        }
    }

    public function fetchInsumos($id = null)
    {
        $pdo = $this->pdo;
        if($id == null) {
            $stmt = $pdo->prepare("SELECT * FROM `insumos` WHERE estado > 0 AND estado < 3 ORDER BY id DESC");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM `insumos` WHERE id_orden = :id");
            $stmt->bindParam(":id", $id);
        }
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchInsumoFromId($id_ins)
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare("SELECT * FROM `insumos` WHERE id = :id");
        $stmt->bindParam(":id", $id_ins);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertInsumo(Insumo $ins)
    {
        $pdo = $this->pdo;
        // Prepare the SQL statement
        $stmt = $pdo->prepare("
            INSERT INTO `insumos` (fecha, nombre, precio, local, estado, id_orden)
            VALUES (:fecha, :nombre, :precio, :loc, :estado, :id_orden)");

        // Bind the parameters from the $ins array
        $stmt->bindParam(":fecha", $ins->fecha);
        $stmt->bindParam(":nombre", $ins->nombre);
        $stmt->bindParam(":precio", $ins->precio);
        $stmt->bindParam(":loc", $ins->local);
        $stmt->bindParam(":estado", $ins->estado);
        $stmt->bindParam(":id_orden", $ins->id_orden);

        try {
            $stmt->execute();
        } catch (Exception $e) {
            logError($e->getMessage());
        }
    }

    public function updateTicket($ticket)
    {
        $sql = "UPDATE info_orden SET 
            nombre = ?,
            documento = ?,
            email = ?,
            direccion = ?,
            cp = ?,
            telefono = ?,
            precio = ?,
            descuento = ?,
            iva = ?,
            `precio-final` = ?,
            insumo_desc = ?,
            insumo_precio = ?,
            `nombre_dispositivo` = ?,
            `desc` = ?,
            `desc_tecnico` = ?,
            `estado` = ?,
            `metodo` = ?,
            `fecha_pago` = ?
            WHERE id = ?";

        // Assuming you have a PDO connection
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([
                $ticket->nombre,
                $ticket->documento,
                $ticket->email,
                $ticket->direccion,
                $ticket->cp,
                $ticket->telefono,
                $ticket->precio,
                $ticket->descuento,
                $ticket->iva,
                $ticket->precio_final,
                $ticket->insumo_desc,
                $ticket->insumo_precio,
                $ticket->nombre_dispositivo,
                $ticket->desc,
                $ticket->desc_tecnico,
                $ticket->estado,
                $ticket->metodo,
                $ticket->fecha_pago,
                $ticket->id
            ]);
        } catch (Exception $e) {
            logError($e->getMessage());
        }
    }

    public function logChange($user, $desc, $orden)
    {
        $fecha = new DateTime();
        $fecha = $fecha->format('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare("INSERT INTO historial_cambios VALUES (null, :user, :fecha, :descripcion, :id_orden)");
        $stmt->bindParam(":user", $user);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->bindParam(":descripcion", $desc);
        $stmt->bindParam(":id_orden", $orden);
        try {
            $stmt->execute();
        } catch (Exception $e) {
            logError($e->getMessage());
        }
    }

    public function fetchTicketHistory($ticket)
    {
        $q = "SELECT * FROM historial_cambios WHERE id_orden = :id";
        $stmt = $this->pdo->prepare($q);
        $stmt->bindParam(':id', $ticket->id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return $data;
        }

        return null;
    }
}
