<?php
namespace App\services\dependencies;

use Interop\Container\Exception\ContainerException;
use Slim\Container;
use Slim\Views\Twig;

/**
 * Templating With Twig
 * @author thomas
 */
class Views extends Twig
{
    /**
     * Construct
     * @param Container $container
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        $settings = $container->get('settings');
        $options = $settings->get('views');

        parent::__construct($options['path'], [
            'cache' => $options['cache']
        ]);
    }

    /**
     * Return Object View
     * @return Twig
     */
    public function __invoke()
    {
        return $this;
    }
}