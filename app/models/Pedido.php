<?php
class Pedido{
    public $id;
    public $mesa_id;
    public $estado_pedido;
    public $nombre_cliente;
    public $costo_pedido;
    public $foto_pedido;

    public function __construct(){}

    public static function crearPedido($mesaId, $estadoPedido, $nombreCliente, $costoPedido, $fotoPedido){
        $pedido = new Pedido();
        $pedido->mesa_id = $mesaId;
        $pedido->estado_pedido = $estadoPedido;
        $pedido->nombre_cliente = $nombreCliente;
        $pedido->costo_pedido = $costoPedido;
        $pedido->foto_pedido = $fotoPedido;
        return $pedido;
    }

    public static function insertarPedidoDB($pedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (mesa_id, estado_pedido, nombre_cliente, costo_pedido, foto_pedido)
        VALUES (:mesa_id, :estado_pedido, :nombre_cliente, :costo_pedido, :foto_pedido)");
        try{
            $consulta->bindValue(':mesa_id', $pedido->mesa_id, PDO::PARAM_STR);
            $consulta->bindValue(':estado_pedido', $pedido->estado_pedido, PDO::PARAM_STR);
            $consulta->bindValue(':nombre_cliente', $pedido->nombre_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':costo_pedido', $pedido->costo_pedido, PDO::PARAM_STR);
            $consulta->bindValue(':foto_pedido', $pedido->foto_pedido, PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function mostrarPedidosTabla(){
        $arrayPedidos = self::obtenerTodos();
        $mensaje = "Lista vacia.";
        if (is_array($arrayPedidos) && count($arrayPedidos) > 0){
            $mensaje = "<h3 align='center'> Lista de Pedidos </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Mesa ID</th><th>Estado Pedido</th><th>Nombre cliente</th><th>Costo</th><th>Foto Pedido</th></tr><tbody>";
            foreach($arrayPedidos as $pedido){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $pedido->id . "</td>" .
                "<td>" . $pedido->mesa_id . "</td>" .
                "<td>" . $pedido->estado_pedido . "</td>" .
                "<td>" . $pedido->nombre_cliente . "</td>" .
                "<td>" . $pedido->costo_pedido . "</td>" .
                "<td>" . $pedido->foto_pedido . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function mostrarPedidoTabla($pedido){
        $mensaje = "Lista vacia.";
        if (is_a($pedido, "Pedido")){
            $mensaje = "<h3 align='center'> Lista de Pedidos </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Mesa ID</th><th>Estado Pedido</th><th>Nombre cliente</th><th>Costo</th><th>Foto Pedido</th></tr><tbody>";
            $mensaje .= "<tr align='center'>" .
            "<td>" . $pedido->id . "</td>" .
            "<td>" . $pedido->mesa_id . "</td>" .
            "<td>" . $pedido->estado_pedido . "</td>" .
            "<td>" . $pedido->nombre_cliente . "</td>" .
            "<td>" . $pedido->costo_pedido . "</td>" .
            "<td>" . $pedido->foto_pedido . "</td></tr>";
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function obtenerPedidoId($pedidoId){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $pedidoId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPedidosMesaId($mesaId){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE mesa_id = :idMesa");
        $consulta->bindValue(':idMesa', $mesaId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedido($pedidoParam){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado_pedido = :estado, costo_pedido = :costo WHERE id = :id");
        try{
            $consulta->bindValue(':mesaId', $pedidoParam->mesa_id, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $pedidoParam->estado_pedido, PDO::PARAM_STR);
            $consulta->bindValue(':costo', $pedidoParam->costo_pedido, PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function eliminarPedido($pedidoId){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado_pedido = :estado WHERE id = :id");
        try{
            $consulta->bindValue(':id', $pedidoId, PDO::PARAM_INT);
            $consulta->bindValue(':estado', "cancelado", PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function actualizarFoto($pedido){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET foto_pedido = :foto_pedido WHERE id = :id");
        try{
            $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);
            $consulta->bindValue(':foto_pedido', $pedido->foto_pedido, PDO::PARAM_STR);
            $consulta->execute();
        } catch(\Throwable $err){
            echo $err->getMessage();
        }

        return $consulta->rowCount() > 0;
    }
    // Agregar modelo ordenes en 2do Sprint
}
?>