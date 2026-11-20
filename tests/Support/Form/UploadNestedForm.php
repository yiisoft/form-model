<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Psr\Http\Message\UploadedFileInterface;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class UploadNestedForm extends FormModel implements RulesProviderInterface
{
    public string $name = '';
    public ?UploadedFileInterface $avatar = null;

    public function getRules(): array
    {
        return [
            'name' => [new Required()],
            'avatar' => [new Required(when: static fn(): bool => false)],
        ];
    }
}
