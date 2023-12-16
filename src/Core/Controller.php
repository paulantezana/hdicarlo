<?php

class Controller
{
    public function render(string $path, array $parameter = [], string $template = '', bool $return = false)
    {

        $content = requireToVar(VIEW_PATH . '/' . $path, $parameter);

        if ($template === '') {
            if ($return) {
                return $content;
            } else {
                echo $content;
                return;
            }
        }

        if ($return) {
            ob_start();
            require_once(VIEW_PATH . '/' . $template);
            return ob_get_clean();
        } else {
            require_once(VIEW_PATH . '/' . $template);
        }
    }

    public function redirect(string $url = "")
    {
        header('Location: ' . URL_PATH . $url);
    }
}
