<?php

namespace JohnDev\Breadcrumbs;

use Illuminate\Routing\Router;
use Illuminate\Support\HtmlString;
use Illuminate\View\Factory as View;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Translation\Translator as Lang;

class BreadcrumbsBuilder
{

    use Macroable {
        Macroable::__call as macroCall;
    }

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

    /**
     * The default breadcrumbs view.
     *
     * @var string
     */
    public static $defaultView = 'breadcrumbs::default';

    public function __construct(View $view, Router $router)
    {
        $this->router = $router;
        $this->view = $view;
    }

    /* GETTERS */

    /**
     * Get translation file to search for translations
     * @return String
     */
    private function getTranslationFile()
    {
        return isset($this->lang_file)
            ? $this->lang_file
            : 'breadcrumbs';
    }

    /**
     * Return route name for the provided segment
     * @param  String $segment segment to inspect
     * @return String|NULL
     */
    private function getRouteName($segment)
    {
        $routes = $this->getRoutes();
        foreach ($routes as $route) {
            if ($segment === $route->uri()) {
                return $route->getName();
            }
        }
        return NULL;
    }

    /**
     * Return content of the breadcrumb link
     * @param  String $segment name of route segment
     * @return String
     */
    private function getLinkContent($segment)
    {
        $field = $this->getTranslationFile().'.'.$segment;

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
            $route_uri = $this->getRouteUri(implode('/', $route));
            if (!empty($route_uri)) {
                $links[] = (object) [
                    'route' => $route_uri,
                    'body'  => $this->getTitle($segment)
                ];
            }
        }

        return new HtmlString($this->view->make($view ?: static::$defaultView, compact('links'))->render());
    }

}
