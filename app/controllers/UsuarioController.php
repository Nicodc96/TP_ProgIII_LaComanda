<?php
require_once "./models/Usuario.php";
require_once "./interfaces/IApiUsable.php";
require_once "./middlewares/JWT.php";

class UsuarioController extends Usuario implements IApiUsable{
    public function CargarUno($request, $response, $args){
        $params = $request->getParsedBody();
        $payload = json_encode(array("mensaje" => "Error al registrar el usuario."));

        $usuario = Usuario::crearUsuario(
          $params["nombre_usuario"],
          $params["clave"],
          $params["esAdmin"],
          $params["tipo_usuario"],
          $params["estado"],
          date("Y-m-d H:i:s"));
        
        echo "Usuario a cargar: <br>";
        echo Usuario::mostrarUsuarioTabla($usuario);

        if (Usuario::insertarUsuarioDB($usuario) > 0){
          $payload = json_encode(array("mensaje" => "Usuario registrado con exito."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
    }

    public function TraerUno($request, $response, $args){
        $usuario = Usuario::obtenerUsuarioPorId($args["id_usuario"]);     
        echo Usuario::mostrarUsuarioTabla($usuario);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function TraerTodos($request, $response, $args){
        $listaUsuarios = Usuario::obtenerTodos();
        echo Usuario::mostrarUsuariosTabla($listaUsuarios);
        $payload = json_encode(array("lista_de_usuarios" => $listaUsuarios));

        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }
    
    public function ModificarUno($request, $response, $args){
      $params = $request->getParsedBody();
      $payload = json_encode(array("error" => "No se ha podido modificar el usuario."));

      if (isset($params["nombre_usuario"])){
        $nombre_usuario = $params["nombre_usuario"];
        $usuario = Usuario::obtenerUsuarioPorUsername($nombre_usuario);
        $usuario->nombre_usuario = $params["nombre_usuario"];
        $usuario->clave = $params["clave"];

        if (Usuario::modificarUsuario($usuario)){
          $payload = json_encode(array("mensaje" => "Usuario modificado exitosamente."));
        }
      }

      $response->getBody()->write($payload);
      return $response
      ->withHeader("Content-Type", "application/json");
  }

    public function BorrarUno($request, $response, $args){
        Usuario::borrarUsuario($args["id_usuario"]) ? 
        $mensaje = "Usuario borrado con exito" : 
        $mensaje = "No se ha podido eliminar el usuario.";

        $payload = json_encode(array("mensaje" => $mensaje));
        $response->getBody()->write($payload);
        return $response
        ->withHeader("Content-Type", "application/json");
    }

    public function Login($request, $response, $args){      
      $params = $request->getParsedBody();

      if (isset($params["nombre_usuario"]) && isset($params["clave"])) {
          $nombre_usuario = $params["nombre_usuario"];
          $clave = $params["clave"];
          $usuario = Usuario::obtenerUsuarioPorUsername($nombre_usuario);

          if (!is_null($usuario) && ($usuario->nombre_usuario == $nombre_usuario && password_verify($clave, $usuario->clave))) {
              $payload = json_encode(array("mensaje" => "Login exitoso!"));
          } else {
              $payload = json_encode(array("mensaje" => "No se ha podido logear."));
          }
      }
      $response->getBody()->write($payload);
      return $response
      ->withHeader("Content-Type", "application/json");
  }

  public static function obtenerInfoToken($request){
    $header = $request->getHeader("Authorization");
    $token = trim(str_replace("Bearer", "", $header[0]));
    $usuario = JWTAuth::getInfoToken($token);
    
    return $usuario;
  }
}
