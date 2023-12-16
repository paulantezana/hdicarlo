<?php

use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../src/route.php');
require_once(__DIR__ . '/../src/Core/Controller.php');

class RouterTest extends TestCase
{
    public function testAdminRouteLoggedInUser()
    {
        // Simular el inicio de sesión
        session_start();

        // Mock URL and SESSION data for an admin route
        $this->defineConstant('URL', '/admin');
        $this->defineConstant('SESS_KEY', 'user_session_key');
        $this->defineConstant('SESS_USER', 'user');  // Definir SESS_USER
        $this->defineConstant('CONTROLLER_PATH', __DIR__ . '/../src/Controllers');  // Definir CONTROLLER_PATH
        $this->defineConstant('MODEL_PATH', __DIR__ . '/../src/Models');  // Definir MODEL_PATH

        $_SESSION[SESS_KEY] = 1;
        $_SESSION[SESS_USER] = ['company_id' => 1];

        // Create a Router instance
        $router = new Router();

        // Access private properties for testing
        $group = $this->getPrivateProperty($router, 'group');
        $controller = $this->getPrivateProperty($router, 'controller');
        $method = $this->getPrivateProperty($router, 'method');
        $param = $this->getPrivateProperty($router, 'param');

        // Assert the route matches expectations
        $this->assertEquals('admin/', $group);
        $this->assertEquals('HomeController', $controller);
        $this->assertEquals('home', $method);
        $this->assertNull($param);
    }

    // Helper function to access private properties
    private function getPrivateProperty($instance, $propertyName)
    {
        $reflection = new ReflectionClass($instance);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($instance);
    }

    private function defineConstant($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}
