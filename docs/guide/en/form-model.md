# Form model

Form model is an abstraction over HTML forms. While you can use [forms](https://github.com/yiisoft/form) directly, 
it can be more convenient to define form in object-oriented style. With this approach, form fields are defined as class
properties. Besides describing form data, form model also handles presentation and validation aspects.

To define a form model, create a class extending from `Yiisoft\FormModel\FormModel`.

## Properties

Form model properties are defined as class properties.

```php
final class LoginForm extends FormModel
{
    public function __construct(
        public ?string $login = null;
        public ?string $password = null;
        public bool $rememberMe = false;
    ) {
    }
}
```

Example of working with properties individually:

```php
use Yiisoft\FormModel\FormModel;

/** @var FormModel $form */
$form = new LoginForm();
$form->login = 'john';

$form->hasProperty('login'); // true
$form->hasProperty('passwordConfirmation'); // false

echo $form->login; // "john"
// or
echo $form->getPropertyValue('login'); // "john"

echo $form->getPropertyValue('password'); // null
echo $form->getPropertyValue('passwordConfirmation'); // null
```

> Static properties are not included in form model's set of properties. On attempt to get such property's value,
> `Yiisoft\FormModel\Exception\StaticObjectPropertyException` exception will be thrown.

## Meta data

Form model's meta data includes:

- Property labels - the labels associated with each input (visible to the end user). When not set, they will be 
automatically generated using inflector from the [strings](https://github.com/yiisoft/strings) package.
- Property hints - complimentary text explaining certain details regarding each input. Optional, not shown when not set.
- Property placeholders - the values used as examples to help user fill the actual values. Optional, not shown when not
set.
- Form name - name of the form used to group all fields together when the data is submitted to the server. When not set,
the corresponding class name is used.

```php
use Yiisoft\FormModel\FormModel;

final class LoginForm extends FormModel
{   
    public function getPropertyLabels(): array
    {
        return [
            'login' => 'Login',
            'password' => 'Password',
            'rememberMe' => 'Remember me',
        ];
    }
         
    public function getPropertyHints(): array
    {
        return [
            'login' => 'ID or e-mail',
            'password' => 'Case-sensitive',
        ];
    }   

    public function getPropertyPlaceholders(): array
    {
        return [
            'login' => 'john',
            'password' => '123456',
        ];
    }
    
    public function getFormName(): string 
    {
        return 'MyLoginForm';
    }
}
```

### Translating meta data

If your application uses only one language, you can provide translations right in the mappings:

```php
use Yiisoft\FormModel\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;

final class LoginForm extends FormModel implements AttributeTranslatorInterface
{
    // ...
    
    public function getPropertyLabels(): array
    {
        return [
            'login' => 'Логин',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }
         
    public function getPropertyHints(): array
    {
        return [
            'login' => 'ID или e-mail',
            'password' => 'Зависит от регистра',
        ];
    }   

    public function getPropertyPlaceholders(): array
    {
        return [
            'login' => 'джон',
            'password' => '123456',
        ];
    }
    
    // ...
}
```

For multiple languages, you can inject translator as a dependency and perform translation in the methods responsible 
for getting single meta item:

```php
use Yiisoft\FormModel\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;

final class LoginForm extends FormModel implements AttributeTranslatorInterface
{
    // ...

    public function __construct(private TranslatorInterface $translator) 
    {
    }
    
    public function getPropertyLabel(string $property): string
    {
        $label = parent::getPropertyLabel($property);
        
        return $this->translator->translate($label);
    }
         
    public function getPropertyHint(string $property): string
    {
        $hint = parent::getPropertyHint($property);
        
        return $this->translator->translate($hint);
    }

    public function getPropertyPlaceholder(string $propert): string
    {
        $placeholder = parent::getPropertyPlaceholder($property);
        
        return $this->translator->translate($placeholder);
    }
    
    // ...
}
```

## Validation rules

Validation rules can be provided in the 
[interface method implementation](https://github.com/yiisoft/validator/blob/master/docs/guide/en/using-validator.md#using-interface-method-implementation):

```php
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class LoginForm extends FormModel implements RulesProviderInterface
{    
    public function getRules(): array
    {
        return [
            'login' => $this->getLoginRules(),
            'password' => $this->getPasswordRules(),
        ];
    }

    private function getLoginRules(): array
    {
        return [
            new Required(),
            new Length(min: 4, max: 40, lessThanMinMessage: 'Is too short.', greaterThanMaxMessage: 'Is too long.'),
            new Email(),
        ];
    }

    private function getPasswordRules(): array
    {
        return [
            new Required(),
            new Length(min: 8, lessThanMinMessage: 'Is too short.'),
        ];
    }
}
```

or via 
[PHP attributes](https://github.com/yiisoft/validator/blob/master/docs/guide/en/configuring-rules-via-php-attributes.md).

```php
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class LoginForm extends FormModel
{
    #[Required]
    #[Length(min: 4, max: 40, lessThanMinMessage: 'Is too short.', greaterThanMaxMessage: 'Is too long.')]
    #[Email]
    private ?string $login = null;
    #[Required]
    #[Length(min: 8, lessThanMinMessage: 'Is too short.')]
    private ?string $password = null;    
    
    // ...    
}
```

### `Safe` rule

This package also provides `Safe` rule that marks a model property as safe for filling from user input.

```php
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\Safe;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class LoginForm extends FormModel
{    
    // ...

    #[Safe]
    private bool $rememberMe = false;
    
    // ...    
}
```
