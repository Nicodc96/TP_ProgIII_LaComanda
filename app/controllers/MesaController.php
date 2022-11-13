<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable{
    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $mesa = Mesa::crearMesa(
            $parametros["codigo_mesa"],
            $parametros["id_empleado"],
            "Con cliente esperando pedido"
        );

        if (Mesa::insertarMesaDB($mesa) > 0){
            $payload = json_encode(array("mensaje" => "Mesa registrado con exito."));
        } else{
        $payload = json_encode(array("mensaje" => "Error al registrar la mesa."));
        }
  
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno($request, $response, $args){
        $mesa = Mesa::obtenerMesa($args['mesaId']);        
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function TraerTodos($request, $response, $args){
        $listaMesas = Mesa::obtenerTodos();
        $payload = json_encode(array("lista_de_mesas" => $listaMesas));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function BorrarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        Mesa::eliminarMesa($parametros["mesaId"]) ? 
        $mensaje = "Mesa borrada con exito" : 
        $mensaje = "No se ha podido eliminar la mesa";

        $payload = json_encode(array("mensaje" => $mensaje));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
	public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $mesa = Mesa::obtenerMesa($args["mesaId"]);
        $mensaje = "No se ha podido modificar la mesa";
        if ($mesa){
            $mesa->codigo_mesa = $parametros["codigo_mesa"];
            $mesa->id_empleado = $parametros["id_empleado"];
            $mesa->estado = $parametros["estado"];
            if (Mesa::modificarMesa($mesa)){
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