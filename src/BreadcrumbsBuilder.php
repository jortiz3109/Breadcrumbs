<?php

namespace JohnDev\Breadcrumbs;

use Illuminate\Routing\Router;
use Illuminate\Support\HtmlString;
use Illuminate\View\Factory as View;
use Illuminate\Translation\Translator as Lang;

class BreadcrumbsBuilder
{

    /**
     * The View factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    private $view;

    /**
     * The Router instance
     * @var Illuminate\Routing\Router
     */
    private $router;

    /**
     * File used to search for translations
     * @var String
     */
    private $lang_file;

    private $segments = [];

    private $lang;

    /**
     * The default breadcrumbs view.
     *
     * @var string
     */
    public static $defaultView = 'breadcrumbs::default';

    public function __construct(View $view, Router $router, Lang $lang, Array $segments)
    {
        $this->router = $router;
        $this->view = $view;
        $this->segments = array_merge($segments);
        $this->lang = $lang;
    }

    /**
     * Return route name for the provided path
     * @param  Array $path path to inspect
     * @return String|NULL
     */
    private function getRouteName(Array $path)
    {
        $routes = $this->getRoutes();
        $path = implode('/', $path);
        foreach ($routes as $route) {
            if ($path === $route->uri()) {
                return $route->getName();
            }
        }
        return NULL;
    }

    /**
     * Return route uri for the provided path
     * @param  Array $path path to inspect
     * @return String|NULL
     */
    private function getRouteUri(Array $path)
    {
        $routes = $this->getRoutes();
        $path = implode('/', $path);
        foreach ($routes as $route) {
            if ($path === $route->uri()) {
                return $route->uri();
            }
        }
        return NULL;
    }

    /**
     * Return content of the breadcrumb link
     * @param  String $segment name of route segment
     * @return String
     */
    private function getLinkContent($route_name)
    {
        $field = 'breadcrumbs::links.'.$route_name;

        $content = $this->lang->get($field);

        return $content === $field
            ? $segment
            : $content;

    }

    /**
     * Get array of routes
     * @return Array
     */
    private function getRoutes()
    {
        return $this->router->getRoutes();
    }

    /**
     * Render breadcrumb
     * @return Illuminate\Support\HtmlString
     * The default template could be published (into resources/views/breadcrumbs/)
     * or be located inside the components directory (vendor/johndev/breadcrumbs/templates/)
     */
    public function render($view = null)
    {
        $links = array();
        $route = array();

        foreach ($this->segments as $segment) {
            $route[] = $segment;
            $route_name = $this->getRouteName($route);
            if (!empty($route_name)) {
                $links[] = (object) [
                    'uri' => $this->getRouteUri($route),
                    'body'  => $this->getLinkContent($route_name)
                ];
            }
        }

        return new HtmlString($this->view->make($view ?: static::$defaultView, compact('links'))->render());
    }

}
