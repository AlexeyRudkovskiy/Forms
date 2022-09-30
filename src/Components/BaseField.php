<?php

namespace YProjects\Forms\Components;

use YProjects\Forms\Contracts\FieldContract;

abstract class BaseField extends BaseComponent implements FieldContract
{

    public function getViewOptions(): array
    {
        return [];
    }

}
