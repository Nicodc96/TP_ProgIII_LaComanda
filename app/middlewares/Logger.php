<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
class Logger{
    public static function RegistroOperacion($request, $response, $next){
        $retorno = $next($request, $response);
        return $retorno;
    }

    public static function validateGP($request, $handler){        
        $requestType = $request->getMethod();
        $response = $handler->handle($request);

        if($requestType == "GET"){
            $response->getBody()->write("<h2>Error: Uso de petición GET no disponible para esta sección.</h2>");
        }else if($requestType == "POST"){
            $response->getBody()->write("<h2>Petición POST:</h2>");
            $data = $request->getParsedBody();
            $nombre = $data["nombre"];
            $tipo = $data["tipo"];

            $tipo == "Admin" ? 
            $response->getBody()->write("<h2> Bienvenido ". $nombre .".</h2>") :
            $response->getBody()->write("<h2> No tienes permiso para acceder a esta sección.</h2>");
        }
        return $response;
    }
    // Habilitar MW en 2do Sprint
}