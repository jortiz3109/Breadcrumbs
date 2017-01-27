# JohnDev Breadcrumbs package

This package helps you to generate the breadcrumb component for your app in Laravel

## Compatibilty
This package is currently only compatible with [Laravel](http://www.laravel.com) 5.3.*

## Instalation
### Using [Composer](http://getcomposer.org/):

To install this component using **composer**, simply run this command on the base path of your Laravel project:

```bash
composer require johndev/breadcrumbs
```
or add `"johndev/breadcrumbs": "^0.1.0"` to your `composer.json` file and then run the command:

```bash
composer update
```

### Configuration
Next, add the BreadcrumbsServiceProvider container to the *Package Service Providers* section in **provider** element on your **app.php** config file:

```
'providers' => [
    // ...
    JohnDev\Breadcrumbs\BreadcrumbsServiceProvider::class,
    // ...
],
```

### Facades
Finally add to **aliases** element in your **app.php** config file:


```
'Breadcrumbs' => JohnDev\Breadcrumbs\Facades\Breadcrumbs::class,
```

## Usage
Simply call the function `Breadcrumbs::render()` in apropiate section on your layout template or view.
