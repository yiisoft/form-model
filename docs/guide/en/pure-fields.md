# Pure fields

To ease the creation of the fields with `\Yiisoft\FormModel\FormModelInputData` as a data source, use
corresponding helper (`\Yiisoft\FormModel\Field`):

```php
use Yiisoft\FormModel\Field;
use Yiisoft\FormModel\FormModel;

/** @var FormModel $formModel */
$field = Field::text($formModel, 'login');
```

or factory (`\Yiisoft\FormModel\FieldFactory`):

```php
use Yiisoft\FormModel\FieldFactory;
use Yiisoft\FormModel\FormModel;

/** @var FormModel $formModel */
$factory = new FieldFactory();
$factory->text($formModel, 'login');
```

If you want to customize other properties, such as label, hint, etc., use dedicated methods:

```php
use Yiisoft\Form\Field\Text;

/** @var Text $field */
$field
    ->label('Label')
    ->hint('Hint')
    ->placeholder('Placeholder')
    ->inputId('ID');
```

For more info see these guide sections:

- [form input data concept](https://github.com/yiisoft/form/blob/master/docs/guide/en/input-data.md).
- [form model input data implementation](form-model-input-data.md).

## Applying theme

To additionally apply theme, you can pass it as argument to a specific field's method (supported by both helper and
factory):

```php
use Yiisoft\FormModel\Field;
use Yiisoft\FormModel\FieldFactory;

/** @var FormModel $formModel */

// Using helper

Field::text($formModel, 'login', theme: 'my-theme');

// Using factory

$factory = new FieldFactory();
$factory->text('login', 'value', theme: 'my-theme');
```

To apply the theme for all fields, either pass it as argument in constructor (supported by factory).

```php
use Yiisoft\FormModel\FieldFactory;

$factory = new FieldFactory('my-theme');
$factory->text('name', 'value');
```

or override the theme property via class inheritance (supported by helper):

```php
use Yiisoft\Form\PureField\Field;

final class ThemedField extends Field
{
    protected const DEFAULT_THEME = 'default';
}
```

and use this class instead:

```php
/** @var FormModel $formModel */
ThemedField::text($formModel, 'login');
```

Which one to choose depends on the situation, but factory has some advantages:

- It's more convenient to use when multiple themes are used simultaneously. The static helper requires separate file /
  class per each theme.
- It can be passed and injected between classes as a dependency more explicitly.
