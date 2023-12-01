<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class TestForm extends FormModel implements RulesProviderInterface
{
    private string $name = '';
    private string $desc = '';
    private string $site = '';
    private string $number = '';
    private ?int $count = null;
    private int $volume = 23;
    private ?string $color = null;
    private string $startTime = '14:00:23';
    private string $oldPassword = '';
    private ?int $age = 42;
    private ?string $key = 'x100';
    private ?string $avatar = null;
    public string $mainEmail = '';
    private string $partyDate = '2017-06-01T08:30';
    private string $xDate = '2017-06-01T08:30';
    private string $birthday = '1996-12-19';
    public array $color2 = [];
    private bool $blue = false;

    public function getRules(): array
    {
        return [
            'name' => [new Required(), new Length(min: 4)],
        ];
    }

    public function getPropertyLabels(): array
    {
        return [
            'desc' => 'Description',
            'site' => 'Your site',
            'number' => 'Phone',
            'count' => 'Select count',
            'color' => 'Select color',
            'age' => 'Your age',
            'mainEmail' => 'Main email',
            'partyDate' => 'Date of party',
            'xDate' => 'Date X',
            'color2' => 'Select one or more colors',
            'blue' => 'Blue color',
        ];
    }

    public function getPropertyHints(): array
    {
        return [
            'name' => 'Input your full name.',
            'site' => 'Enter your site URL.',
            'number' => 'Enter your phone.',
            'color' => 'Color of box.',
            'oldPassword' => 'Enter your old password.',
            'age' => 'Full years.',
            'mainEmail' => 'Email for notifications.',
        ];
    }

    public static function validated(): self
    {
        $form = new self();
        (new Validator())->validate($form);
        return $form;
    }
}
