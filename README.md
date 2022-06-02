# Maxters Router
A Simple PHP Router for PHP 8

Example:
```php
use Maxters\Router\Router;
use Maxters\Router\HttpVerbs;
use Maxters\Router\Exceptions\RouteNotFoundException;

$router = new Router;

$router->get('/', fn () => 'Home Page');

$router->get('/blog/{slug}', fn ($slug) => "Blog $slug");

$path = $_SERVER['PATH_INFO'] ?? '/';
$method = HttpVerbs::from($_SERVER['REQUEST_METHOD'] ?? 'GET');

try {
    echo $router->execute($path, $method);
} catch (RouteNotFoundException $e) {
    http_response_code(404);
    echo '<strong>Page not found</strong>';
}
```
