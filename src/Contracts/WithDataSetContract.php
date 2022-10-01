<?php

namespace YProjects\Forms\Contracts;

use Illuminate\Support\Collection;

interface WithDataSetContract
{

    public function getAvailableData(array $filters = []): Collection;

    public function getDataValueKey(): string;

    public function getDataTextKey(): string;

}
