<?php

namespace JohnDev\Breadcrumbs;

use Illuminate\Http\Request;
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
     * Current route array segments
     * @var Array
     */
    private $segments = [];

    /**
     * Object to get component translations
     * @var Illuminate\Translation\Translator
     */
    private $lang;

    /**
     * The default breadcrumbs view.
     *
     * @var string
     */
    public static $defaultView = 'breadcrumbs::default';

    /**
     * Class constructor
     * @param View    $view View Factory
     * @param Router  $router Router
     * @param Lang    $lang Translator
     * @param Request $request Http Request
     */
    public function __construct(View $view, Router $router, Lang $lang, Request $request)
    {
        $this->router = $router;
        $this->view = $view;
        $this->segments = explode('/', $request->route()->uri());
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
                return !empty($route->getName())
                        ? $route->getName()
                        : $this->routeNameDoted($path);
            }
        }
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
     * @param  String $route_name name of route segment
     * @return String
     */
    private function getLinkContent($route_name)
    {
        $field = 'breadcrumbs::links.'.$route_name;

        $content = $this->lang->get($field);

        return $content === $field
            ? $route_name
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

            $links[] = (object) [
                'uri' => $this->getRouteUri($route),
                'body'  => $this->getLinkContent($route_name)
            ];
        }

        return new HtmlString($this->view->make($view ?: static::$defaultView, compact('links'))->render());
    }

    /**
     * return the route name in doted format
     * @param  String $path Route path to translate
     * @return String translated path route to dots
     */
    private function routeNameDoted($path)
    {

        $path = str_replace('/', '.', $path);

        // Replace route params in route uri
        $path = preg_replace("/(\{[a-zA-Z0-9]*\}.?)/", '', $path);
        $path = preg_replace("/(\{[a-zA-Z0-9]*\})/", '', $path);

        return $path;
    }
}
