<?php
use Slim\Psr7\Response;

require_once "JWT.php";
class MWAcceso{
    public function validarToken($request, $handler){
        $header = $request->getHeaderLine("Authorization");
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            JWTAuth::verificarToken($token);
            $response = $handler->handle($request);
        } else {
            $response->getBody()->write(json_encode(array("Token error" => "Es necesario un token!")));
            $response = $response->withStatus(401);
        }
        return $response->withHeader("Content-Type", "application/json");
    }

    public function esAdmin($request, $handler){
        $header = $request->getHeaderLine("Authorization");
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            $data = JWTAuth::getInfoToken($token);            
            if ($data->tipo_usuario == "Admin") {
                $response = $handler->handle($request);
            } else {
                $response->getBody()->write(json_encode(array("Error" => "Acceso solo a personal autorizado.")));
                $response = $response->withStatus(401);
            }
        } else {
            $response->getBody()->write(json_encode(array("Admin error" => "El token de administrador es necesario.")));
            $response = $response->withStatus(401);
        }

        return $response->withHeader("Content-Type", "application/json");
    }

    public function esEmpleado($request, $handler){
        $header = $request->getHeaderLine("Authorization");
        $response = new Response();
        try {
            if (!empty($header)) {
                $token = trim(explode("Bearer", $header)[1]);
                $data = JWTAuth::getInfoToken($token);
                if (in_array($data->tipo_usuario, ["Mozo", "Cocinero", "Barman"])) {
                    $response = $handler->handle($request);
                } else {
                    $response->getBody()->write(json_encode(array("Error" => "Acceso solo al personal autorizado.")));
                    $response = $response->withStatus(401);
                }
            } else {
                $response->getBody()->write(json_encode(array("Error" => "Es necesario un token de usuario.")));
                $response = $response->withStatus(401);
            }
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
        return $response->withHeader("Content-Type", "application/json");
    }

    public function esCocinero($request, $handler){
        $header = $request->getHeaderLine("Authorization");
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            $data = JWTAuth::getInfoToken($token);
            if ($data->tipo_usuario == "Cocinero" || $data->tipo_usuario == "Admin") {
                $response = $handler->handle($request);
            } else {
                $response->getBody()->write(json_encode(array("Error" => "Solo los cocineros y administradores tienen acceso a esta seccion.")));
                $response = $response->withStatus(401);
            }
        } else {
            $response->getBody()->write(json_encode(array("Admin error" => "El token como cocinero o administrador es necesario!")));
            $response = $response->withStatus(401);
        }
        return $response->withHeader("Content-Type", "application/json");
    }

    public function esMozo($request, $handler){
        $header = $request->getHeaderLine("Authorization");
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            $data = JWTAuth::getInfoToken($token);
            if ($data->tipo_usuario == "Mozo" || $data->tipo_usuario == "Admin") {
                $response = $handler->handle($request);
            } else {
                $response->getBody()->write(json_encode(array("Error" => "Solo los mozos y administradores tienen acceso a esta seccion.")));
                $response = $response->withStatus(401);
            }
        } else {
            $response->getBody()->write(json_encode(array("Admin error" => "El token como mozo o administrador es necesario!")));
            $response = $response->withStatus(401);
        }
        return $response->withHeader("Content-Type", "application/json");
    }
}
?>