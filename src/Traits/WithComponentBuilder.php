<?php

namespace YProjects\Forms\Traits;

trait WithComponentBuilder
{

    protected mixed $objectBuilder = null;

    public function setComponentBuilder(callable $builder): self
    {
        $this->objectBuilder = $builder;
        return $this;
    }

}
