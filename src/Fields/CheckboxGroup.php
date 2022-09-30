<?php

namespace YProjects\Forms\Fields;

use Illuminate\Support\Collection;
use YProjects\Forms\Components\BaseField;
use YProjects\Forms\Contracts\ComponentContract;

/**
 * @method static CheckboxGroup make(string $name)
 */
class CheckboxGroup extends BaseField
{

    protected array $options = [];

    protected string $labelProperty = 'label';
    protected string $valueProperty = 'id';

    /** @var callable $optionsResolver */
    protected mixed $optionsResolver = null;

    public function getType(): string
    {
        return 'checkbox-group';
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
        $options = [
            'items' => $this->getAvailableData()
        ];

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

    /**
     * @param array $options
     * @return CheckboxGroup
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabelProperty(): string
    {
        return $this->labelProperty;
    }

    /**
     * @param string $labelProperty
     * @return CheckboxGroup
     */
    public function setLabelProperty(string $labelProperty): self
    {
        $this->labelProperty = $labelProperty;
        return $this;
    }

    /**
     * @return string
     */
    public function getValueProperty(): string
    {
        return $this->valueProperty;
    }

    /**
     * @param string $valueProperty
     * @return CheckboxGroup
     */
    public function setValueProperty(string $valueProperty): self
    {
        $this->valueProperty = $valueProperty;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptionsResolver(): mixed
    {
        return $this->optionsResolver;
    }

    /**
     * @param mixed $optionsResolver
     * @return CheckboxGroup
     */
    public function setOptionsResolver(mixed $optionsResolver): self
    {
        $this->optionsResolver = $optionsResolver;
        return $this;
    }

    protected function getAvailableData(): Collection
    {
        $options = [];

        if ($this->optionsResolver !== null) {
            $options = call_user_func($this->optionsResolver, $this);
            if (! $options instanceof Collection) {
                $options = collect($options);
            }
        } else {
            $options = collect($this->getOptions());
        }

        $value = $this->getValueProperty();
        $label = $this->getLabelProperty();

        return $options
            ->map(fn (mixed $item) => [ 'value' => $item[$value], 'label' => $item[$label] ]);
    }

}
