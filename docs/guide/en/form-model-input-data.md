# Form model input data

An implementation of [input data](https://github.com/yiisoft/form/blob/master/docs/guide/en/input-data.md) from 
[Yii Form](https://github.com/yiisoft/form) package designed to work with [form model](form-model.md).

- Validation rules are parsed and normalized using 
[Yii Validator](https://github.com/yiisoft/validator/blob/master/docs/guide/en/using-validator.md#providing-rules-via-dedicated-object).
- Meta data is obtained via [form model's dedicated methods](form-model.md#meta-data).

## Usage with fields

To add form model input data to a field, you only need to pass form model itself and the property name:

```php
use Yiisoft\Form\Field\Text;
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\FormModelInputData;

/** @var FormModel $formModel */
$inputData = new FormModelInputData($formModel,'login');    
$result = Text::widget()->inputData($inputData)->render();
```
