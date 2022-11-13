<?php
require_once "./models/Producto.php";
require_once "./interfaces/IApiUsable.php";

class ProductoController extends Producto implements IApiUsable{
    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $producto = Producto::crearProducto(
            $parametros["nombre"],
            $parametros["precio"],
            $parametros["tipo"],
            $parametros["stock"],
            true
        );

        if (Producto::insertarProductoDB($producto) > 0){
            $payload = json_encode(array("mensaje" => "Producto registrado con exito."));
        } else{
        $payload = json_encode(array("mensaje" => "Error al registrar el producto."));
        }
  
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno($request, $response, $args){
        $producto = Producto::obtenerProducto($args['productoId']);        
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function TraerTodos($request, $response, $args){
        $listaProductos = Producto::obtenerTodos();
        $payload = json_encode(array("lista_de_productos" => $listaProductos));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function BorrarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        Producto::eliminarProducto($parametros["productoId"]) ? 
        $mensaje = "Producto borrado con exito" : 
        $mensaje = "No se ha podido eliminar el producto.";

        $payload = json_encode(array("mensaje" => $mensaje));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
	public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $producto = Producto::ObtenerProducto($args["productoId"]);
        $mensaje = "No se ha podido modificar al producto";
        if ($producto){
            $producto->nombre = $parametros["usuario"];
            $producto->precio = $parametros["precio"];
            $producto->tipo = $parametros["tipo"];
            $producto->stock = $parametros["stock"];
            if (Producto::modificarProducto($producto)){
              $mensaje = "Usuario modificado con exito";
            }
        }
        $payload = json_encode(array("mensaje" => $mensaje));
  
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
?>