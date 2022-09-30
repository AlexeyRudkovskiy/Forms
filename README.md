# Y-Forms

This package will help you create different type of forms and handle them

Package provides some functions that makes building forms simpler and clear for
understanding. You should not use HTML to build the form - describe it in PHP
using specific syntax, and we'll catch everything else!

## Installation

To install this package just run next command:

```composer require alexeyrudkovskiy/y-forms```

No need any other configurations - package is ready to use!

## First Form

The primary object in our package is Forms. To create a form just create a new class
and extend ``YProjects\Forms\Components\BaseForm`` or ``YProjects\Forms\Components\EloquentForm``
to create form for your models.

Next, define ``getFields(): array`` method and specify which fields you have.

Please find form example in code below:

```php
use YProjects\Forms\Components\BaseForm;
use YProjects\Forms\Fields\TextField;

class DemoForm extends BaseForm {

    public function getFields(): array
    {
        return [
            TextField::make('title'),
            TextField::make('slug')
        ];
    }

}
```

## What's next?

Next you need to create the form instance and do something with it.

The instance creating process is very simple - create object and execute ``build``
method.

Then you can fill the form and handle requests.

Currently, form rendering is fully in your hands (yeah, I know, I'm working on it
at the moment).

## Examples
### Create the form and return it as JSON

You can simply render form schema to draw it using any frontend library - VueJS,
React, Angular or any other library/framework.

Please refer example below to see how it works:

```php
Route::get('form', function () {
    $form = new \DemoForm('post');
    $form->build();
    $form->fill(/* any data for your form */);
    
    return (new \YProjects\Forms\Renderers\JsonRenderer())->render($form);
});
```

The code above will generate JSON which all fields and their properties plus form's
data to fill it on frontend

### Handle POST request

Please read example below:

```php
Route::post('form', function(\Illuminate\Http\Request $request) {
    $form = new \DemoForm('post');
    $form->build();
    $data = $form->handle([
        // Your payload
        'title' => 'Deadpool',
        'slug' => 'deadpool'
    ]);
});
```

As result of ``handle(array $data): mixed`` method you will receive filled data,
so you can do anything you want with it

## How it works with Laravel?

For Laravel we have ``YProjects\Forms\Components\EloquentForm`` class. This class
contain some extra code that automatically saves/updates records in database,
can work with relations (for example we use it in ``Collection`` field). This class
also splits fields into two groups - first group will be executed before ``save``
method, second - after. You should divide fields by groups manually.

## Have questions?

Feel free to contact us through issues! We are waiting for your feature requests
and bug reports! 🙂
