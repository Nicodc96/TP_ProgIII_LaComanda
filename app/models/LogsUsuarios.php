<?php

class LogsUsuarios{
    public $id;
    public $usuario_id;
    public $nombre_usuario;
    public $fecha_login;

    public function __construct(){}

    public static function crearLogsUsuarios($usuario_id, $nombre_usuario, $fecha_login){
        $logUsuarios = new LogsUsuarios();
        $logUsuarios->usuario_id = $usuario_id;
        $logUsuarios->nombre_usuario = $nombre_usuario;
        $logUsuarios->fecha_login = $fecha_login;

        return $logUsuarios;
    }

    public static function insertarLogsDB($logUsuarios){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logsusuarios (usuario_id, nombre_usuario, fecha_login)
        VALUES (:usuario_id, :nombre_usuario, :fecha_login);");
        $consulta->bindParam(":usuario_id", $logUsuarios->usuario_id);
        $consulta->bindParam(":nombre_usuario", $logUsuarios->nombre_usuario);
        $consulta->bindParam(":fecha_login", $logUsuarios->fecha_login);
        try {
            $consulta->execute();
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }        
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM logsusuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "LogsUsuarios");
    }

    public static function obtenerLogPorId($id_log){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM logsusuarios WHERE id = :id");
        $consulta->bindValue(":id", $id_log, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "LogsUsuarios");
    }

    public static function mostrarLogsUsuariosTabla($array_logsUsuarios = array()){
        $mensaje = "Lista vacia.<br>";
        if (is_array($array_logsUsuarios) && count($array_logsUsuarios) > 0){
            $mensaje = "<h3 align='center'> Historial de login de Usuarios </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Usuario ID</th><th>Nombre de Usuario</th><th>Fecha de Login</th></tr><tbody>";
            foreach($array_logsUsuarios as $log_individual){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $log_individual->id . "</td>" .
                "<td>" . $log_individual->usuario_id . "</td>" .
                "<td>" . $log_individual->nombre_usuario . "</td>" .
                "<td>" . $log_individual->fecha_login . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function leerArchivoCSV($archivo_nombre = "./Logs/historial_loginUsuarios.csv"){
        $archivo = fopen($archivo_nombre, "r");
        $array = array();
        try {
            if (!is_null($archivo) && self::eliminarTabla() > 0){
                echo "<h2>Tabla eliminada correctamente para insertar nueva informacion.</h2>";
            }
            while (!feof($archivo)) {
                $linea = fgets($archivo);
                
                if (!empty($linea)) {
                    $linea = str_replace(PHP_EOL, "", $linea);
                    $array_login = explode(",", $linea);
                    $logUsuarios = self::crearLogsUsuarios($array_login[0], $array_login[1], $array_login[2]);
                    array_push($array, $logUsuarios);
                    self::insertarLogsDB($logUsuarios);
                }
            }
        } catch (\Throwable $th) {
            echo "Se ha producido un error al intentar leer el archivo solicitado.";
        }finally{
            fclose($archivo);
            return $array;
        }
    }

    public static function escribirArchivoCSV($array_logs, $archivo_nombre = "./Logs/historial_loginUsuarios.csv"){
        $escritura_realizada = false;
        $directorio = dirname($archivo_nombre, 1);
        
        try {
            if(!file_exists($directorio)){
                mkdir($directorio, 0777, true);
            }
            $archivo = fopen($archivo_nombre, "w");
            if ($archivo) {
                foreach ($array_logs as $log) {
                    $linea = $log->usuario_id . "," . $log->nombre_usuario . "," . $log->fecha_login . PHP_EOL;
                    fwrite($archivo, $linea);
                    $escritura_realizada = true;
                }
            }
        } catch (\Throwable $th) {
            echo "Se ha producido un error al intentar escribir en el archivo solicitado.";
        }finally{
            fclose($archivo);
        }
        return $escritura_realizada;
    }

    public static function eliminarLogPorID($id_log){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM logsusuarios WHERE id = :id");
        try{
            $consulta->bindValue(":id", $id_log, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function eliminarTabla(){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM logsusuarios");
        try{
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }
}
?>