<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px" alt="Yii">
    </a>
    <h1 align="center">Yii Form Model</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/form-model/v)](https://packagist.org/packages/yiisoft/form-model)
[![Total Downloads](https://poser.pugx.org/yiisoft/form-model/downloads)](https://packagist.org/packages/yiisoft/form-model)
[![Build status](https://github.com/yiisoft/form-model/actions/workflows/build.yml/badge.svg)](https://github.com/yiisoft/form-model/actions/workflows/build.yml)
[![Code Coverage](https://codecov.io/gh/yiisoft/form-model/branch/master/graph/badge.svg)](https://codecov.io/gh/yiisoft/form-model)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fform-model%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/form-model/master)
[![static analysis](https://github.com/yiisoft/form-model/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/form-model/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/form-model/coverage.svg)](https://shepherd.dev/github/yiisoft/form-model)
[![psalm-level](https://shepherd.dev/github/yiisoft/form-model/level.svg)](https://shepherd.dev/github/yiisoft/form-model)

The package provides a base for form models and helps to fill them with data, validate them and display them.

## Requirements

- PHP 8.1 or higher.

## Installation

The package could be installed with [Composer](https://getcomposer.org):

```shell
composer require yiisoft/form-model
```

## General usage

Define a [form model](docs/guide/en/form-model.md):

```php
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\Safe;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class LoginForm extends FormModel
{
    #[Label('Your login')]
    #[Required]
    #[Length(min: 4, max: 40, skipOnEmpty: true)]
    #[Email(skipOnEmpty: true)]
    private ?string $login = null;

    #[Label('Your password')]
    #[Required]
    #[Length(min: 8, skipOnEmpty: true)]
    private ?string $password = null;

    #[Label('Remember me for 1 week')]
    #[Safe]
    private bool $rememberMe = false;
}
```

Fill it with data and validate using [form hydrator](docs/guide/en/form-hydrator.md):

```php
use Psr\Http\Message\RequestInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\FormModel\FormModel;

final class AuthController 
{
    public function login(RequestInterface $request, FormHydrator $formHydrator): ResponseInterface
    {
        $formModel = new LoginForm();
        $errors = [];
        if ($formHydrator->populateFromPostAndValidate($formModel, $request)) {
            $errors = $formModel->getValidationResult()->getErrorMessagesIndexedByProperty();
        }
        
        // You can pass $formModel and $errors to the view now.
    }
}
```

Display it using [fields](docs/guide/en/displaying-fields.md) in the view:

```php
use Yiisoft\FormModel\Field;
use Yiisoft\FormModel\FormModel;

if (!empty($errors)) {
    foreach ($errors as $property => $errorMessage) {
        // Display an error message.
        <p><?= Html::encode($errorMessage) ?></p>
    }
}

// Display a field.

/** @var FormModel $formModel */
echo Field::text($formModel, 'login');

// ...
```

## Documentation

- [Guide](docs/guide/en/README.md)
- [Internals](docs/internals.md)

If you need help or have a question, the [Yii Forum](https://forum.yiiframework.com/c/yii-3-0/63) is a good place for
that. You may also check out other [Yii Community Resources](https://www.yiiframework.com/community).

## License

The Yii Form Model is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
