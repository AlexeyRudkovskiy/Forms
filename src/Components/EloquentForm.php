<?php

namespace YProjects\Forms\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use YProjects\Forms\Contracts\ComponentContract;
use YProjects\Forms\Exceptions\RelationMethodDoesNotExist;

abstract class EloquentForm extends BaseForm
{

    protected bool $isEloquentModel = true;
    protected mixed $saveCallback = null;

    abstract public function getInitialValue(): mixed;

    public function handle(mixed $data, ?ComponentContract $parent = null): mixed
    {
        $value = $this->getValue() ?? $this->getInitialValue();
        $value = empty($value) ? $this->getInitialValue() : $value;

        $fields = $this->fields;

        /** @var ComponentContract $field */
        foreach ($this->fields as $field) {
            if ($field->getHandleOn() === ComponentContract::HANDLE_AFTER_SAVE) {
                continue ;
            }

            $value = $this->handleField($field, $value, $data);
        }

        if ($parent === null) {
            if ($this->saveCallback === null) {
                $value->save();
                $this->setValue($value);
            } else {
                $value = call_user_func($this->saveCallback, $value);
                $this->setValue($value);
            }
        } else {
            if ($parent instanceof Collection) {
                $value = $parent->applyChangesFor($value);
            } else {
                $value = $this->applyChanges($value, $parent->getValue());
            }
            $this->isHandled = true;
        }

        /** @var ComponentContract $field */
        foreach ($this->fields as $field) {
            if ($field->getHandleOn() === ComponentContract::HANDLE_BEFORE_SAVE) {
                continue ;
            }

            $value = $this->handleField($field, $value, $data);
        }

        return $value;
    }

    public function applyChanges(mixed $data, mixed $parentData): mixed
    {
        if ($this->isEloquentModel) {
            $methodName = $this->relationName;
            $methodName = Str::camel($methodName);

            if (method_exists($parentData, $methodName)) {
                $relation = call_user_func_array([
                    $parentData, $methodName
                ], [ ]);

                $relation->save($data);
            } else if ($parentData instanceof Model) {
                throw new RelationMethodDoesNotExist('Method ' . $methodName . ' does not exists in ' . get_class($parentData));
            }

            return $parentData;
        }

        return parent::applyChanges($data, $parentData);
    }

    public function setSaveCallback(mixed $callback): self
    {
        $this->saveCallback = $callback;
        return $this;
    }

}
