<?php
require_once "./interfaces/IApiUsable.php";
require_once "./models/Empleado.php";
require_once "./models/Area.php";
require_once "./models/Usuario.php";
require_once "UsuarioController.php";

class EmpleadoController extends Empleado implements IApiUsable{
	public function TraerUno($request, $response, $args) {
        $id_empleado = $args["id_empleado"];
        $empleado = Empleado::obtenerEmpleadoPorId($id_empleado);
        $payload = json_encode($empleado);
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
	}

	public function TraerTodos($request, $response, $args) {
        $array_empleados = Empleado::obtenerTodos();
        $payload = json_encode(array("lista_empleados" => $array_empleados));

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
	}
	
	public function CargarUno($request, $response, $args) {
        $params = $request->getParsedBody();
        
        $nombre_empleado = $params["nombre"];
        $area_empleado = $params["area"];
        $usuario_empleado = $params["nombre_usuario"];
        $area_empleado  = Area::obtenerAreasPorDescripcion($area_empleado);
        $idUsuario_empleado = Usuario::obtenerUsuarioPorUsername($usuario_empleado)->id;
        $nuevo_empleado = Empleado::crearEmpleado($idUsuario_empleado, $area_empleado->id, $nombre_empleado, date("Y-m-d H:i:s"));

        if (Empleado::insertarEmpleadoDB($nuevo_empleado) > 0) {
            $payload = json_encode(array("mensaje" => "Empleado creado exitosamente!"));
        }else{
            $payload = json_encode(array("error" => "No se ha podido crear el empleado solicitado."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
	}

	public function BorrarUno($request, $response, $args) {
        $id_empleado = $args["id_empleado"];
        $empleado = Empleado::obtenerEmpleadoPorId($id_empleado);
        if (Empleado::borrarEmpleado($empleado->id) > 0) {
            $payload = json_encode(array("mensaje" => "Empleado borrado con exito"));
        }else{
            $payload = json_encode(array("mensaje" => "No se ha podido borrar el empleado solicitado!"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
	}

	public function ModificarUno($request, $response, $args) {
        $params = $request->getParsedBody();
        $id_empleado = $args["id_empleado"];
        $empleado = Empleado::obtenerEmpleadoPorId($id_empleado);
        $nombre_empleado = $params["nombre"];
        $idArea_empleado = Area::obtenerAreasPorDescripcion($params["Area"])->id;
        $idUsuario_empleado = Usuario::obtenerUsuarioPorId($params["usuario"])->id;

        $empleado->nombre = $nombre_empleado;
        $empleado->id_area_empleado = $idArea_empleado;
        $empleado->usuario_id = $idUsuario_empleado;

        if (Empleado::modificarEmpleado($empleado) > 0) {
            $payload = json_encode(array("mensaje" => "Empleado modificado exitosamente."));
        }else{
            $payload = json_encode(array("error" => "No se ha podido modificar al empleado solicitado!"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
	}
}
?>