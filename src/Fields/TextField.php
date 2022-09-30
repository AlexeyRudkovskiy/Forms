<?php

namespace YProjects\Forms\Fields;

use YProjects\Forms\Components\BaseComponent;
use YProjects\Forms\Components\BaseField;
use YProjects\Forms\Contracts\ComponentContract;

/**
 * @method static TextField make(string $name)
 */
class TextField extends BaseField
{

    protected bool $isEmail = false;

    public function email(): self
    {
        $this->isEmail = true;
        return $this;
    }

    public function handle(mixed $data, ?ComponentContract $parent = null): mixed
    {
        return (string) $data;
    }

    public function getType(): string
    {
        return 'text';
    }

    public function getViewOptions(): array
    {
        return [
            'is_email' => $this->isEmail
        ];
    }

}
