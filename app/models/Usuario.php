<?php
class Usuario{
    public $id;
    public $nombre_usuario;
    public $clave;
    public $esAdmin;
    public $tipo_usuario;
    public $estado;
    public $fecha_alta;
    public $fecha_baja;

    public function __construct(){}

    public static function crearUsuario($nombre_usuario, $pass, $esAdmin, $tipo, $estado, $fechaAlta){
        $usuario = new Usuario();
        $usuario->nombre_usuario = $nombre_usuario;
        $usuario->clave = $pass;
        $usuario->esAdmin = $esAdmin;
        $usuario->tipo_usuario = $tipo;
        $usuario->estado = $estado;
        $usuario->fecha_alta = $fechaAlta;

        return $usuario;
    }

    public static function insertarUsuarioDB($usuario){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (nombre_usuario, clave, esAdmin, tipo_usuario, estado, fecha_alta)
        VALUES (:usuario, :clave, :esAdmin, :tipo, :estado, :fechaAlta)");
        try{
            $claveHasheada = password_hash($usuario->clave, PASSWORD_DEFAULT);
            $consulta->bindValue(':usuario', $usuario->nombre_usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $claveHasheada);
            $consulta->bindValue(':esAdmin', $usuario->esAdmin, PDO::PARAM_BOOL);
            $consulta->bindValue(':tipo', $usuario->tipo_usuario, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $usuario->estado, PDO::PARAM_STR);
            $consulta->bindValue(':fechaAlta', $usuario->fecha_alta, PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function mostrarUsuariosTabla(){
        $arrayUsuarios = self::obtenerTodos();
        $mensaje = "Lista vacia.";
        if (is_array($arrayUsuarios) && count($arrayUsuarios) > 0){
            $mensaje = "<h3 align='center'> Lista de usuarios </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Nombre de Usuario</th><th>Es Administrador</th><th>Tipo de Usuario</th><th>Estado</th><th>Fecha de Alta</th></tr><tbody>";
            foreach($arrayUsuarios as $usuario){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $usuario->nombre_usuario . "</td>" .
                "<td>" . $usuario->esAdmin . "</td>" .
                "<td>" . $usuario->tipo_usuario . "</td>" .
                "<td>" . $usuario->estado . "</td>" .
                "<td>" . $usuario->fecha_alta . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function mostrarUsuarioTabla($usuario){
        $mensaje = "El objeto envíado por parámetro no es un usuario.";
        if (is_a($usuario, "Usuario")){
            $mensaje = "<h3 align='center'> Lista de usuarios </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Nombre de Usuario</th><th>Es Administrador</th><th>Tipo de Usuario</th><th>Estado</th><th>Fecha de Alta</th></tr><tbody>";
            $mensaje .= "<tr align='center'>" .
            "<td>" . $usuario->nombre_usuario . "</td>" .
            "<td>" . $usuario->esAdmin . "</td>" .
            "<td>" . $usuario->tipo_usuario . "</td>" .
            "<td>" . $usuario->estado . "</td>" .
            "<td>" . $usuario->fecha_alta . "</td></tr>";
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
    }

    public static function obtenerUsuario($usuarioId){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE id = :id");
        $consulta->bindValue(":id", $usuarioId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject("Usuario");
    }

    public static function obtenerUsuarioSegunUsername($username){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE nombre_usuario = :nombre_usuario");
        $consulta->bindValue(":nombre_usuario", $username, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject("Usuario");
    }

    public static function modificarUsuario($usuarioParam){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET nombre_usuario = :usuario, clave = :clave WHERE id = :id");
        try{
            $consulta->bindValue(':usuario', $usuarioParam->nombre_usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $usuarioParam->clave, PDO::PARAM_STR);
            $consulta->bindValue(':id', $usuarioParam->id, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function borrarUsuario($usuarioId){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fecha_baja = :fechaBaja WHERE id = :id");
        try{
            $fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(':id', $usuarioId, PDO::PARAM_INT);
            $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }
}