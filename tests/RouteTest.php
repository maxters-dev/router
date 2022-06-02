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

        $result = $route->getValuesFromPattern('/user/wallace');
        $this->assertEquals($result, ['wallace']);
    }


    public function testGetValuesFromPatternWhenDoesntMatch()
    {
        $route = new Route('/user/{user}', HttpVerbs::PUT, function (string $user) {
            return 'User ' . $user;
        });

        try {
            $route->getValuesFromPattern('/invalid/wallace');
        } catch (RouteDoesNotMatchException $e) {
            $this->assertInstanceOf(RouteDoesNotMatchException::class, $e);
        }

    }
}