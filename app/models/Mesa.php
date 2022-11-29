<?php
class Mesa{
    public $id;
    public $codigo_mesa;
    public $id_empleado;
    public $estado;

    public function __construct(){}

    public static function crearMesa($codigo, $idEmpleado, $estado){
        $mesa = new Mesa();
        $mesa->codigo_mesa = $codigo;
        $mesa->id_empleado = $idEmpleado;
        $mesa->estado = $estado;
        return $mesa;
    }

    public static function insertarMesaDB($mesa){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo_mesa, id_empleado, estado)
        VALUES (:codigo, :id_empleado, :estado)");
        try{
            $consulta->bindValue(":codigo", $mesa->codigo_mesa, PDO::PARAM_STR);
            $consulta->bindParam(":id_empleado", $mesa->id_empleado);
            $consulta->bindValue(":estado", $mesa->estado, PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
    }

    public static function obtenerMesaPorId($mesaId){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE id = :id");
        $consulta->bindValue(":id", $mesaId, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject("Mesa");
    }

    public static function modificarMesa($mesaParam){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET codigo_mesa = :codigo, id_empleado = :id_emp, estado = :estado 
        WHERE id = :id");
        try{
            $consulta->bindValue(":codigo", $mesaParam->codigo_mesa, PDO::PARAM_STR);
            $consulta->bindValue(":id_emp", $mesaParam->id_empleado, PDO::PARAM_INT);
            $consulta->bindValue(":estado", $mesaParam->estado, PDO::PARAM_STR);
            $consulta->bindValue(":id", $mesaParam->id, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function eliminarMesa($mesaId){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM mesas WHERE id = :id");
        try{
            $consulta->bindValue(":id", $mesaId, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function obtenerMesasPorIdEmpleado($id_empleado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE id_empleado = :id_emp");
        $consulta->bindValue(":id_emp", $id_empleado, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject("Mesa");
    }

    // Se obtiene la primera mesa cerrada que se recupere de la DB
    public static function obtenerPrimeraMesaCerrada(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE estado = 'Cerrada' LIMIT 1");
        $consulta->execute();

        return $consulta->fetchObject("Mesa");
    }

    public static function obtenerMesaPorIdPedido($pedido_id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE id = (SELECT mesa_id FROM pedidos WHERE id = :id_pedido)");
        $consulta->bindValue(':id_pedido', $pedido_id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject("Mesa");
    }

    // Si existe una mesa cerrada, utilizo esta función para 'abrirla'. Devuelvo el id de la mesa abierta. De lo contrario
    // al no existir mesas disponibles para abrir, devuelvo 'false'.
    public static function abrirMesaCerrada($estado = "Cerrada"){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $mesaLibre = self::obtenerPrimeraMesaCerrada();
        if ($mesaLibre){
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
            $consulta->bindValue(":estado", $estado, PDO::PARAM_STR);
            $consulta->bindValue(":id", $mesaLibre->id, PDO::PARAM_INT);
            $consulta->execute();
            return $mesaLibre->id;
        }
        return false;
    }
    
    public static function actualizarEstadoMesa($mesa, $estado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estado = :estado where id = :id");
        $consulta->bindValue(":estado", $estado, PDO::PARAM_STR);
        $consulta->bindValue(":id", $mesa->id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount() > 0;
    }
}
?>