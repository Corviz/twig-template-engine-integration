<?php

namespace Corviz\LayoutEngine\Twig;

use Corviz\Application;
use Corviz\Mvc\View\TemplateEngine;

class TwigTemplateEngine implements TemplateEngine
{
    /**
     * @var bool
     */
    private static $cacheEnabled = true;

    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Disables caching.
     */
    public static function disableCache()
    {
        self::$cacheEnabled = false;
    }

    /**
     * Enables caching.
     */
    public static function enableCache()
    {
        self::$cacheEnabled = true;
    }

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
        return $this->twig->render($file, $data);
    }

    /**
     * @param string $templatesPath
     *
     * @throws \Exception
     */
    private function createCache(string $templatesPath)
    {
        //Doesn't attempt to create caching dir when not required
        if (!self::$cacheEnabled)
            return;

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

        $this->createCache($templatesPath);

        $loader = new \Twig_Loader_Filesystem($templatesPath);
        $this->twig = new \Twig_Environment($loader, [
            'cache' => self::$cacheEnabled ? $this->cachePath : false,
        ]);
    }

    /**
     * @return bool
     */
    private function isInitialized(): bool
    {
        return !empty($this->twig);
    }
}
