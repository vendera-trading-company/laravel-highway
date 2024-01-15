<?php

namespace VenderaTradingCompany\LaravelHighway;

use VenderaTradingCompany\PHPActions\Response;
use VenderaTradingCompany\PHPActions\Action;
use Illuminate\Routing\Controller;

abstract class HighwayController extends Controller
{
    private array $attachedData = [];

    protected static $entity;

    private function attachData(string $key, mixed $data)
    {
        $this->attachedData[$key] = $data;
    }

    private function getAttachedData(): array
    {
        return [];
    }

    protected function _getAttachedData(): array
    {
        return array_merge($this->attachedData, $this->getAttachedData());
    }

    private function get_action_class_name(string | null $prefix, string | null $suffix)
    {
        if (empty($prefix)) {
            $prefix = '';
        }

        if (empty($suffix)) {
            $suffix = '';
        }

        return $prefix . static::get_class_name(static::$entity) . $suffix;
    }

    public function _action(string $type, string | null $namespace = null, $contextName): Response | null
    {
        $action_class_name = $this->get_action_class_name(null, $type);

        $entity_class_name = static::get_class_name(static::$entity);

        if (!empty($contextName)) {
            $action_class =  $namespace . '\\Actions\\' . $contextName . '\\' . $entity_class_name . '\\'  . $action_class_name;

            if (!class_exists($action_class)) {
                $action_class = $namespace . '\\Actions\\' . $entity_class_name . '\\'  . $action_class_name;
            }
        } else {
            $action_class =  $namespace . '\\Actions\\' . $entity_class_name . '\\'  . $action_class_name;
        }


        if (!class_exists($action_class)) {
            return null;
        }

        $actionData = Action::run($action_class);

        if (!empty($actionData->getData())) {
            if (is_array($actionData->getData())) {
                foreach ($actionData->getData() as $key => $value) {
                    $this->attachData($key, $value);
                }
            }
        }

        return $actionData;
    }

    public function _responseAction(string $type, array | null $data = [], string | null $namespace = null): Response | null
    {
        $action_class_name = $this->get_action_class_name('Response', $type);

        $entity_class_name = static::get_class_name(static::$entity);

        if (!empty($contextName)) {
            $action_class =  $namespace . '\\Actions\\' . $contextName . '\\' . $entity_class_name . '\\'  . $action_class_name;

            if (!class_exists($action_class)) {
                $action_class = $namespace . '\\Actions\\' . $entity_class_name . '\\'  . $action_class_name;
            }
        } else {
            $action_class =  $namespace . '\\Actions\\' . $entity_class_name . '\\'  . $action_class_name;
        }

        if (!class_exists($action_class)) {
            return null;
        }

        $actionData = Action::run($action_class, $data ?? []);

        if (!empty($actionData->getData())) {
            if (is_array($actionData->getData())) {
                foreach ($actionData->getData() as $key => $value) {
                    $this->attachData($key, $value);
                }
            }
        }

        return $actionData;
    }

    private static function _shouldReturnAsResponse(mixed $value)
    {
        if (empty($value)) {
            return false;
        }

        if ($value instanceof \Illuminate\Http\RedirectResponse) {
            return true;
        }

        if ($value instanceof \Illuminate\Http\Response) {
            return true;
        }

        if ($value instanceof \Illuminate\Http\JsonResponse) {
            return true;
        }

        if ($value instanceof \Illuminate\Contracts\View\Factory) {
            return true;
        }

        if ($value instanceof \Illuminate\Contracts\View\View) {
            return true;
        }

        if ($value instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            return true;
        }

        return false;
    }

    public function action(string $entity, string $type, string $namespace, string $routeName, string $contextName)
    {
        $type = ucfirst($type);

        $actionData = $this->_action($type, $namespace, $contextName);

        $responseData = $this->_responseAction($type, is_array($actionData?->getData()) ? $actionData->getData() : null, $namespace, $contextName);

        if ($this->_shouldReturnAsResponse($responseData?->getData())) {
            return $responseData->getData();
        }

        if ($this->_shouldReturnAsResponse($actionData?->getData())) {
            return $actionData->getData();
        }

        return redirect()->back();
    }

    public function view(string $entity, string $type, string $namespace, string $routeName, string $contextName)
    {
        $type = ucfirst($type);

        $actionData = $this->_action($type, $namespace, $contextName);

        $responseData = $this->_responseAction($type, is_array($actionData?->getData()) ? $actionData?->getData() : null, $namespace, $contextName);

        if ($this->_shouldReturnAsResponse($responseData?->getData())) {
            return $responseData->getData();
        }

        if ($this->_shouldReturnAsResponse($actionData?->getData())) {
            return $actionData->getData();
        }

        $route_class = 'App\Routing\\' . $namespace;

        return $route_class::view('pages.' . strtolower($namespace) . '.' . $routeName, $this->_getAttachedData());
    }

    public static function get_class_name(mixed $class = null)
    {
        if (empty($class)) {
            return ucfirst(last(explode('\\', static::class)));
        }

        return ucfirst(last(explode('\\', $class)));
    }
}
