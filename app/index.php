<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

// Acceso a BD
require_once './db/AccesoDatos.php';

// Middlewares
// require_once './middlewares/Logger.php';

// Controladores
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', '1');

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add Path for local testing
$app->setBasePath('/tp_comanda/app');
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Rutas
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    //$group->delete('/{usuarioId}', \UsuarioController::class . ':BorrarUno');
  });

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Trabajo Practico: 'La comanda' | Diseñado por: Lautaro N. Díaz, 3°D 2C 2002");
    return $response;
});

$app->delete("/usuarios/{usuarioId}", function (Request $request, Response $response) {
  $response = \UsuarioController::class . ":BorrarUno";
  return $response;
});

$app->run();
