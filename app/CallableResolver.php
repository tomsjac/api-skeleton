<?php

namespace App;

/**
 * Dynamic Routing System for Slim Framework
 * Depending on the route, trying to resolve the call to the controller and action
 *
 * Exemple :
 * \App\CallableResolver::addRoute($app)->withNamespace('\\App\\controller')->load();
 * => Route in folder app/controller
 *
 * \App\CallableResolver::addRoute($app)->withGroup('auth')->withNamespace('\\App\\controller\\auth')->load();
 * => url : /auth/ in folder app/controller/auth
 *
 * \App\CallableResolver::addRoute($app)->withVersionGroup('2.0')->withGroup('auth')->withNamespace('\\App\\controller\\auth')->load();
 *  => url : /2.0/auth/ in folder app/controller/auth With version Url
 *
 * @author Thomas
 * @version 1.0.0
 */

use Exception;
use Slim\App;

class CallableResolver
{
    /**
     * Slim application object reference
     *
     * @var App
     */
    private $app = null;

    /**
     * Grouped routes, first level name
     *
     * @var string
     */
    private $group = null;

    /**
     * Grouped routes, second level name
     *
     * @var string
     */
    private $versionGroup = null;

    /**
     * Namespace to be used on controllers autoloading
     *
     * @var string
     */
    private $namespace = null;

    /**
     * Main factory constructor, protected to avoid direct instances
     *
     * @param App $app Reference to a previously instanced Slim app
     * @return void
     */
    public function __construct(App $app)
    {
        if (is_a($app, '\\Slim\\App') === true) {
            $this->app = $app;
        }
    }

    /**
     * Gets a copy of Factory object to perform parametrizations
     *
     * @param App $app Reference to a previously instanced Slim app
     * @return CallableResolver
     */
    static public function addRoute(App $app)
    {
        $oResol = new self($app);
        return $oResol;
    }

    /**
     * Sets route main grouping option
     *
     * @param string|null $group Route group name
     * @return CallableResolver
     */
    public function withGroup(?string $group)
    {
        if (empty($group) === false or $group == null) {
            $this->group = $group;
        }
        return $this;
    }

    /**
     * Sets a secondary group, intended to group api versions
     *
     * @param string $group Route group name
     * @return CallableResolver
     */
    public function withVersionGroup(string $group)
    {
        if (empty($group) === false or $group == null) {
            $this->versionGroup = $group;
        }
        return $this;
    }

    /**
     * Sets controllers namespace
     *
     * @param string $namespace Namespace to autoload controllers
     * @return CallableResolver
     */
    public function withNamespace(string $namespace)
    {
        if (empty($namespace) === false or $namespace == null) {
            $this->namespace = $namespace;
        }
        return $this;
    }

    /**
     * If necessary, performs slim app creation and configures options in order
     * to use dinamic routing
     *
     * @return App
     */
    public function load()
    {
        // Sets main and secondary routing groups.
        if ($this->group !== null && $this->versionGroup != '') {
            $this->setGroups($this->versionGroup, $this->group);
        } elseif ($this->versionGroup !== null) {
            $this->setGroups($this->versionGroup);
        } elseif ($this->group !== null) {
            $this->setGroups($this->group);
        } else {
            // Sets dynamic routing for bare routes.
            $this->setMap($this->namespace);
        }
        return $this->app;
    }

    /**
     * Sets routing groups to be used on dynamic routing
     *
     * @param string|null $group Main group name
     * @param string|null $subGroup Secondary group name
     */
    private function setGroups(?string $group, ?string $subGroup = null)
    {
        $ns = $this->namespace;
        $resolver = $this;
        $this->app->group('/' . $group,
            function () use ($subGroup, $ns, $resolver) {
                // Version group
                if ($subGroup !== null) {
                    /** @var App $this */
                    $this->group('/' . $subGroup,
                        function () use ($ns, $resolver) {
                            $resolver->setMap($ns);
                        }
                    ); // End api group.
                } else {
                    $resolver->setMap($ns);
                }
            }
        );
    }

    /**
     * Sets main dynamic routing params into Slim app
     * @param string $namespace
     */
    public function setMap(string $namespace)
    {
        $resolver = $this;
        $this->app->any('/{controller}[/[{method}[/{params:.*}]]]',
            function ($request, $response, $args) use ($namespace, $resolver) {

                // Get config namespace if necessary
                $calledController = $namespace . '\\' . ucfirst($args['controller']);

                if (class_exists($calledController)) {
                    $controller = new $calledController($this, $request, $response);
                } else {
                    throw new Exception('Controller not found : ' . $calledController);
                }

                //Add Argument Controller
                $funcArgs = $resolver->getArgs();

                //Method
                if (isset($args['method']) && method_exists($controller, $args['method']) === true) {
                    $method = $args['method'];
                } else if (method_exists($controller, 'index') === true) {
                    $method = 'index';
                } else {
                    throw new Exception('Method not found in the controller : ' . $calledController);
                }

                //Call Action
                return call_user_func_array(array($controller, $method), $funcArgs);
            });
    }

    /**
     * Sets an array to be used to fill parameters in controllers call
     * @return array
     */
    public function getArgs()
    {
        return [];
    }
}