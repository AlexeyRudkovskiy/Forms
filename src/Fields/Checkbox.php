<?php

namespace YProjects\Forms\Fields;

use YProjects\Forms\Components\BaseField;
use YProjects\Forms\Contracts\ComponentContract;

/**
 * @method static Checkbox make(string $name)
 */
class Checkbox extends BaseField
{

    public function getType(): string
    {
        return 'checkbox';
    }

    public function handle(mixed $data, ?ComponentContract $parent = null): mixed
    {
        return $data !== null && ($data !== false || $data != '0');
    }

}
