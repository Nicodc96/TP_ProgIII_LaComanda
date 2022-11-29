<?php

require_once "./db/AccesoDatos.php";
require_once "./models/Area.php";

class Orden{
    public $id;
    public $area_orden;
    public $id_pedido;
    public $estado;
    public $descripcion;
    public $precio;
    public $tiempo_inicio;
    public $tiempo_fin;
    public $tiempo_estimado;

    public function __construct(){}

    public static function crearOrden($area_orden, $id_pedido, $estado, $descripcion, $precio, $tiempo_inicio){
        $orden = new Orden();
        $orden->area_orden = $area_orden;
        $orden->id_pedido = $id_pedido;
        $orden->estado = $estado;
        $orden->descripcion = $descripcion;
        $orden->precio = $precio;
        $orden->tiempo_inicio = $tiempo_inicio;
        $orden->tiempo_fin = null;
        $orden->tiempo_estimado = null;
        return $orden;
    }

    public function calcularTiempoTerminado(){
        $nuevo_tiempo = new DateTime($this->tiempo_inicio);
        $nuevo_tiempo = $nuevo_tiempo->modify("+". $this->tiempo_estimado . " minutes");
        $this->tiempo_fin = $nuevo_tiempo->format("Y-m-d H:i:s");
    }

    public static function insertarOrdenDB($orden){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ordenes (area_orden, id_pedido, estado, descripcion, precio, tiempo_inicio)
        VALUES (:area_orden, :id_pedido, :estado, :descripcion, :precio, :tiempo_inicio)");
        try{
            $consulta->bindValue(":area_orden", $orden->area_orden, PDO::PARAM_INT);
            $consulta->bindValue(":id_pedido", $orden->id_pedido, PDO::PARAM_INT);
            $consulta->bindValue(":estado", $orden->estado, PDO::PARAM_STR);
            $consulta->bindValue(":descripcion", $orden->descripcion, PDO::PARAM_STR);
            $consulta->bindValue(":precio", $orden->precio, PDO::PARAM_STR);
            $consulta->bindValue(":tiempo_inicio", $orden->tiempo_inicio, PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ordenes");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Orden");
    }

    public static function filtrarOrdenesTerminadas($lista_ordenes, $estado){
        $lista_filtrada = array();
        foreach($lista_ordenes as $orden){
            if(strcmp($orden->estado, "Listo para servir") == 0){
                array_push($lista_filtrada, $orden);
            }
        }
        return $lista_filtrada;
    }

    public static function actualizarOrden($ordenParam){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ordenes 
        SET estado = :estado, tiempo_fin = :tiempo_fin, tiempo_estimado = :tiempo_estimado
        WHERE id = :id");
        try{
            $consulta->bindValue(":estado", $ordenParam->estado, PDO::PARAM_STR);
            $consulta->bindValue(":tiempo_fin", $ordenParam->tiempo_fin, PDO::PARAM_STR);
            $consulta->bindValue(":tiempo_estimado", $ordenParam->tiempo_estimado, PDO::PARAM_STR);
            $consulta->bindValue(":id", $ordenParam->id, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function obtenerOrdenPorId($ordenId){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ordenes WHERE id = :id");
        $consulta->bindValue(":id", $ordenId, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject("Orden");
    }

    public static function obtenerOrdenesPorTipoUsuario($tipo_usuario){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ordenes WHERE area_orden = :tipo_usuario");
        $consulta->bindParam(":tipo_usuario", $tipo_usuario);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Orden");
    }

    public static function obtenerOrdenesPorPedido($pedido_id){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM ordenes WHERE id_pedido = :id_pedido");
        try{
            $consulta->bindValue(":id_pedido", $pedido_id, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Orden");
    }

    public static function obtenerPrecioDeOrdenesPorPedido($pedido_id){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT SUM(precio) AS total FROM ordenes WHERE id_pedido = :id_pedido");
        try{
            $consulta->bindValue(":id_pedido", $pedido_id, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Orden");
    }

    public static function borrarOrden($ordenId){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM ordenes WHERE id = :id");
        try{
            $consulta->bindValue(":id", $ordenId, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }
}
?>