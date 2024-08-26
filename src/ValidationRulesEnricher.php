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
 * @psalm-type EnrichmentType = array{inputAttributes?:array}
 */
final class ValidationRulesEnricher implements ValidationRulesEnricherInterface
{
    public function process(BaseField $field, mixed $rules): ?array
    {
        if (!is_iterable($rules)) {
            return null;
        }

        if ($field instanceof DateTimeInputField) {
            $enrichment = [];
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof Email) {
            $enrichment = [];
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
                $this->processLengthToMinMaxLength($rule, $enrichment);
                $this->processRegexToPattern($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof File) {
            $enrichment = [];
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof Number) {
            $enrichment = [];
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
                $this->processAbstractNumberToMinMax($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof Password) {
            $enrichment = [];
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
                $this->processLengthToMinMaxLength($rule, $enrichment);
                $this->processRegexToPattern($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof Range) {
            $enrichment = [];
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
                $this->processAbstractNumberToMinMax($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof Select) {
            $enrichment = [];
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof Telephone) {
            $enrichment = [];
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
                $this->processLengthToMinMaxLength($rule, $enrichment);
                $this->processRegexToPattern($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof Text) {
            $enrichment = [];
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
                $this->processLengthToMinMaxLength($rule, $enrichment);
                $this->processRegexToPattern($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof Textarea) {
            $enrichment = [];
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
                $this->processLengthToMinMaxLength($rule, $enrichment);
            }
            return $enrichment;
        }

        if ($field instanceof Url) {
            $enrichment = [];
            $processedUrl = false;
            foreach ($rules as $rule) {
                if ($this->hasWhen($rule)) {
                    continue;
                }
                $this->processRequiredToRequired($rule, $enrichment);
                $this->processLengthToMinMaxLength($rule, $enrichment);
                $processedUrl = $processedUrl || $this->processUrlToPattern($rule, $enrichment);
                if (!$processedUrl) {
                    $this->processRegexToPattern($rule, $enrichment);
                }
            }
            return $enrichment;
        }

        return null;
    }

    /**
     * @psalm-param EnrichmentType $enrichment
     */
    private function processRequiredToRequired(mixed $rule, array &$enrichment): void
    {
        if ($rule instanceof Required) {
            $enrichment['inputAttributes']['required'] = true;
        }
    }

    /**
     * @psalm-param EnrichmentType $enrichment
     */
    private function processLengthToMinMaxLength(mixed $rule, array &$enrichment): void
    {
        if ($rule instanceof Length) {
            if (null !== $min = $rule->getMin()) {
                $enrichment['inputAttributes']['minlength'] = $min;
            }
            if (null !== $max = $rule->getMax()) {
                $enrichment['inputAttributes']['maxlength'] = $max;
            }
        }
    }

    /**
     * @psalm-param EnrichmentType $enrichment
     */
    private function processRegexToPattern(mixed $rule, array &$enrichment): void
    {
        if ($rule instanceof Regex && !$rule->isNot()) {
            $enrichment['inputAttributes']['pattern'] = Html::normalizeRegexpPattern($rule->getPattern());
        }
    }

    /**
     * @psalm-param EnrichmentType $enrichment
     */
    private function processUrlToPattern(mixed $rule, array &$enrichment): bool
    {
        if ($rule instanceof UrlRule && !$rule->isIdnEnabled()) {
            $enrichment['inputAttributes']['pattern'] = Html::normalizeRegexpPattern($rule->getPattern());
            return true;
        }
        return false;
    }

    /**
     * @psalm-param EnrichmentType $enrichment
     */
    private function processAbstractNumberToMinMax(mixed $rule, array &$enrichment): void
    {
        if ($rule instanceof AbstractNumber) {
            if (null !== $min = $rule->getMin()) {
                $enrichment['inputAttributes']['min'] = $min;
            }
            if (null !== $max = $rule->getMax()) {
                $enrichment['inputAttributes']['max'] = $max;
            }
        }
    }

    private function hasWhen(mixed $rule): bool
    {
        return $rule instanceof WhenInterface && $rule->getWhen() !== null;
    }
}
