<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
class Logger{
    public static function validateGP($request, $handler){        
        $requestType = $request->getMethod();
        $response = $handler->handle($request);

        if($requestType == "GET"){
            $response->getBody()->write("<h2>Error: Uso de peticion GET no disponible para esta seccion.</h2>");
        } else if($requestType == "POST"){
            $response->getBody()->write("<h2>Peticion POST:</h2>");
            $data = $request->getParsedBody();
            $nombre = $data["nombre_usuario"];
            $tipo = $data["tipo_usuario"];

            $tipo == "Admin" ? 
            $response->getBody()->write("<h2> Bienvenido ". $nombre .".</h2>") :
            $response->getBody()->write("<h2> No tienes permiso para acceder a esta seccion.</h2>");
        }
        return $response;
    }

    public static function RegistroOperacion($request, $response, $next){
        $retorno = $next($request, $response);
        return $retorno;
    }
}