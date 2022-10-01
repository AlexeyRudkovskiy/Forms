<?php

namespace YProjects\Forms\Contracts;

use Illuminate\Http\Request;

interface ComponentContract
{

    public const HANDLE_BEFORE_SAVE = 'beforeSave';
    public const HANDLE_AFTER_SAVE = 'afterSave';

    public static function make(string $name): self;

    public function setName(string $name): self;

    public function getName(): string;

    public function setValue($value): self;

    public function getValue(): mixed;

    public function setGroup(string $group, ?string $groupTitle = null): self;

    public function getGroup(): string;

    public function getGroupTitle(): string;

    public function setLocation(string $location): self;

    public function getLocation(): string;

    public function setHandleOn(string $on = self::HANDLE_BEFORE_SAVE): self;

    public function getHandleOn(): string;

    public function isAlreadyHandled(): bool;

    public function getType(): string;

    public function setCustomOption(string $name, mixed $value): self;

    public function getCustomOptions(): array;

    public function setParentForm(FormContract $formContract): self;

    public function getParentForm(): FormContract;

    public function handle(mixed $data, ?ComponentContract $parent = null): mixed;

    public function applyChanges(mixed $data, mixed $parentData): mixed;

}
