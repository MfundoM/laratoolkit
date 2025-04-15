<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    protected function modelRouteKey(): string
    {
        return $this->guessRouteKey();
    }

    protected function isUpdate(): bool
    {
        return $this->route($this->modelRouteKey())?->id !== null;
    }

    protected function getModelId(): ?int
    {
        return $this->route($this->modelRouteKey())?->id;
    }

    protected function guessRouteKey(): string
    {
        $class = class_basename($this);
        $model = str_replace(['StoreRequest', 'UpdateRequest', 'Request'], '', $class);
        return strtolower($model);
    }
}
