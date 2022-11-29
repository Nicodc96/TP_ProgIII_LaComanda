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
        $params = $request->getParsedBody();

        $mesa_id = $params["mesa_id"];

        $pedido = Pedido::crearPedido(
            $mesa_id,
            $params["estado_pedido"],
            $params["nombre_cliente"],
            0,
            ""
        );

        $payload = json_encode($pedido);
        $pedido_id = Pedido::insertarPedidoDB($pedido);

        if ($pedido_id > 0){
            $file_manager = new UploadManager($carpetaImg, $pedido_id, $_FILES);
            $pedido = Pedido::obtenerPedidoPorId($pedido_id);
            $pedido->foto_pedido = UploadManager::getOrderImageNameExt($file_manager, $pedido_id);
            Pedido::actualizarFoto($pedido);
            $payload = json_encode(array("mensaje" => "Pedido registrado con exito. Se ha actualizado la foto del pedido."));
        } else{
          $payload = json_encode(array("mensaje" => "Error al registrar el pedido."));
        }
  
        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }
    public function TraerUno($request, $response, $args){
        $pedido = Pedido::obtenerPedidoPorId($args["id_pedido"]);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function TraerTodos($request, $response, $args){
        $listaPedidos = Pedido::obtenerTodos();
        $payload = json_encode(array("lista_de_pedidos" => $listaPedidos));

        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function TraerSegunArea($request, $response, $args){
        $tipo_usuario = UsuarioController::obtenerInfoToken($request)->tipo_usuario;
        
        $ordenes = Orden::obtenerOrdenesPorTipoUsuario($tipo_usuario);
        $payload = json_encode(array("lista_de_ordenes" => $ordenes));

        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function TraerPedidosTiempo($request, $response, $args){
        $pedidos = Pedido::obtenerPedidosConTiempo();

        $payload = json_encode(array("lista_pedidos" => $pedidos));

        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function BorrarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        Pedido::eliminarPedido($parametros["id_pedido"]) ? 
        $mensaje = "Pedido borrado con exito" : 
        $mensaje = "No se ha podido eliminar el pedido";
        $payload = json_encode(array("mensaje" => $mensaje));

       $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

	public function ModificarUno($request, $response, $args){
        $params = $request->getParsedBody();

        $pedido = Pedido::obtenerPedidoPorId($args["id_pedido"]);
        $mensaje = "No se ha podido modificar el pedido";
        if ($pedido){
            $pedido->estado_pedido = $params["estado_pedido"];
            if (Pedido::actualizarPedido($pedido)){
              $mensaje = "Pedido modificado con exito";
            }
        }
        $payload = json_encode(array("mensaje" => $mensaje));
  
        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }
}
?>