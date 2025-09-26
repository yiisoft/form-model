<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Input\Http\Attribute\Parameter\UploadedFiles;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class UploadedFileForm extends FormModel implements RulesProviderInterface
{
    #[UploadedFiles('UploadedFileForm.file')]
    private ?UploadedFile $file = null;

    public function getRules(): array
    {
        return [
            'file' => [new Required()],
        ];
    }
}
