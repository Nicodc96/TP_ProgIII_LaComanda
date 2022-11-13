<?php
class Mesa{
    public $id;
    public $codigo_mesa;
    public $id_empleado;
    public $estado;

    public function __construct(){}

    public static function crearMesa($codigo, $idEmpleado, $estado){
        $mesa = new Mesa();
        $mesa->codigo_mesa = $codigo;
        $mesa->id_empleado = $idEmpleado;
        $mesa->estado = $estado;
        return $mesa;
    }

    public static function insertarMesaDB($mesa){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo_mesa, id_empleado, estado)
        VALUES (:codigo, :id_empleado, :estado)");
        try{
            $consulta->bindValue(':codigo', $mesa->codigo_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':id_empleado', $mesa->id_empleado, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $mesa->estado, PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function mostrarMesasTabla(){
        $arrayMesas = self::obtenerTodos();
        $mensaje = "Lista vacia.";
        if (is_array($arrayMesas) && count($arrayMesas) > 0){
            $mensaje = "<h3 align='center'> Lista de Mesas </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Codigo</th><th>ID Empleado Asociado</th><th>Estado</th></tr><tbody>";
            foreach($arrayMesas as $mesa){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $mesa->id . "</td>" .
                "<td>" . $mesa->codigo_mesa . "</td>" .
                "<td>" . $mesa->id_empleado . "</td>" .
                "<td>" . $mesa->estado . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function mostrarMesaTabla($mesa){
        $mensaje = "Lista vacia.";
        if (is_a($mesa, "Mesa")){
            $mensaje = "<h3 align='center'> Lista de Mesas </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Codigo</th><th>ID Empleado Asociado</th><th>Estado</th></tr><tbody>";
            $mensaje .= "<tr align='center'>" .
            "<td>" . $mesa->id . "</td>" .
            "<td>" . $mesa->codigo_mesa . "</td>" .
            "<td>" . $mesa->id_empleado . "</td>" .
            "<td>" . $mesa->estado . "</td></tr>";
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function obtenerMesa($mesaId){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $mesaId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificarMesa($mesaParam){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET codigo_mesa = :codigo, id_empleado = :id_emp, estado = :estado WHERE id = :id");
        try{
            $consulta->bindValue(':codigo', $mesaParam->codigo_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':id_emp', $mesaParam->id_empleado, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $mesaParam->estado, PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function eliminarMesa($mesaId){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
        try{
            $consulta->bindValue(':id', $mesaId, PDO::PARAM_INT);
            $consulta->bindValue(':estado', "cerrada", PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }
}
?>