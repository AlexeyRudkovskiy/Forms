<?php

namespace YProjects\Forms\Renderers;

use YProjects\Forms\Components\BaseField;
use YProjects\Forms\Components\Collection;
use YProjects\Forms\Components\BaseForm;
use YProjects\Forms\Contracts\ComponentContract;
use YProjects\Forms\Contracts\Renderer;

class JsonRenderer implements Renderer
{

    /**
     * @param BaseForm $component
     * @return array
     */
    public function render(BaseForm $component): mixed
    {
        $formData = [];
        $formData['schema'] = $this->mapForm($component);
        $formData['data'] = $component->getValue();

        return $formData;
    }

    protected function mapField(BaseField $component): array
    {
        return [
            'name' => $component->getName(),
            'type' => $component->getType(),
            'options' => $this->mergeOptions($component)
        ];
    }

    protected function mapCollection(Collection $collection): array
    {
        return [
            'name' => $collection->getName(),
            'type' => $collection->getType(),
            'schema' => $this->mapForm($collection->getFormObject()),
            'options' => $this->mergeOptions($collection)
        ];
    }

    protected function mapGeneric(ComponentContract $component): array
    {
        return [
            'name' => $component->getName(),
            'type' => $component->getType()
        ];
    }

    protected function mapForm(BaseForm $form): array
    {
        $fields = $form->getCreatedFields();
        $layout = $form->getLayout();
        $fields = collect($fields);
        $fields = $fields
                ->groupBy(fn (ComponentContract $component) => $component->getLocation())
                ->map(function (\Illuminate\Support\Collection $locationFields, string $key) use ($layout) {
                    $groups = $locationFields
                        ->groupBy(fn (ComponentContract $component) => $component->getGroup())
                        ->map(function (\Illuminate\Support\Collection $groupFields) {
                            /** @var ComponentContract $firstGroupField */
                            $firstGroupField = $groupFields->first();

                            return [
                                'name' => $firstGroupField->getName(),
                                'title' => $firstGroupField->getGroupTitle(),
                                'fields' => $groupFields->map(function (ComponentContract $field) {
                                    if ($field instanceof BaseForm) {
                                        return $this->mapForm($field);
                                    } else if ($field instanceof Collection) {
                                        return $this->mapCollection($field);
                                    } else if ($field instanceof BaseField) {
                                        return $this->mapField($field);
                                    } else {
                                        return $this->mapGeneric($field);
                                    }
                                })
                                ->values()
                            ];
                        })
                        ->values();

                    return [
                        'options' => $layout[$key] ?? [],
                        'groups' => $groups
                    ];
                });

        return [
            'name' => $form->getName(),
            'locations' => $fields
        ];
    }

    protected function mergeOptions(ComponentContract $component)
    {
        $customOptions = $component->getCustomOptions();
        if ($component instanceof BaseField) {
            $options = $component->getViewOptions();
            return array_merge($options, [ 'custom' => $customOptions ]);
        }

        return $customOptions;
    }

}
