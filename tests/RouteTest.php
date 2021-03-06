<?php

use Maxters\Router\Exceptions\RouteDoesNotMatchException;
use Maxters\Router\HttpVerbs;
use Maxters\Router\Route;
use PHPUnit\Framework\TestCase;


class RouteTest extends TestCase
{
    public function testGetValuesFromPattern()
    {
        $route = new Route('/user/{user}', HttpVerbs::PUT, function (string $user) {
           return 'User ' . $user; 
        });

        $params = $route->extractParametersFromPath('/user/wallace');
        $this->assertEquals($params, ['wallace']);
    }


    public function testGetValuesFromPatternWhenDoesntMatch()
    {
        $route = new Route('/user/{user}', HttpVerbs::PUT, function (string $user) {
            return 'User ' . $user;
        });

        try {
            $route->extractParametersFromPath('/invalid/wallace');
        } catch (RouteDoesNotMatchException $e) {
            $this->assertInstanceOf(RouteDoesNotMatchException::class, $e);
        }

    }
}