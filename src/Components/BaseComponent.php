<?php

namespace YProjects\Forms\Components;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use YProjects\Forms\Contracts\ComponentContract;
use YProjects\Forms\Contracts\FormContract;

abstract class BaseComponent implements ComponentContract
{

    protected string $name = '';

    protected string $location = 'default';

    protected string $group = 'default';

    protected string $groupTitle = 'Default';

    protected mixed $value = null;

    protected string $handleOn = ComponentContract::HANDLE_BEFORE_SAVE;

    protected bool $isHandled = false;

    protected array $customOptions = [];

    protected ?FormContract $parentForm = null;

    public static function make(string $name): ComponentContract
    {
        $className = get_called_class();

        /** @var ComponentContract $component */
        $component = new $className($name);
        $component->setName($name);

        return $component;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setGroup(string $group, ?string $groupTitle = null): self
    {
        $this->group = $group;

        if ($groupTitle !== null) {
            $this->groupTitle = $groupTitle;
        } else {
            $this->groupTitle = Str::headline($group);
        }

        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getGroupTitle(): string
    {
        return $this->groupTitle;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }


    public function setHandleOn(string $on = 'beforeSave'): self
    {
        $this->handleOn = $on;
        return $this;
    }

    public function getHandleOn(): string
    {
        return $this->handleOn;
    }

    public function isAlreadyHandled(): bool
    {
        return $this->isHandled;
    }

    public function setCustomOption(string $name, mixed $value): self
    {
        $this->customOptions[$name] = $value;
        return $this;
    }

    public function getCustomOptions(): array
    {
        return $this->customOptions;
    }

    public function setParentForm(FormContract $formContract): self
    {
        $this->parentForm = $formContract;
        return $this;
    }

    public function getParentForm(): FormContract
    {
        return $this->parentForm;
    }

    public function applyChanges(mixed $data, mixed $parentData): mixed
    {
        $parentData[$this->getName()] = $data;
        return $parentData;
    }

}
