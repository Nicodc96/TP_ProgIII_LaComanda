<?php
require_once "./models/Mesa.php";
require_once "./interfaces/IApiUsable.php";
require_once "./models/Empleado.php";
require_once "./models/Pedido.php";

class MesaController extends Mesa implements IApiUsable{
    public function CargarUno($request, $response, $args){
        $params = $request->getParsedBody();

        $mesa = Mesa::crearMesa($params["codigo_mesa"], $params["id_empleado"], $params["estado"]);
        $payload = json_encode(array("mensaje" => "Error al registrar la mesa."));

        if (Mesa::insertarMesaDB($mesa) > 0){
            echo Mesa::mostrarMesaTabla($mesa);
            $payload = json_encode(array("mensaje" => "Mesa registrado con exito."));
        }
  
        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function TraerUno($request, $response, $args){
        $mesa = Mesa::obtenerMesaPorId($args["id_mesa"]);  
        if ($mesa){
            echo Mesa::mostrarMesaTabla($mesa);
        }
        $payload = json_encode($mesa);
        
        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function TraerTodos($request, $response, $args){
        $listaMesas = Mesa::obtenerTodos();
        $payload = json_encode(array("lista_de_mesas" => $listaMesas));

        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function BorrarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        isset($parametros["id_mesa"]) && Mesa::eliminarMesa($parametros["id_mesa"]) > 0 ? 
        $mensaje = "Mesa borrada con exito" : 
        $mensaje = "No se ha podido eliminar la mesa";
        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

	public function ModificarUno($request, $response, $args){
        $params = $request->getParsedBody();

        if (isset($args["id"]) && isset($params["estado"]) && isset($params["id_empleado"])){
            $mesa_id = $args["id"];
            $id_empleado = $params["id_empleado"];
            $estado = $params["estado"];
            $empleado = Empleado::obtenerEmpleadoPorId($id_empleado);

            if (isset($empleado) && strcmp($estado, "Cerrada") != 0){
                $mesa = Mesa::obtenerMesaPorId($mesa_id);
                $mesa->estado = $estado;
                $mesa->id_empleado = $id_empleado;
                echo "Mesa a modificar: <br>";
                echo Mesa::mostrarMesaTabla($mesa);
            } else{
                echo "Error: uno o mas parametros incorrectos.<br>";
            }
        }

        if (isset($mesa) && Mesa::modificarMesa($mesa) > 0){
            $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
        } else{
            $payload = json_encode(array("error" => "No se ha podido modificar la mesa"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function CobrarUno($request, $response, $args){
        $params = $request->getParsedBody();
        $payload = json_encode(array("mensaje" => "Se ha producido un error al realizar la accion."));

        if(isset($args["id_mesa"]) && isset($params["estado"])){
            $id_mesa = $args["id_mesa"];
            $estado_mesa = $params["estado"];
            $mesa = Mesa::obtenerMesaPorId($id_mesa);

            echo "Mesa a cobrar: <br>";
            echo Mesa::mostrarMesaTabla($mesa);

            if (isset($mesa)){
                $mesa->estado = $estado_mesa;
                echo "Mesa actualizada: <br>";
                echo Mesa::mostrarMesaTabla($mesa);
                if (Mesa::actualizarEstadoMesa($mesa, $estado_mesa)){
                    $payload = json_encode(array("mensaje" => "Se ha actualizado la mesa correctamente."));
                }
            }
        }
        
        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function ModificarUnoAdmin($request, $response, $args){
        $params = $request->getParsedBody();        
        $payload = json_encode(array("error" => "No se ha podido modificar la mesa solicitada."));
        
        if (isset($params["id_mesa"]) && isset($params["estado"])) {
            $id_mesa = $params["id_mesa"];
            $estado_mesa = $params["estado"];
            $mesa = Mesa::obtenerMesaPorId($id_mesa);
            
            echo "Mesa seleccionada: <br>";
            echo Mesa::mostrarMesaTabla($mesa);

            if(strcmp($mesa->estado, "Cerrada") == 0 && strcmp($estado_mesa, "Cerrada") == 0){
                echo "<h2>La mesa ya se encuentra cerrada!</h2><br>";
            } else{
                $mesa->estado = $estado_mesa;
                echo "El estado de la mesa se ha actualizado: <br>";
                echo Mesa::mostrarMesaTabla($mesa);
                if (Mesa::actualizarEstadoMesa($mesa, $estado_mesa) > 0){
                    $payload = json_encode(array("mensaje" => "El estado de la mesa se actualizo correctamente"));
                }
            }
        } else{
            echo "<h2>Error: uno o mas parametros incorrectos.</h2><br>";
        }
        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function TraerDemoraPedidoMesa($request, $response, $args){
        $codigo_mesa = $args["codigo_mesa"];
        $id_pedido = $args["pedido_id"];
        $tiempo_restante = Pedido::obtenerTiempoMaximoPedidoPorCodigoMesa($id_pedido, $codigo_mesa)[0]["tiempo_estimado"];
        if ($tiempo_restante == 0){
            echo "<h2>Mesa de codigo: " . $codigo_mesa . "<br>Tiempo restante: ". $tiempo_restante . " minutos.<br>
            Su pedido esta listo, pronto llegara a su mesa. ¡Gracias por elegirnos!</h2><br>";
        } else{
            echo "<h2>Mesa de codigo: " . $codigo_mesa . "<br>Tiempo restante: ". $tiempo_restante . " minutos.</h2><br>";
        }

        $payload = json_encode(array("mensaje" => "Tiempo restante: ". $tiempo_restante . " minutos."));
        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }
}
?>