<?php
require_once "./models/Pedido.php";
require_once "./models/UploadManager.php";
require_once "./interfaces/IApiUsable.php";
require_once "./models/Orden.php";
require_once "./models/Mesa.php";
require_once "./controllers/UsuarioController.php";

class PedidoController extends Pedido implements IApiUsable{
    public function CargarUno($request, $response, $args){
        $carpetaImg = "./PedidoImagenes/";
        $parametros = $request->getParsedBody();

        $pedido = Pedido::crearPedido(
            $parametros["id_mesa"], // Cambiar en 2do Sprint (chequear que exista la mesa)
            "Pendiente",
            $parametros["nombre_cliente"],
            $parametros["costo_pedido"],
            ""
        );
        $pedido_id = Pedido::insertarPedidoDB($pedido);
        if ($pedido_id > 0){
            $payload = json_encode(array("mensaje" => "Pedido registrado con exito."));
            $file_manager = new UploadManager($carpetaImg, $pedido_id, $_FILES);
            $pedido->foto_pedido = UploadManager::getOrderImageNameExt($file_manager, $pedido_id);
            $pedido->id = $pedido_id;
            Pedido::actualizarFoto($pedido);
            echo Pedido::mostrarPedidoTabla($pedido);
        } else{
        $payload = json_encode(array("mensaje" => "Error al registrar el pedido."));
        }
  
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno($request, $response, $args){
        $pedido = Pedido::obtenerPedidoId($args['pedidoId']);        
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args){
        $listaPedidos = Pedido::obtenerTodos();
        $payload = json_encode(array("lista_de_pedidos" => $listaPedidos));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerSegunArea($request, $response, $args){
        $tipo_usuario = UsuarioController::obtenerInfoToken($request)->tipo_usuario;
        
        $ordenes = Orden::obtenerOrdenesPorTipoUsuario($tipo_usuario);
        echo Orden::mostrarOrdenesTabla($ordenes);
        $payload = json_encode(array("lista_de_ordenes_del_pedido" => $ordenes));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPedidosTiempo($request, $response, $args){
        $pedidos = Pedido::obtenerPedidosConTiempo();
        echo Pedido::mostrarPedidosTabla($pedidos);

        $payload = json_encode(array("lista_pedidos" => $pedidos));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        Pedido::eliminarPedido($parametros["pedidoId"]) ? 
        $mensaje = "Pedido borrado con exito" : 
        $mensaje = "No se ha podido eliminar el pedido";

        $payload = json_encode(array("mensaje" => $mensaje));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

	public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $pedido = Pedido::obtenerPedidoId($args["pedidoId"]);
        $mensaje = "No se ha podido modificar el pedido";
        if ($pedido){
            $pedido->estado_pedido = $parametros["estado_pedido"];
            $pedido->costo_pedido = Orden::obtenerPrecioDeOrdenesPorPedido($pedido->id);
            if (Pedido::modificarPedido($pedido)){
              $mensaje = "Pedido modificado con exito";
              echo Pedido::mostrarPedidoTabla($pedido);
            }
        }
        $payload = json_encode(array("mensaje" => $mensaje));
  
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
?>