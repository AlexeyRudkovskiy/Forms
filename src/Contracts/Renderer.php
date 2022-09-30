<?php

namespace YProjects\Forms\Contracts;

use YProjects\Forms\Components\BaseForm;

interface Renderer
{

    public function render(BaseForm $component): mixed;

}
