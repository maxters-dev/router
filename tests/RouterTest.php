<?php

use Maxters\Router\Exceptions\RouteNotFoundException;
use Maxters\Router\Route;

use Maxters\Router\Router;
use Maxters\Router\HttpVerbs;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testCreateRoute()
    {
        $route = (new Router)->createRoute('/', HttpVerbs::GET, fn () => 'Result');

        $this->assertEquals('/', $route->pattern);
    }

    public function testGet()
    {
        $route = (new Router)->get('/user', fn () => 'User #1');

        $this->assertEquals('/user', $route->pattern);

        $this->assertEquals('User #1', ($route->action)());
    }

    public function testFindRoute()
    {
        $tests = [
            '/',
            '/blog/5',
            '/blog/five'
        ];

        $router = (new Router);

        $router->get('/', static fn () => 'Home');
        $router->get('/blog/{id}', static fn () => 'Blog');

        foreach ($tests as $path) {
            $result = $router->findRoute($path, HttpVerbs::GET);
            $this->assertInstanceOf(Route::class, $result);
        }
    }

    public function testExecute()
    {

        $router = new Router;
        $router->get('/blog/{slug}', static fn ($id) => 'Blog ' . $id);
        $router->get('/user/{id}', static fn ($id) => 'User ' . $id)->where('id', '\d+');
        $router->get(
            '/user/{user}/post/{post}', 
            static fn (int $user, int $post) => "User $user Post $post"
        );

        $tests = [
            '/user/1/post/100'  => 'User 1 Post 100',
            '/user/50/post/20'  => 'User 50 Post 20',
            '/blog/first-post'  => 'Blog first-post',
            '/blog/second-post' => 'Blog second-post',
            '/blog/third_post'  => 'Blog third_post',
            '/blog/3'           => 'Blog 3',
            '/user/5'           => 'User 5',
            '/user/100'         => 'User 100',
        ];

        foreach ($tests as $path => $expected) {
            $result = $router->execute($path, HttpVerbs::GET);
            $this->assertEquals($expected, $result);
        }

        $router->post(
            '/extra-params/{id}', 
            fn ($request, $response, int $id) => [$request, $response, $id]
        );

        $result = $router->execute('/extra-params/1000', HttpVerbs::POST, ['Request', 'Response']);

        $this->assertEquals(['Request', 'Response', 1000], $result);

    }

    public function testPut()
    {
        $router = new Router();

        $router->put('/', fn () => 'Updated');

        $action = $router->execute('/', HttpVerbs::PUT);

        $this->assertEquals('Updated', $action);
    }

    public function testPost()
    {
        $router = new Router();

        $router->post('/new-user', fn () => 'Created');

        $action = $router->execute('/new-user', HttpVerbs::POST);

        $this->assertEquals('Created', $action);
    }

    public function testOptions()
    {
        $router = new Router();

        $router->options('/get-options', fn () => 'The Options');

        $action = $router->execute('/get-options', HttpVerbs::OPTIONS);

        $this->assertEquals('The Options', $action);
    }

    public function testTrace()
    {
        $router = new Router();

        $router->trace('/trace', fn () => 'Trace');

        $action = $router->execute('/trace', HttpVerbs::TRACE);

        $this->assertEquals('Trace', $action);
    }

    public function testDelete()
    {
        $router = new Router();

        $router->delete('/user/{id}', fn ($id) => 'Deleted ' . $id);

        $action = $router->execute('/user/1000', HttpVerbs::DELETE);

        $this->assertEquals('Deleted 1000', $action);
    }

    public function testPatch()
    {
        $router = new Router();

        $router->patch('/patch/{id}', fn ($id) => 'Patch ' . $id);

        $action = $router->execute('/patch/200', HttpVerbs::PATCH);

        $this->assertEquals('Patch 200', $action);
    }

    public function testCreateRouteWithCallableNonClosure()
    {
        $router = new Router();

        $router->get('/', new class {
            public function __invoke()
            {
                return 'Invokable Class';
            }
        });

        $result = $router->execute('/', HttpVerbs::GET);

        $this->assertEquals('Invokable Class', $result);
    }

    public function testFindRouteWhenNotFound()
    {
        $router = new Router();

        try {
            $router->findRoute('/not-found', HttpVerbs::GET);   
        } catch (RouteNotFoundException $e) {
            $this->assertInstanceOf(RouteNotFoundException::class, $e);
        }
        
    }
}

