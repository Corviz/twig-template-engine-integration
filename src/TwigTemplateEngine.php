<?php

namespace Corviz\LayoutEngine\Twig;

use Corviz\Application;
use Corviz\Mvc\View\TemplateEngine;

class TwigTemplateEngine implements TemplateEngine
{
    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Process and render a template.
     *
     * @param string $file
     * @param array $data
     *
     * @return string
     */
    public function draw(string $file, array $data): string
    {
        //Initialize if not initialized already.
        $this->isInitialized() || $this->initialize();

        $file = substr($file, strlen($this->getAppDirectory().'views/'));
        $this->twig->render($file, $data);
    }

    /**
     * @return string
     */
    private function getAppDirectory()
    {
        return Application::current()->getDirectory();
    }

    /**
     * Set up environment.
     */
    private function initialize()
    {
        $appPath = $this->getAppDirectory();
        $templatesPath = "{$appPath}views";

        //If no cache is set, attempt to create one
        if (empty($this->cachePath)) {
            $this->cachePath = "{$templatesPath}/cache/twig";
            if (!is_dir($this->cachePath)) {
                if (!mkdir($this->cachePath, 0777, true)) {
                    throw new \Exception('Could not setup twig cache');
                }
            }
        }

        if (!is_dir($this->cachePath) || !is_writable($this->cachePath)) {
            throw new \Exception('Invalid cache');
        }

        $loader = new \Twig_Loader_Filesystem($templatesPath);
        $this->twig = new \Twig_Environment($loader, [
            'cache' => $this->cachePath,
        ]);
    }

    /**
     * @return bool
     */
    private function isInitialized(): bool
    {
        return empty($this->twig);
    }
}