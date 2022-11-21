<?php

require "./db/AccesoDatos.php";
require "./models/Area.php";

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
        $orden->precio = number_format($precio, 2);
        $orden->tiempo_inicio = $tiempo_inicio;
        return $orden;
    }

    public function calcularTiempoTerminado(){
        $nuevo_tiempo = new DateTime($this->tiempo_inicio);
        $nuevo_tiempo = $nuevo_tiempo->modify("+". $this->tiempo_estimado . " minutos");
        $this->tiempo_fin = $nuevo_tiempo->format('Y-m-d H:i:s');
    }

    public static function insertarOrdenDB($orden){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ordenes (area_orden, id_pedido, estado, descripcion, precio, tiempo_inicio)
        VALUES (:area_orden, :id_pedido, :estado, :descripcion, :precio, :tiempo_inicio)");
        try{
            $consulta->bindValue(":area_orden", $orden->area_orden, PDO::PARAM_STR);
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


    public static function mostrarOrdenesTabla($arrayOrdenes = array()){
        if (count($arrayOrdenes) == 0){
            $arrayOrdenes = self::obtenerTodos();
        }
        $mensaje = "Lista vacia.";
        if (is_array($arrayOrdenes) && count($arrayOrdenes) > 0){
            $mensaje = "<h3 align='center'> Lista de Ordenes </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Area Asociada</th><th>ID Pedido</th><th>Estado</th><th>Descripcion</th><th>Tiempo inicio</th><th>Tiempo fin</th><th>Tiempo estimado</th></tr><tbody>";
            foreach($arrayOrdenes as $orden){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $orden->id . "</td>" .
                "<td>" . $orden->area_orden . "</td>" .
                "<td>" . $orden->id_pedido. "</td>" .
                "<td>" . $orden->estado . "</td>" .
                "<td>" . $orden->descripcion . "</td>" .
                "<td>" . $orden->tiempo_inicio . "</td>" .
                "<td>" . $orden->tiempo_fin . "</td>" .
                "<td>" . $orden->tiempo_estimado . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function mostrarOrdenTabla($orden){
        $mensaje = "El objeto envíado por parámetro no es una Orden.";
        if (is_a($orden, "Orden")){
            $mensaje = "<h3 align='center'> Lista de Ordenes </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Area Asociada</th><th>ID Pedido</th><th>Estado</th><th>Descripcion</th><th>Tiempo inicio</th><th>Tiempo fin</th><th>Tiempo estimado</th></tr><tbody>";
            $mensaje .= "<tr align='center'>" .
            "<td>" . $orden->id . "</td>" .
            "<td>" . $orden->area_orden . "</td>" .
            "<td>" . $orden->id_pedido. "</td>" .
            "<td>" . $orden->estado . "</td>" .
            "<td>" . $orden->descripcion . "</td>" .
            "<td>" . $orden->tiempo_inicio . "</td>" .
            "<td>" . $orden->tiempo_fin . "</td>" .
            "<td>" . $orden->tiempo_estimado . "</td></tr>";
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function filtrarOrdenesTerminadas($lista_ordenes, $estado){
        $lista_filtrada = array();
        foreach($lista_ordenes as $orden){
            if(strcmp($orden->estado, $estado) == 0){
                array_push($lista_filtrada, $orden);
            }
        }
        return $lista_filtrada;
    }

    public static function actualizarOrden($ordenParam){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ordenes SET estado = :estado, tiempo_fin = :tiempo_fin, tiempo_estimado = :tiempo_estimado
        WHERE id = :id");
        try{
            $consulta->bindValue(":estado", $ordenParam->estado, PDO::PARAM_STR);
            $consulta->bindValue(":tiempo_fin", $ordenParam->estado, PDO::PARAM_STR);
            $consulta->bindValue(":tiempo_estimado", $ordenParam->estado, PDO::PARAM_STR);
            $consulta->bindValue(":id", $ordenParam->id, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function obtenerOrdenId($ordenId){
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

    public static function obtenerPrecioDeOrdenesPorPedido($pedido_id){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT SUM(precio) FROM ordenes WHERE id_pedido = :id_pedido");
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