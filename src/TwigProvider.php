<?php

namespace Corviz\LayoutEngine\Twig;

use Corviz\DI\Provider;
use Corviz\Mvc\View;

class TwigProvider extends Provider
{
    /**
     * Init dependencies in the application container.
     */
    public function register()
    {
        $this->container()->setSingleton(
            View\TemplateEngine::class,
            TwigTemplateEngine::class
        );

        View::setExtension('twig');
    }
}
