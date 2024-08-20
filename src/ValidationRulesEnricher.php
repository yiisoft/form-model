<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use Yiisoft\Form\Field\Base\BaseField;
use Yiisoft\Form\Field\Base\DateTimeInputField;
use Yiisoft\Form\Field\Email;
use Yiisoft\Form\Field\File;
use Yiisoft\Form\Field\Number;
use Yiisoft\Form\Field\Password;
use Yiisoft\Form\Field\Range;
use Yiisoft\Form\Field\Select;
use Yiisoft\Form\Field\Telephone;
use Yiisoft\Form\Field\Text;
use Yiisoft\Form\Field\Textarea;
use Yiisoft\Form\Field\Url;
use Yiisoft\Form\ValidationRulesEnricherInterface;
use Yiisoft\Html\Html;
use Yiisoft\Validator\Rule\AbstractNumber;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url as UrlRule;
use Yiisoft\Validator\WhenInterface;

use function is_iterable;

/**
 * @psalm-suppress MixedArrayAssignment
 */
final class ValidationRulesEnricher implements ValidationRulesEnricherInterface
{
    public function process(BaseField $field, mixed $rules): ?array
    {
        if (!is_iterable($rules)) {
            return null;
        }

        if (
            $field instanceof DateTimeInputField ||
            $field instanceof File ||
            $field instanceof Select
        ) {
            $enrichment = [];
            foreach ($rules as $rule) {
                $this->processRequired($rule, $enrichment);
            }
            return $enrichment;
        }

        if (
            $field instanceof Number ||
            $field instanceof Range
        ) {
            $enrichment = [];
            foreach ($rules as $rule) {
                $this->processNumber($rule, $enrichment);
            }
            return $enrichment;
        }

        if (
            $field instanceof Email ||
            $field instanceof Password ||
            $field instanceof Telephone ||
            $field instanceof Text ||
            $field instanceof Textarea
        ) {
            $enrichment = [];
            foreach ($rules as $rule) {
                $this->processText($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof Url) {
            $enrichment = [];
            foreach ($rules as $rule) {
                $this->processUrl($rule, $enrichment);
            }
            return $enrichment;
        }

        return null;
    }

    private function processRequired(mixed $rule, array &$enrichment): void
    {
        if ($rule instanceof Required && $rule->getWhen() === null) {
            $enrichment['inputAttributes']['required'] = true;
        }
    }

    private function processText(mixed $rule, array &$enrichment): void
    {
        if ($rule instanceof WhenInterface && $rule->getWhen() !== null) {
            return;
        }

        $this->processRequired($rule, $enrichment);

        if ($rule instanceof Length) {
            if (null !== $min = $rule->getMin()) {
                $enrichment['inputAttributes']['minlength'] = $min;
            }
            if (null !== $max = $rule->getMax()) {
                $enrichment['inputAttributes']['maxlength'] = $max;
            }
        }

        if ($rule instanceof Regex && !$rule->isNot()) {
            $enrichment['inputAttributes']['pattern'] = Html::normalizeRegexpPattern(
                $rule->getPattern(),
            );
        }
    }

    private function processNumber(mixed $rule, array &$enrichment): void
    {
        if ($rule instanceof WhenInterface && $rule->getWhen() !== null) {
            return;
        }

        $this->processRequired($rule, $enrichment);

        if ($rule instanceof AbstractNumber) {
            if (null !== $min = $rule->getMin()) {
                $enrichment['inputAttributes']['min'] = $min;
            }
            if (null !== $max = $rule->getMax()) {
                $enrichment['inputAttributes']['max'] = $max;
            }
        }
    }

    private function processUrl(mixed $rule, array &$enrichment): void
    {
        $this->processText($rule, $enrichment);

        if ($rule instanceof UrlRule && !$rule->isIdnEnabled()) {
            $enrichment['inputAttributes']['pattern'] = Html::normalizeRegexpPattern($rule->getPattern());
        }
    }
}
