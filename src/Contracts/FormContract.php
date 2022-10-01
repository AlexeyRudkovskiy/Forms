<?php

namespace YProjects\Forms\Contracts;

interface FormContract
{

    public function getName(): string;

    public function getFields(): array;

    public function getCreatedFields(): array;

    public function getLayout(): array;

}
