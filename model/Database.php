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
            `desc_tecnico` = ?
            WHERE id = ?";

        // Assuming you have a PDO connection
        $stmt = $this->pdo->prepare($sql);
        try{
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
                $ticket->id
            ]);
        } catch (Exception $e){
            logError($e->getMessage());
        }
    }
    
}
