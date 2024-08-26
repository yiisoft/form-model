<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class FileForm extends FormModel implements RulesProviderInterface
{
    private ?string $image = null;
    private ?string $photo = null;
    private ?string $video = null;

    public function getRules(): array
    {
        return [
            'image' => [new Required()],
            'photo' => [new Required(when: static fn () => false)],
            'video' => [new Required(when: static fn () => false), new Required()],
        ];
    }
}
