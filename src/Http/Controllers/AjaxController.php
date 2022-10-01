<?php

namespace YProjects\Forms\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use YProjects\Forms\Components\BaseForm;
use YProjects\Forms\Contracts\ComponentContract;
use YProjects\Forms\Contracts\FormContract;
use YProjects\Forms\Contracts\WithDataSetContract;


class AjaxController extends BaseController
{
    use ValidatesRequests;

    public function __invoke(Request $request)
    {
        $formName = $request->query->get('form');
        $fieldName = $request->query->get('field');

        $form = $this->getFormInstance($formName);

        /** @var WithDataSetContract $field */
        $field = collect($form->getCreatedFields())
            ->first(fn (ComponentContract $component) => $component->getName() === $fieldName);

        $filters = $request->get('filters', []);
        $data = $field->getAvailableData($filters);

        return [
            'data' => $data,
            'value_key' => $field->getDataValueKey(),
            'label_key' => $field->getDataTextKey(),
            'count' => count($data)
        ];
    }

    protected function getFormInstance(string $name): FormContract
    {
        $forms = config('forms.ajax', []);
        if (count($forms) < 1) {
            throw new \Exception("There are no mapping in forms config file!");
        }

        $form = collect($forms)
            ->map(fn ($value, $key) => [ 'className' => $key, 'value' => $value ])
            ->first(fn ($item) => $item['value']['name'] === $name);

        if ($form === null) {
            throw new \Exception("Form with name '" . $name . "' is not found in forms config file!");
        }

        $form = $form['className'];

        /** @var BaseForm $formInstance */
        $formInstance = new $form($name);
        $formInstance->build();

        return $formInstance;
    }

}
