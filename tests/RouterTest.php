<?php

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
        $router->get('/user/{user}/post/{post}', static fn (int $user, int $post) => "User $user Post $post");

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

    }
}

