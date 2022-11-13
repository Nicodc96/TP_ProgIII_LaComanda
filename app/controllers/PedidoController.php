<?php
require_once "./models/Pedido.php";
require_once "./models/UploadManager.php";
require_once "./interfaces/IApiUsable.php";

class PedidoController extends Pedido implements IApiUsable{
    public function CargarUno($request, $response, $args){
        $carpetaImg = "./PedidoImagenes/";
        $parametros = $request->getParsedBody();

        $pedido = Pedido::crearPedido(
            $parametros["id_mesa"], // Cambiar en 2do Sprint (chequear que exista la mesa)
            "Pendiente",
            $parametros["nombre_cliente"],
            $parametros["costo_pedido"],
            "" // Cambiar en 2do Sprint (calcular segun precios de productos)
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
    // Agregar 'TraerPorMesa' y 'TraerPorEmpleadoId' en 2do Sprint
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
            $pedido->costo_pedido = $parametros["costo_pedido"]; // Modificar en 2do Sprint
            if (Pedido::modificarPedido($pedido)){
              $mensaje = "Mesa modificado con exito";
            }
        }
        $payload = json_encode(array("mensaje" => $mensaje));
  
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

?>