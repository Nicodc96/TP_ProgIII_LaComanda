<?php
class Producto{
    public $id;
    public $nombre;
    public $precio;
    public $tipo;
    public $stock;
    public $activo;

    public function __construct(){}

    public static function crearProducto($nombre, $precio, $tipo, $stock, $activo){
        $producto = new Producto();
        $producto->nombre = $nombre;
        $producto->precio = $precio;
        $producto->tipo = $tipo;
        $producto->stock = $stock;
        $producto->activo = $activo;
        return $producto;
    }

    public static function insertarProductoDB($producto){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (nombre, precio, tipo, stock, activo)
        VALUES (:nombre, :precio, :tipo, :stock, :activo)");
        try{
            $consulta->bindValue(':nombre', $producto->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $producto->precio, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $producto->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':stock', $producto->stock, PDO::PARAM_INT);
            $consulta->bindValue(':activo', $producto->activo, PDO::PARAM_BOOL);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function mostrarProductosTabla(){
        $arrayProductos = self::obtenerTodos();
        $mensaje = "Lista vacia.";
        if (is_array($arrayProductos) && count($arrayProductos) > 0){
            $mensaje = "<h3 align='center'> Lista de Productos </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Tipo</th><th>Stock</th><th>Activo</th></tr><tbody>";
            foreach($arrayProductos as $producto){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $producto->id . "</td>" .
                "<td>" . $producto->nombre . "</td>" .
                "<td>" . $producto->precio . "</td>" .
                "<td>" . $producto->tipo . "</td>" .
                "<td>" . $producto->stock . "</td>" .
                "<td>" . $producto->activo . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function mostrarProductoTabla($producto){
        $mensaje = "Lista vacia.";
        if (is_a($producto, "Producto")){
            $mensaje = "<h3 align='center'> Lista de Productos </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Tipo</th><th>Stock</th><th>Activo</th></tr><tbody>";
            $mensaje .= "<tr align='center'>" .
            "<td>" . $producto->id . "</td>" .
            "<td>" . $producto->nombre . "</td>" .
            "<td>" . $producto->precio . "</td>" .
            "<td>" . $producto->tipo . "</td>" .
            "<td>" . $producto->stock . "</td>" .
            "<td>" . $producto->activo . "</td></tr>";
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }
    public static function obtenerProducto($productoId){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $productoId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function modificarProducto($productoParam){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET nombre = :nombre, precio = :precio, tipo = :tipo, stock = :stock WHERE id = :id");
        try{
            $consulta->bindValue(':usuario', $productoParam->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $productoParam->precio, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $productoParam->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':stock', $productoParam->stock, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function restarStock($productoId, $cantidad){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET stock = (stock - :cantidad) WHERE id = :id");
        try{
            $consulta->bindValue(':id', $productoId, PDO::PARAM_INT);
            $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }

    public static function eliminarProducto($productoId){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET activo = :estado WHERE id = :id");
        try{
            $consulta->bindValue(':id', $productoId, PDO::PARAM_INT);
            $consulta->bindValue(':estado', false, PDO::PARAM_BOOL);
            $consulta->execute();
        }catch(\Throwable $err){
            echo $err->getMessage();
        }
        return $consulta->rowCount() > 0;
    }
}
?>