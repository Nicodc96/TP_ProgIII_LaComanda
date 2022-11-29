<?php
require_once "./models/Encuesta.php";
class EncuestaController extends Encuesta{
    public function CrearEncuestaController($request, $response, $args){
        $params = $request->getParsedBody();
        $payload = json_encode(array("mensaje" => "No se ha podido crear la encuesta."));

        if (isset($params["mesa_puntuacion"]) && isset($params["restaurante_puntuacion"])
        && isset($params["mozo_puntuacion"]) && isset($params["cocinero_puntuacion"])
        && isset($params["id_pedido"]) && isset($params["comentario"])) {
            $id_pedido = $params["id_pedido"];
            $mesa_puntuacion = $params["mesa_puntuacion"];
            $restaurante_puntuacion = $params["restaurante_puntuacion"];
            $mozo_puntuacion = $params["mozo_puntuacion"];
            $cocinero_puntuacion = $params["cocinero_puntuacion"];
            $comentario = $params["comentario"];

            $encuesta = Encuesta::crearEncuesta($id_pedido, $mesa_puntuacion, $restaurante_puntuacion, $mozo_puntuacion, $cocinero_puntuacion, $comentario);
            if(Encuesta::insertarEncuestaDB($encuesta) > 0){
                $payload = json_encode(array("Encuesta" => $encuesta, "mensaje" => "Se ha publicado la encuesta, ¡Vuelva pronto!"));
            }
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
    }

    public function ObtenerMejoresEncuestasController($request, $response, $args){
        $payload = json_encode(array("error" => "No se han podido obtener encuestas."));

        if (isset($args["cantidad"])){
            $cantidad = $args["cantidad"];
            $encuestas = Encuesta::obtenerMejoresEncuestas($cantidad);
            $payload = json_encode(array("Mejores encuestas" => $encuestas));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
    }
}
?>