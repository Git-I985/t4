<?php

namespace T4\Mvc\Renderers;

use T4\Core\IArrayable;
use T4\Mvc\ARenderer;
use T4\Mvc\Controller;

class Vanilla
    extends ARenderer
{

    // TODO: непонятно что с этим делать. Вообще-то надо во View этот метод использовать, а не здесь
    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function render($template, $data = [])
    {
        if ($data instanceof IArrayable) {
            extract($data->toArray());
        } else {
            extract((array)$data);
        }

        $templatePath = $this->findTemplate($template);
        ob_start();
        include $templatePath;
        $content = ob_get_clean();

        return $content;
    }

}