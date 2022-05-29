# Maxters Router
A Simple PHP Router for PHP 8

Example:
```php

$router = new Maxters\Router\Router;

$router->get('/', fn () => 'Home Page');

$router->get('/blog/{slug}', fn ($slug) => "Blog $slug");

$router->execute($_SERVER['PATH_INFO'], HttpVerbs::tryFrom($_SERVER['HTTP_REQUEST_METHOD']));

```
