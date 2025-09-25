# Form hydrator

Form hydrator is a wrapper for [hydrator](https://github.com/yiisoft/hydrator) adapted to work with 
[form models](form-model.md). It allows both to hydrate and validate form model.

## Initialization

### Manual initialization

Form hydrator has two dependencies: hydrator and validator.

```php
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Validator\Validator;

$formHydrator = new FormHydrator(
    new Hydrator(
        new CompositeTypeCaster(
            new NullTypeCaster(emptyString: true),
            new PhpNativeTypeCaster(),
            new NonArrayTypeCaster(),
            new HydratorTypeCaster(),
        ),
    ),
    new Validator(),
);
```

Detailed information about configuring each of them is available in the documentation of according packages:
[Yii Hydrator](https://github.com/yiisoft/hydrator) and [Yii Validator](https://github.com/yiisoft/validator).

### Initialization using dependency injection

When using [Yii DI](https://github.com/yiisoft/di), there is no need to create form hydrator manually, it can be done
automatically with [Yii Injector](https://github.com/yiisoft/injector):

```php
use Psr\Http\Message\RequestInterface;
use Yiisoft\FormModel\FormHydrator;

final class PostController 
{
    public function edit(RequestInterface $request, FormHydrator $formHydrator): ResponseInterface
    {
        // ...
    }
}
```

You can check `config/di.php` for configuration that's used by default.

## Usage

### `populate()`

Hydrates form model from existing data.

```php
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\FormModel\FormModel;

/** 
 * @var FormHydrator $formHydrator
 * @var FormModel $formModel 
 */
$data = [
    'PostForm' => [
        'name' => 'Hello!',
        'preview' => 'Introduction post',
    ],
];
$isPopulated = $formHydrator->populate($formModel, $data);
```

To customize it further:

- Use `$map` parameter if the names in the form and data are different and to explicitly define which properties to 
fill. 
- Use `$strict` parameter to customize strict mode for filling data:
  - If `false`, fills everything that is in the data.
  - If `null`, fills data that is either defined in a map explicitly or allowed via validation rules.
  - If `true`, fills either only data defined explicitly in a map or only data allowed via validation rules but not 
  both.
- By default, form hydrator expects the data to be wrapped with form model name (class name if not customized). Use 
`$scope` to customize outer' key name or `''` (empty string) to disable it completely.

```php
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\FormModel\FormModel;

/** 
 * @var FormHydrator $formHydrator
 * @var FormModel $formModel 
 */
 
$data = [
    'title' => 'Hello!',
    'preview' => 'Introduction post',
];
$map = [
    'name' => 'title',
    'preview' => 'preview',
];
$formHydrator->populate($formModel, $data, $map, strict: true, scope: '');
```

### `validate()`

Validates already hydrated form model:

```php
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\FormModel\FormModel;

/** 
 * @var FormHydrator $formHydrator
 * @var FormModel $formModel 
 */
 
$result = $formHydrator->validate($formModel);
$errors = $result->getErrorMessagesIndexedByPath();
```

For further working with result, refer to corresponding 
[validator's guide section](https://github.com/yiisoft/validator/blob/master/docs/guide/en/result.md).

### `populateAndValidate()`

A shortcut to execute [`populate()`](#populate), then [`validate()`](#validate) consecutively.

```php
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\FormModel\FormModel;

/** 
 * @var FormHydrator $formHydrator
 * @var FormModel $formModel 
 */
 
$data = [
    'PostForm' => [
        'name' => 'Hello!',
        'preview' => 'Introduction post',
    ],
];
$isValid = $formHydrator->populateAndValidate($formModel, $data);
$result = $form->getValidationResult();
```

The parameters are the same as in [`populate()`](#populate). But, unlike [`validate()`](#validate), the method returns 
just whether the validation was successful. Validation result is still available via form's dedicated method though.

### `populateFromPost()`

A shortcut, hydrates form model from existing data similar to [`populate()`](#populate) but data is retrieved from 
request's parsed body. 

```php
use Psr\Http\Message\RequestInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\FormModel\FormModel;

/** 
 * @var FormHydrator $formHydrator
 * @var FormModel $formModel
 * @var RequestInterface $request 
 */
 
$isPopulated = $formHydrator->populateFromPost($formModel, $request);
```

### `populateFromPostAndValidate()`

A shortcut to execute [`populateFromPost()`](#populatefrompost), then [`validate()`](#validate) consecutively.

```php
use Psr\Http\Message\RequestInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\FormModel\FormModel;

/** 
 * @var FormHydrator $formHydrator
 * @var FormModel $formModel
 * @var RequestInterface $request 
 */


$isValid = $formHydrator->populateFromPostAndValidate($formModel, $request);
$result = $formModel->getValidationResult();
```

The parameters are the same as in [`populate()`](#populate). But, unlike [`validate()`](#validate), the method returns
just whether the validation was successful. Validation result is still available via form's dedicated method though.

## Hydrator extensions

This package provides some extensions for hydrator you can use:

- `NonArrayTypeCaster` - type caster ensuring that a non-array value such as string provided for property of array type 
is converted into an empty array.

To register custom type caster, see 
["Tweaking type-casting"](https://github.com/yiisoft/hydrator/blob/master/docs/guide/en/typecasting.md#tweaking-type-casting)
section in hydrator's guide.
