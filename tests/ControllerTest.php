<?php

use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../src/Helpers/Common.php');
require_once(__DIR__ . '/../src/Core/Controller.php');

class ControllerTest extends TestCase
{
    public function testRenderWithoutTemplate()
    {
        $controller = new Controller();

        $this->defineConstant('APP_NAME', 'hdicarlo');
        $this->defineConstant('APP_DESCRIPTION', 'hdicarlo');
        $this->defineConstant('APP_COLOR', '#000');
        $this->defineConstant('URL_PATH', 'hdicarlo');
        $this->defineConstant('SESS_USER', '1');
        $this->defineConstant('APP_AUTHOR_WEB', 'paul');
        $this->defineConstant('APP_AUTHOR', 'paul');
        $this->defineConstant('VIEW_PATH', __DIR__ . '/../src/Views');  // Definir VIEW_PATH

        $content = $controller->render('home.view.php', ['name' => 'Ana'], '', true);
        $this->assertStringContainsString('Ana', $content);
    }

    public function testRenderWithTemplate()
    {
        $controller = new Controller();

        $this->defineConstant('APP_NAME', 'hdicarlo');
        $this->defineConstant('APP_DESCRIPTION', 'hdicarlo');
        $this->defineConstant('APP_COLOR', '#000');
        $this->defineConstant('URL_PATH', 'hdicarlo');
        $this->defineConstant('SESS_USER', '1');
        $this->defineConstant('APP_AUTHOR_WEB', 'paul');
        $this->defineConstant('APP_AUTHOR', 'paul');
        $this->defineConstant('VIEW_PATH', __DIR__ . '/../src/Views');  // Definir VIEW_PATH

        $content = $controller->render('home.view.php', ['name' => 'Ana'], 'layouts/site.layout.php', true);
        $this->assertStringContainsString('Ana', $content);
    }

    private function defineConstant($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}
