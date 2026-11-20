<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Psr\Http\Message\UploadedFileInterface;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class UploadForm extends FormModel implements RulesProviderInterface
{
    public string $title = '';
    public ?UploadedFileInterface $picture = null;
    public ?UploadedFileInterface $attachment = null;
    public array $gallery = [];
    public UploadNestedForm $profile;

    public function __construct()
    {
        $this->profile = new UploadNestedForm();
    }

    public function getRules(): array
    {
        return [
            'title' => [new Required()],
            'picture' => [new Required()],
            'attachment' => [new Required(when: static fn(): bool => false)],
            'gallery' => [new Each(new Required())],
            'profile' => [new Nested()],
        ];
    }
}
