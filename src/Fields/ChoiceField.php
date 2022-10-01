<?php

namespace YProjects\Forms\Fields;

use Illuminate\Console\View\Components\Choice;
use Illuminate\Support\Collection;
use YProjects\Forms\Components\BaseField;
use YProjects\Forms\Contracts\ComponentContract;
use YProjects\Forms\Contracts\WithDataSetContract;
use YProjects\Forms\Traits\HasDataProvider;

/**
 * @method static ChoiceField make(string $name)
 */
class ChoiceField extends BaseField implements WithDataSetContract
{

    use HasDataProvider;

    protected array $options = [];

    protected bool $isMultiple = false;
    protected bool $isExpanded = false;

    /**
     * @param array $options
     * @return ChoiceField
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function getType(): string
    {
        return 'choice';
    }

    public function multiple(): self
    {
        $this->isMultiple = true;
        return $this;
    }

    public function expanded(): self
    {
        $this->isExpanded = true;
        return $this;
    }

    public function handle(mixed $data, ?ComponentContract $parent = null): mixed
    {
        return collect($data)
            ->unique()
            ->toArray();
    }

    public function getViewOptions(): array
    {
        $baseOptions = parent::getViewOptions();
        $options = $this->getDataViewOptions();

        $options['multiple'] = $this->isMultiple;
        $options['expanded'] = $this->isExpanded;

        $options = array_merge_recursive($options, $baseOptions);
        return $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getDataValueKey(): string
    {
        return 'value';
    }

    public function getDataTextKey(): string
    {
        return 'label';
    }

}
