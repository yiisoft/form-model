<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\FormHydrator\NestedObject;

use PHPUnit\Framework\TestCase;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Validator\Validator;

final class FormHydratorNestedObjectTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new FormHydrator(
            new Hydrator(),
            new Validator(),
        );

        $form = new Form();
        $hydrator->populate($form, [
            'Form' => [
                'name' => 'Test',
                'item' => [
                    'color' => 'red',
                    'size' => 'L',
                ],
            ],
        ]);

        $this->assertSame('Test', $form->name);
        $this->assertSame('', $form->item?->color);
        $this->assertSame('L', $form->item?->size);
    }
}
