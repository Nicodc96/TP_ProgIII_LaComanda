<?php
require_once "./interfaces/IApiUsable.php";
require_once "./models/Orden.php";
require_once "./models/Area.php";
require_once "./models/Mesa.php";
require_once "./models/Pedido.php";
require_once "UsuarioController.php";

class OrdenController extends Orden implements IApiUsable{
	public function TraerUno($request, $response, $args) {
        $orden = Orden::obtenerOrdenPorId($args["id_orden"]);
        $payload = json_encode($orden);

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
	}
	
	public function TraerTodos($request, $response, $args) {
        $tipo_empleado = UsuarioController::obtenerInfoToken($request)->tipo_usuario;
        $id_tipoEmpleado = Area::obtenerAreasPorDescripcion($tipo_empleado);
        $ordenes = Orden::obtenerOrdenesPorTipoUsuario($id_tipoEmpleado);
        $ordenes_pendientes = array();

        foreach($ordenes as $orden){
            if ($orden->estado != "Listo para servir"){
                array_push($ordenes_pendientes, $orden);
            }
        }
        echo "Ordenes pendientes de los empleados " . $tipo_empleado . " <br>";
        echo Orden::mostrarOrdenesTabla($ordenes_pendientes);
        echo "<br>Ordenes totales: <br>";
        $payload = json_encode(array("ordenes_pendientes" => $ordenes));

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
	}

	public function CargarUno($request, $response, $args) {
        $params = $request->getParsedBody();
        $area = $params["area"];
        $pedido_id = $params["pedido_id"];
        $area = Area::obtenerAreasPorDescripcion($area);
        $pedido = Pedido::obtenerPedidoPorId($pedido_id);
        $orden = Orden::crearOrden(
            $area->id,
            $pedido->id,
            $pedido->estado_pedido,
            $params["descripcion"],
            $params["precio"],
            date("Y-m-d H:i:s"));
        echo "Orden creado: <br>";
        echo Orden::mostrarOrdenTabla($orden);
        
        $payload = json_encode(array("error" => "Hubo un problema al ingresar la orden."));
        if (Orden::insertarOrdenDB($orden) > 0){
             $pedido_costo = Orden::obtenerPrecioDeOrdenesPorPedido($pedido->id);
             $pedido->costo_pedido = $pedido_costo;
             if (Pedido::actualizarPedido($pedido) > 0){
                echo "<br> Se ha actualizado el precio del pedido: <br>";
                echo Pedido::mostrarPedidoTabla($pedido);

                $payload = json_encode(array("nueva_orden" => $orden, "pedido_relacionado" => $pedido->id));
             }
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
	}

	public function BorrarUno($request, $response, $args) {
        if (Orden::borrarOrden($args["id_orden"]) > 0){
            $payload = json_encode(array("mensaje" => "Se ha eliminado la orden correctamente."));
        } else {
            $payload = json_encode(array("error" => "No se ha podido eliminar la orden solicitada."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
	}
	
	public function ModificarUno($request, $response, $args) {
        $params = $request->getParsedBody();

        if (isset($args["id_orden"]) && isset($params["estado"])){
            $id_orden = $args["id_orden"];
            $estado_orden = $params["estado"];
            $orden = Orden::obtenerOrdenPorId($id_orden);
            $pedido = Pedido::obtenerPedidoPorId($orden->id_pedido);

            echo "Orden a ser modificada: <br>";
            echo Orden::mostrarOrdenTabla($orden);

            $orden->estado = $estado_orden;
            if (strcmp($estado_orden, "Listo para servir") == 0){
                $orden->tiempo_estimado = 0;
                echo "<br>La orden " . $orden->descripcion . ", ya esta lista para servir y su tiempo estimado se ha seteado a 0.<br>";
            }
        }

        if (isset($params["tiempo_estimado"])){
            $tiempo_estimado = $params["tiempo_estimado"];
            $orden->tiempo_estimado = $tiempo_estimado;
            $orden->calcularTiempoTerminado();
        }

        if (isset($orden)){
            echo "<br>Orden modificada: <br>";
            echo Orden::mostrarOrdenTabla($orden);
        }

        if (isset($orden) && Orden::actualizarOrden($orden) > 0){
            echo "<br>La orden de ID " . $orden->id . " ha cambiado su estado a: " . $orden->estado;
        }

        if (isset($orden) && $orden->estado != "Listo para servir" && $estado_orden != "Listo para servir"){
            $orden->estado = $estado_orden;
            Orden::actualizarOrden($orden);
        }

        if (isset($orden) && $orden->estado != "Listo para servir" && $estado_orden == "Listo para servir"){
            $ordenes = Orden::obtenerOrdenesPorPedido($orden->id_pedido);
            $ordenes_terminadas = Orden::filtrarOrdenesTerminadas($ordenes, "Listo para servir");

            echo "Ordenes terminadas: <br>";
            echo Orden::mostrarOrdenesTabla($ordenes_terminadas);

            if (count($ordenes) == count($ordenes_terminadas)){
                $orden->estado = $estado_orden;
                Orden::actualizarOrden($orden);

                echo "<br>La orden " . $orden->descripcion . ", ya esta lista para servir y su tiempo estimado se ha seteado a 0.<br>";
                echo "Todas las ordenes ahora tienen tiempo estimado a terminar en 0. <br>";

                $mesa_del_pedido = Mesa::obtenerMesaPorIdPedido($pedido->id);
                Mesa::actualizarEstadoMesa($mesa_del_pedido, "Con clientes comiendo");
            }
        }

        if (isset($orden) && Orden::actualizarOrden($orden) > 0){
            $payload = json_encode(array("mensaje" => "Se ha actualizado el pedido correctamente."));
        } else{
            $payload = json_encode(array("error" => "No se ha podido actualizar el pedido."));
        }
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
	}
}
?>