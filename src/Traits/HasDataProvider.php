<?php

namespace YProjects\Forms\Traits;

use Illuminate\Support\Collection;

trait HasDataProvider
{

    protected bool $isAjax = false;

    protected string $labelProperty = 'label';
    protected string $valueProperty = 'id';

    /** @var callable $optionsResolver */
    protected mixed $optionsResolver = null;

    /** @var callable $customItemTransformer */
    protected mixed $customItemTransformer = null;

    /** @var callable $labelFormatter */
    protected mixed $labelFormatter = null;

    /** @var callable $valueFormatter */
    protected mixed $valueFormatter = null;

    public function ajax(): self
    {
        $this->isAjax = true;
        return $this;
    }

    public function isAjax(): bool
    {
        return $this->isAjax;
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
     */
    public function setOptionsResolver(mixed $optionsResolver): self
    {
        $this->optionsResolver = $optionsResolver;
        return $this;
    }

    /**
     * @param mixed $customItemTransformer
     */
    public function setCustomItemTransformer(mixed $customItemTransformer): self
    {
        $this->customItemTransformer = $customItemTransformer;
        return $this;
    }

    /**
     * @param mixed $labelFormatter
     */
    public function setLabelFormatter(mixed $labelFormatter): self
    {
        $this->labelFormatter = $labelFormatter;
        return $this;
    }

    /**
     * @param mixed $valueFormatter
     */
    public function setValueFormatter(mixed $valueFormatter): self
    {
        $this->valueFormatter = $valueFormatter;
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

        if ($this->customItemTransformer !== null) {
            return $options
                ->map(fn (mixed $item) => call_user_func($this->customItemTransformer, $item, $this));
        }

        $valueField = $this->getValueProperty();
        $labelField = $this->getLabelProperty();

        return $options
            ->map(function (mixed $item) use ($valueField, $labelField) {
                if ($this->labelFormatter !== null) {
                    $label = call_user_func($this->labelFormatter, $item, $this);
                } else {
                    $label = $item[$labelField];
                }

                if ($this->valueFormatter !== null) {
                    $value = call_user_func($this->valueFormatter, $item, $this);
                } else {
                    $value = $item[$valueField];
                }

                return [ 'value' => $value, 'label' => $label ];
            });
    }

    public function getDataViewOptions(): array
    {
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

        return $options;
    }

}
