<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable{
    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $usuario = Usuario::crearUsuario(
          $parametros["nombre_usuario"],
          $parametros["clave"],
          $parametros["esAdmin"],
          $parametros["tipo_usuario"],
          $parametros["estado"],
          date("Y-m-d H:i:s"));
          
        if (Usuario::insertarUsuarioDB($usuario) > 0){
          $payload = json_encode(array("mensaje" => "Usuario registrado con exito."));
        } else{
          $payload = json_encode(array("mensaje" => "Error al registrar el usuario."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args){
        $usuario = Usuario::obtenerUsuario($args['usuarioId']);        
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args){
        $listaUsuarios = Usuario::obtenerTodos();
        $payload = json_encode(array("lista_de_usuarios" => $listaUsuarios));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args){
      $parametros = $request->getParsedBody();

      $usuario = Usuario::obtenerUsuario($args["usuarioId"]);
      $mensaje = "No se ha podido modificar al usuario";
      if ($usuario){
        $usuario->usuario = $parametros["usuario"];
        $usuario->clave = $parametros["clave"];
        if (Usuario::modificarUsuario($usuario)){
            $mensaje = "Usuario modificado con exito";
        }
      }
      $payload = json_encode(array("mensaje" => $mensaje));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

    public function BorrarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        Usuario::borrarUsuario($parametros['usuarioId']) ? 
        $mensaje = "Usuario borrado con exito" : 
        $mensaje = "No se ha podido eliminar el usuario.";

        $payload = json_encode(array("mensaje" => $mensaje));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function Login($request, $response, $args){
      
      $params = $request->getParsedBody();

      if (isset($params["username"]) && isset($params["clave"])) {
          $username = $params["username"];
          $clave = $params["clave"];
          $usuario = Usuario::obtenerUsuarioSegunUsername($username);

          if ($usuario != null && ($usuario->nombre_usuario == $username && $usuario->clave == $clave)) {
              $payload = json_encode(array("mensaje" => "Login exitoso!"));
          } else {
              $payload = json_encode(array("mensaje" => "No se ha podido logear."));
          }
      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public static function obtenerInfoToken($request){
    $header = $request->getHeader("Authorization");
    $token = trim(str_replace("Bearer", "", $header[0]));
    $usuario = JWTAuth::getInfoToken($token);
    
    return $usuario;
  }
}
