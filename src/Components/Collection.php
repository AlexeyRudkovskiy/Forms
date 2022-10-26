<?php

namespace YProjects\Forms\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use YProjects\Forms\Contracts\ComponentContract;
use YProjects\Forms\Exceptions\RelationMethodDoesNotExist;
use YProjects\Forms\Traits\WithComponentBuilder;

/**
 * @method static Collection make(string $name)
 */
class Collection extends BaseField
{

    use WithComponentBuilder;

    public const STRATEGY_DETACH = 'detach';
    public const STRATEGY_DELETE = 'delete';

    protected ?string $childFormClass = null;
    protected ?string $childIdField = 'id';
    protected string $removeStrategy = self::STRATEGY_DELETE;

    protected ?ComponentContract $parent = null;

    public function setChildFormClass(string $childFormClass): self
    {
        $this->childFormClass = $childFormClass;
        return $this;
    }

    public function setChildIDField(string $childIdField): self
    {
        $this->childIdField = $childIdField;
        return $this;
    }

    /**
     * @param string $strategy
     * @see self::STRATEGY_DELETE
     * @see self::STRATEGY_DETACH
     * @return $this
     */
    public function setRemoveStrategy(string $strategy): self
    {
        $this->removeStrategy = $strategy;
        return $this;
    }

    public function handle(mixed $data, ?ComponentContract $parent = null): mixed
    {
        $this->parent = $parent;

        if (!$data instanceof \Illuminate\Support\Collection) {
            $data = collect($data);
        }

        $value = $this->getValue();
        $updateIds = $data
            ->map(fn ($item) => $item['@key'] ?? null)
            ->filter()
            ->toArray();

        $updateItems = collect([]);
        $updateByIds = collect([]);
        $removeItems = collect([]);
        $createItems = collect($data);

        $createItems = $createItems->filter(fn ($item) => !array_key_exists('@key', $item));

        $value = $value ?? collect([]);

        if ($value instanceof \Illuminate\Support\Collection || $value instanceof \Illuminate\Database\Eloquent\Collection) {
            $updateItems = $value->filter(fn ($item) => in_array($this->getItemId($item), $updateIds));
            $removeItems = $value->filter(fn ($item) => !in_array($this->getItemId($item), $updateIds));
        } else {
            throw new \Exception("Items for Collection form field should be placed in parent object as Laravel Collection");
        }

        $updateByIds = collect($data)
            ->filter(fn ($item) => array_key_exists('@key', $item))
            ->mapWithKeys(fn ($item) => [ $item['@key'] => $item ]);

        $updateItems->each(function ($item) use ($updateByIds) {
            $form = $this->getFormObject();
            $itemId = $this->getItemId($item);
            $updateData = $updateByIds[$itemId];

            $form->fill($item);
            $form->handle($updateData);
        });

        $createItems->each(function ($item) {
            $form = $this->getFormObject();
            $value = $form->handle($item, $this);
        });

        $this->removeItems($removeItems);

        return $data;
    }

    public function applyChangesFor(mixed $data): mixed
    {
        /** @var Model $parentData */
        $parentData = $this->parent->getValue();
        $methodName = $this->getName();
        $methodName = Str::camel($methodName);

        if (method_exists($parentData, $methodName)) {
            $relationship = call_user_func_array([$parentData, $methodName], []);
            $value = $relationship->save($data);

            return $value;
        } else {
            throw new RelationMethodDoesNotExist('Method ' . $methodName . ' is not exists in ' . get_class($parentData));
        }
    }

    public function getType(): string
    {
        return 'collection';
    }

    public function getFormObject(): BaseForm
    {
        $formClass = $this->childFormClass;
        /** @var BaseForm $form */
        $form = new $formClass("@collection::" . $this->getName());

        if ($this->objectBuilder !== null) {
            $form = call_user_func($this->objectBuilder, $form);
        }

        $form->build();

        return $form;
    }

    public function getItemId(mixed $object): string
    {
        return sha1(get_class($object) . '@' . $object->{$this->childIdField});
    }

    protected function removeItems(\Illuminate\Support\Collection $items, ): self
    {
        if ($this->removeStrategy === self::STRATEGY_DELETE) {
            $items->each(fn(Model $item) => $item->delete());
        } else {
            /// TODO: Implement detach strategy
        }

        return $this;
    }

}
