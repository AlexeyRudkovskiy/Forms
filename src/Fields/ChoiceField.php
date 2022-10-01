<?php

namespace YProjects\Forms\Fields;

use Illuminate\Support\Collection;
use YProjects\Forms\Components\BaseField;
use YProjects\Forms\Contracts\ComponentContract;
use YProjects\Forms\Contracts\WithDataSetContract;
use YProjects\Forms\Traits\WithAjaxDataProvider;

/**
 * @method static ChoiceField make(string $name)
 */
class ChoiceField extends BaseField implements WithDataSetContract
{

    use WithAjaxDataProvider;

    protected array $options = [];

    protected bool $isMultiple = false;
    protected bool $isExpanded = false;

    protected string $labelProperty = 'label';
    protected string $valueProperty = 'id';

    /** @var callable $optionsResolver */
    protected mixed $optionsResolver = null;

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
        $options = [];
        $options['is_ajax'] = $this->isAjax;

        if ($this->isAjax) {
            $ajaxRoute = config('forms.ajax_route_name', null);
            if ($ajaxRoute === null) {
                throw new \Exception("Please specify route name before using Ajax data providers!");
            }
            $options['items'] = [];
            $formClass = get_class($this->getParentForm());
            $mapping = config('forms.ajax', []);
            if (!array_key_exists($formClass, $mapping)) {
                throw new \Exception("Please create mapping before using Ajax data providers!");
            }

            $formMapping = $mapping[$formClass];

            $options['url'] = route($ajaxRoute, [
                'form' => $formMapping['name'],
                'field' => $this->getName()
            ]);
        } else {
            $options['items'] = $this->getAvailableData();
        }

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

    /**
     * @param array $options
     * @return ChoiceField
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
     * @return ChoiceField
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
     * @return ChoiceField
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
     * @return ChoiceField
     */
    public function setOptionsResolver(mixed $optionsResolver): self
    {
        $this->optionsResolver = $optionsResolver;
        return $this;
    }

    public function getAvailableData(array $filters = []): Collection
    {
        if ($this->optionsResolver !== null) {
            $options = call_user_func($this->optionsResolver, $this, $filters);
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

    public function getDataValueKey(): string
    {
        return 'value';
    }

    public function getDataTextKey(): string
    {
        return 'label';
    }

}
