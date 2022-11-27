<?php
require_once "./models/Usuario.php";
require_once "./middlewares/JWT.php";

class LoginController extends Usuario{
    public function VerificarUsuario($request, $response, $args){
        $params = $request->getParsedBody();
        $nombre_usuario = $params["nombre_usuario"];
        $clave = $params["clave"];
        
        $usuario = Usuario::obtenerUsuarioPorUsername($nombre_usuario);
        echo Usuario::mostrarUsuarioTabla($usuario);
        $payload = json_encode(array("error" => "Usuario invalido"));
        
        if(!is_null($usuario)){
            if(password_verify($clave, $usuario->clave)){
                $info_usuario = array(
                    "id" => $usuario->id,
                    "nombre_usuario" => $usuario->nombre_usuario,
                    "clave" => $usuario->clave,
                    "esAdmin" => $usuario->esAdmin,
                    "tipo_usuario" => $usuario->tipo_usuario);
                
                    $payload = json_encode(array(
                    "Token" => JWTAuth::crearToken($info_usuario),
                    "response" => "Usuario verificado", 
                    "es_administrador" => $usuario->esAdmin,
                    "tipo_usuario" => $usuario->tipo_usuario));
            }
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
}
?>