<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . "/../vendor/autoload.php";

// Acceso a BD
require_once "./db/AccesoDatos.php";

// Middlewares
require_once "./middlewares/Logger.php";
require_once "./middlewares/MWAcceso.php";
require_once "./middlewares/JWT.php";

// Controladores
require_once "./controllers/ArchivoController.php";
require_once "./controllers/EmpleadoController.php";
require_once "./controllers/EncuestaController.php";
require_once "./controllers/LoginController.php";
require_once "./controllers/MesaController.php";
require_once "./controllers/OrdenController.php";
require_once "./controllers/PedidoController.php";
require_once "./controllers/UsuarioController.php";

date_default_timezone_set("America/Argentina/Buenos_Aires");
// Error Handling
error_reporting(-1);
ini_set("display_errors", 1);
ini_set("display_startup_errors", "1");

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add Path for local testing
$app->setBasePath("/tp_comanda/app");
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

/* RUTAS - USUARIOS */
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{id_usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->delete('/{id_usuario}', \UsuarioController::class . ':BorrarUno');
    $group->put('/modificar[/]', \UsuarioController::class . ':ModificarUno');
    $group->post('/login', \UsuarioController::class . ':Login');
})->add(\MWAcceso::class . ':esAdmin');

/* RUTAS - EMPLEADOS (solo administrador) */
$app->group('/empleados', function (RouteCollectorProxy $group) {
  $group->get('[/]', \EmpleadoController::class . ':TraerTodos');
  $group->get('/{id_empleado}', \EmpleadoController::class . ':TraerUno');
  $group->post('[/]', \EmpleadoController::class . ':CargarUno');
  $group->delete('/{id_empleado}', \EmpleadoController::class . ':BorrarUno');
  $group->put('/{id_empleado}', \EmpleadoController::class . ':ModificarUno');
})->add(\MWAcceso::class . ':esAdmin');

/* RUTAS - PEDIDOS */
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\MWAcceso::class . ':esMozo');
  $group->get('/{id_pedido}', \PedidoController::class . ':TraerUno')->add(\MWAcceso::class . ':esMozo');
  $group->get('/listar/area', \PedidoController::class . ':TraerSegunArea')->add(\MWAcceso::class . ':esEmpleado');
  $group->get('/listar/tiempo', \PedidoController::class . ':TraerPedidosTiempo')->add(\MWAcceso::class . ':esAdmin');
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\MWAcceso::class . ':esMozo');
  $group->delete('/{id_pedido}', \PedidoController::class . ':BorrarUno')->add(\MWAcceso::class . ':esAdmin');
  $group->put('/{id_pedido}', \PedidoController::class . ':ModificarUno')->add(\MWAcceso::class . ':esEmpleado');
});

/* RUTAS - ORDENES */
$app->group('/ordenes', function (RouteCollectorProxy $group) {
  $group->get('[/]', \OrdenController::class . ':TraerTodos')->add(\MWAcceso::class . ':esEmpleado');
  $group->get('/{id_orden}', \OrdenController::class . ':TraerUno')->add(\MWAcceso::class . ':esEmpleado');
  $group->post('[/]', \OrdenController::class . ':CargarUno')->add(\MWAcceso::class . ':esMozo');
  $group->delete('/{id_orden}', \OrdenController::class . ':BorrarUno')->add(\MWAcceso::class . ':esAdmin');
  $group->put('/{id_pedido}', \OrdenController::class . ':ModificarUno')->add(\MWAcceso::class . ':esEmpleado');
});

/* RUTAS - MESAS */
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos')->add(\MWAcceso::class . ':esMozo');
  $group->get('/{id_mesa}', \MesaController::class . ':TraerUno')->add(\MWAcceso::class . ':esMozo');
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(\MWAcceso::class . ':esAdmin');
  $group->delete('/{id_mesa}', \MesaController::class . ':BorrarUno')->add(\MWAcceso::class . ':esAdmin');
  $group->put('/cobrar/{id_mesa}', \MesaController::class . ':CobrarUno')->add(\MWAcceso::class . ':esMozo');
  $group->put('/{id_mesa}', \MesaController::class . ':ModificarUno')->add(\MWAcceso::class . ':esMozo');
  $group->put('/cerrar/{id_mesa}', \MesaController::class . ':ModificarUnoAdmin')->add(\MWAcceso::class . ':esAdmin');
});

/* RUTAS - CLIENTE */
$app->group('/cliente', function (RouteCollectorProxy $group) {
  $group->get('/{codigo_mesa}/{pedido_id}', \MesaController::class . ':TraerDemoraPedidoMesa');
  $group->post('/encuesta', \EncuestaController::class . ':CrearEncuesta');
});

/* RUTAS - LOGIN */
$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \LoginController::class . ':VerificarUsuario');
});

/* RUTAS - ADMINISTRACION */
$app->group('/admin', function (RouteCollectorProxy $group) {
  $group->post('/mejores-encuestas[/]', \EncuestaController::class . ':ObtenerMejoresEncuestas');
  $group->post('/reportes[/]', \ArchivoController::class . ':DescargarPDF');
})->add(\MWAcceso::class . ':esAdmin');

/* RUTAS - CARGA CSV */
$app->group('/archivos', function (RouteCollectorProxy $group) {
  $group->get('/escritura', \ArchivoController::class . ':Escritura');
  $group->get('/lectura', \ArchivoController::class . ':Lectura');
})->add(\MWAcceso::class . ':esAdmin');

/* RUTAS - INFO PRINCIPAL */
$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write('Trabajo Practico: "La comanda" | Diseñado por: Lautaro N. Díaz, 3°D 2C 2002');
    return $response;
});

$app->run();

// TAREAS PENDIENTES
/*
  - TERMINAR MODELS - listo
  - TERMINAR CONTROLLERS - listo
  - TERMINAR MIDDLEWARES - listo
  - TERMINAR INDEX - listo
  - TERMINAR BASE DE DATOS - listo
  - TESTEAR TODO
  - PROBAR PDF Y LOGS
  - COMPLETAR POSTMAN
*/