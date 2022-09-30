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
    protected bool $isPassword = false;

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
            'is_email' => $this->isEmail,
            'is_password' => $this->isPassword
        ];
    }

    public function email(): self
    {
        $this->isEmail = true;
        return $this;
    }

    public function password(): self
    {
        $this->isPassword = true;
        return $this;
    }

}
