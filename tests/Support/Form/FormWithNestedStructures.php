<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\Tests\Support\Dto\Coordinates;

final class FormWithNestedStructures extends FormModel
{
    private array $array = [];
    private ?Coordinates $coordinates = null;

    public function getArray(): array
    {
        return $this->array;
    }

    public function getCoordinates(): ?Coordinates
    {
        return $this->coordinates;
    }
}