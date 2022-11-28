<?php
require_once "./db/AccesoDatos.php";
class Area{
    public $id;
    public $descripcion;
    public static $areas_trabajo = array(1 => "Salon", 2 => "Cocina", 3 => "Bar", 4 => "Administracion");

    public function __construct(){}

    public function insertarAreaDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO area (descripcion) VALUES (:descripcion)");
        try{
            $consulta->bindValue(":descripcion", $this->descripcion, PDO::PARAM_STR);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function actualizarArea($area){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE area SET descripcion = :descripcion WHERE id = :id");
        try{
            $consulta->bindValue(":descripcion", $area->descripcion, PDO::PARAM_STR);
            $consulta->bindValue(":id", $area->id, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function borrarArea($areaId){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM area WHERE id = :id");
        try{
            $consulta->bindValue(":id", $areaId, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function obtenerAreasPorId($areaId){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM area WHERE id = :id");
        $consulta->bindValue(":id", $areaId, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject("Area");
    }

    public static function obtenerAreasPorDescripcion($area_desc){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM area WHERE descripcion = :descripcion");
        $consulta->bindValue(":descripcion", self::$areas_trabajo[$area_desc], PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject("Area");
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM area");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Area");
    }
}
?>