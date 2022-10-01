<?php

namespace YProjects\Forms\Traits;

trait WithAjaxDataProvider
{

    protected bool $isAjax = false;

    public function ajax(): self
    {
        $this->isAjax = true;
        return $this;
    }

}
