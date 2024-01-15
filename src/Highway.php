<?php

namespace VenderaTradingCompany\LaravelHighway;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

abstract class Highway
{
    protected static $namespace = 'App';

    private static function _route(string $route, array $parameters = []): string
    {
        $url = route($route, $parameters);

        return $url;
    }

    public static function route(string $route, array $parameters = []): string
    {
        return self::_route($route, $parameters);
    }

    public static function redirect(string $route, array $parameters = [])
    {
        return redirect()->secure(self::route($route, $parameters));
    }

    public static function listable($model)
    {
        self::get($model, 'list', 'list');
    }

    public static function editable($model)
    {
        self::get($model, 'create', 'create');
        self::post($model, 'store', 'store');
        self::get($model, '{id}/edit', 'edit');
        self::post($model, '{id}/update', 'update');
        self::any($model, '{id}/destroy', 'destroy');
    }

    public static function file($page, string $path = '/', string $method = 'show')
    {
        self::_method($page, $path, $method, 'get');
    }

    public static function get($page, string $path = '/', string $method = 'show')
    {
        self::_method($page, $path, $method, 'get');
    }

    public static function post($page, string $path, string $method)
    {
        self::_method($page, $path, $method, 'post');
    }

    public static function any($page, string $path, string $method)
    {
        self::_method($page, $path, $method, 'any');
    }

    public static function _namespace()
    {
        return static::$namespace;
    }

    private static function _method(mixed $entity, string $path, mixed $method, mixed $routeType = 'get')
    {
        $id = static::get_class_name($entity);

        $path = self::_path($id, $path);

        $routeName = self::_routeName(strtolower($id), $method);

        $methodName = self::_methodName($method);

        $namespace = static::_namespace();

        $context_name = static::get_class_name();

        $class = $namespace . '\\Controllers\\' .$context_name . '\\'. $id . 'Controller';

        if (!method_exists($class, $methodName)) {
            if ($routeType == 'get') {
                Route::$routeType($path, function () use ($methodName, $namespace, $id, $routeName, $entity, $context_name) {
                    return App::call($namespace . '\\Controllers\\' .$context_name . '\\'.  $id . 'Controller@view', [
                        'entity' => $entity,
                        'type' => $methodName,
                        'namespace' => $namespace,
                        'routeName' => $routeName,
                        'contextName' => $context_name
                    ]);
                })->name($routeName);
            } else {
                Route::$routeType($path, function () use ($methodName, $namespace, $id, $routeName, $entity, $context_name) {
                    return App::call($namespace . '\\Controllers\\' .$context_name . '\\'.  $id . 'Controller@action', [
                        'entity' => $entity,
                        'type' => $methodName,
                        'namespace' => $namespace,
                        'routeName' => $routeName,
                        'contextName' => $context_name
                    ]);
                })->name($routeName);
            }
        } else {
            Route::$routeType($path, $namespace . '\\Controllers\\' . $context_name . '\\'. $id . 'Controller@' . $methodName)->name($routeName);
        }
    }

    private static function _path($id, $path)
    {
        $id = strtolower($id);

        $context_name = strtolower(static::get_class_name());

        if ($id == 'home') {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $id . $path;
        }

        if ($context_name != 'web') {
            return $context_name . '/' . $id . '/' . $path;
        }

        return $id . '/' . $path;
    }

    private static function _methodName($method)
    {
        if (!str_contains($method, '.')) {
            return $method;
        }

        $result = '';

        $explodedMethod = explode('.', $method);

        foreach ($explodedMethod as $value) {
            $result .= ucfirst($value);
        }

        $result = lcfirst($result);

        return $result;
    }

    private static function _routeName($id, $method)
    {
        $context_name = strtolower(static::get_class_name());

        $routeName = $id;

        $explodedMethod = explode('.', $method);

        foreach ($explodedMethod as $value) {
            $routeName .= '.' . strtolower($value);
        }

        return $context_name . '.' . $routeName;
    }

    public static function get_class_name(mixed $class = null)
    {
        if (empty($class)) {
            return ucfirst(last(explode('\\', static::class)));
        }

        return ucfirst(last(explode('\\', $class)));
    }
}
