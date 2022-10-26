<?php

namespace YProjects\Forms\Components;

use Illuminate\Support\Str;
use YProjects\Forms\Contracts\ComponentContract;
use YProjects\Forms\Contracts\FormContract;

abstract class BaseForm extends BaseComponent implements FormContract
{

    protected ?string $modelType = null;

    protected ?string $relationName = null;

    protected array $fields = [];

    protected bool $isGroup = false;

    abstract public function getFields(): array;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public function build (): self
    {
        $this->fields = $this->getFields();

        /** @var ComponentContract $field */
        foreach ($this->fields as $field) {
            $field->setParentForm($this);

            if ($field instanceof BaseForm) {
                $field->build();
            }
        }

        return $this;
    }

    public function handle(mixed $data, ?ComponentContract $parent = null): mixed
    {
        $value = $this->getValue();

        /** @var ComponentContract $field */
        foreach ($this->fields as $field) {
            $value = $this->handleField($field, $value, $data);
        }

        return $value;
    }

    /**
     * @param array|\ArrayAccess $data
     * @return void
     */
    public function fill(mixed $data)
    {
        $this->setValue($data);

        /** @var ComponentContract $field */
        foreach ($this->fields as $field) {
            $fieldName = $field->getName();
            $fieldValue = $data[$fieldName] ?? $data[Str::camel($fieldName)] ?? null;

            if ($field instanceof BaseForm) {
                $field->fill($fieldValue ?? []);
            } else {
                $field->setValue($fieldValue);
            }
        }
    }

    public function setRelationName(string $name): self
    {
        $this->relationName = $name;
        return $this;
    }

    public function getType(): string
    {
        return 'form';
    }

    public function getLayout(): array
    {
        return [
            'default' => [
                'classes' => 'col-8'
            ],
            'sidebar' => [
                'classes' => 'col-4'
            ]
        ];
    }

    public function getCreatedFields(): array
    {
        return $this->fields;
    }

    public function asGroup(string $name): self
    {
        $this->isGroup = true;
        $this->setGroup($name);

        return $this;
    }

    protected function handleField(ComponentContract $field, mixed $value, mixed $update): mixed
    {
        $fieldName = $field->getName();
        $fieldValue = $value[$fieldName] ?? $value[Str::camel($fieldName)] ?? null;

        if ($field instanceof BaseForm) {
            $fieldValue = $field->handle($update[$fieldName] ?? [], $this);
        } else {
            $fieldValue = $field->handle($update[$fieldName] ?? $fieldValue, $this);
        }

        return $field->isAlreadyHandled() ? $value : $field->applyChanges($fieldValue, $value);
    }

}
