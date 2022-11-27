<?php
require_once "./models/LogsUsuarios.php";

class ArchivoController extends LogsUsuarios{
    public function Lectura($request, $response, $args){
        $nombre_archivo = "./Logs/historial_loginUsuarios.csv";
        $info_a_leer = LogsUsuarios::leerArchivoCSV($nombre_archivo);
        $payload = json_encode(array("error" => "No se ha podido leer el archivo solicitado."));
        if(!is_null($info_a_leer)){
            echo "<h2>La información se ha leido e ingresado a la base de datos correctamente</h2>";
            $payload = json_encode(array("mensaje" => "Archivo ingresado a la base de datos.", "Registro de logins" => $info_a_leer));
        }
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
    }
    public function Escritura($request, $response, $args){
        $logs_db = LogsUsuarios::obtenerTodos();
        $nombre_archivo = "./Logs/historial_loginUsuarios.csv";
        $payload = json_encode(array("error" => "El archivo no se ha podido guardar", "Registro de logins" => "Error en escritura"));
        if(LogsUsuarios::escribirArchivoCSV($logs_db, $nombre_archivo)){
            echo "Archivo salvado en: " . $nombre_archivo;
            echo LogsUsuarios::mostrarLogsUsuariosTabla($logs_db);
            $payload = json_encode(array("mensaje" => "Archivo guardado como: historial_loginUsuarios.csv ", "Registro de logins" => $logs_db));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
    }

    public function DescargarPDF($request, $response, $args){
        $params = $request->getParsedBody();
        $directorio = './Logs/';
        $payload = json_encode(array("error" => "El archivo no se ha podido guardar", "Mejores encuestas" => "Error en lectura"));
        
        if($params["cantidad_encuestas"]){
            $cantidad_encuestas = $params["cantidad_encuestas"];
            $payload = Encuesta::DescargarPDF($directorio, $cantidad_encuestas);
            echo "Archivo guardado en: ". $directorio;
        }
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader("Content-Type", "application/json");
    }
}
?>