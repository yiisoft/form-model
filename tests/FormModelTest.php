<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\FormModel\Exception\PropertyNotSupportNestedValuesException;
use Yiisoft\FormModel\Exception\UndefinedObjectPropertyException;
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\FormModel\Safe;
use Yiisoft\FormModel\Tests\Support\Dto\Coordinates;
use Yiisoft\FormModel\Tests\Support\Form\CustomFormNameForm;
use Yiisoft\FormModel\Tests\Support\Form\DefaultFormNameForm;
use Yiisoft\FormModel\Tests\Support\Form\FormWithNestedProperty;
use Yiisoft\FormModel\Tests\Support\Form\FormWithNestedStructures;
use Yiisoft\FormModel\Tests\Support\Form\LoginForm;
use Yiisoft\FormModel\Tests\Support\Form\NestedForm;
use Yiisoft\FormModel\Tests\Support\Form\NestedMixedForm\NestedMixedForm;
use Yiisoft\FormModel\Tests\Support\Form\NestedRuleForm\MainForm;
use Yiisoft\FormModel\Tests\Support\StubInputField;
use Yiisoft\FormModel\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Widget\WidgetFactory;

require __DIR__ . '/Support/Form/NonNamespacedForm.php';

final class FormModelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        WidgetFactory::initialize(new SimpleContainer());
    }

    public function testAnonymousFormName(): void
    {
        $form = new class () extends FormModel {
        };
        $this->assertSame('', $form->getFormName());
    }

    public function testCustomFormName(): void
    {
        $form = new CustomFormNameForm();
        $this->assertSame('my-best-form-name', $form->getFormName());
    }

    public function testDefaultFormName(): void
    {
        $form = new DefaultFormNameForm();
        $this->assertSame('DefaultFormNameForm', $form->getFormName());
    }

    public function testArrayValue(): void
    {
        $expected = <<<'HTML'
        <div>
        <label for="nestedform-letters-0">Letters</label>
        <input type="text" id="nestedform-letters-0" name="NestedForm[letters][0]" value="A">
        </div>
        HTML;

        $result = StubInputField::widget()
            ->inputData(new FormModelInputData(new NestedForm(), 'letters[0]'))
            ->render();

        $this->assertSame($expected, $result);
    }

    public function testNonExistArrayValue(): void
    {
        $widget = StubInputField::widget()->inputData(new FormModelInputData(new NestedForm(), 'letters[1]'));

        $result = $widget->render();

        $this->assertSame(
            <<<HTML
            <div>
            <label for="nestedform-letters-1">Letters</label>
            <input type="text" id="nestedform-letters-1" name="NestedForm[letters][1]" value>
            </div>
            HTML,
            $result
        );
    }

    public function testArrayValueIntoObject(): void
    {
        $expected = <<<'HTML'
        <div>
        <label for="nestedform-object-numbers-1">Object</label>
        <input type="text" id="nestedform-object-numbers-1" name="NestedForm[object][numbers][1]" value="42">
        </div>
        HTML;

        $result = StubInputField::widget()
            ->inputData(new FormModelInputData(new NestedForm(), 'object[numbers][1]'))
            ->render();

        $this->assertSame($expected, $result);
    }

    public function testGetAttributeHint(): void
    {
        $form = new LoginForm();

        $this->assertSame('Write your id or email.', $form->getPropertyHint('login'));
        $this->assertSame('Write your password.', $form->getPropertyHint('password'));
        $this->assertEmpty($form->getPropertyHint('noExist'));
    }

    public function testGetAttributeLabel(): void
    {
        $form = new LoginForm();

        $this->assertSame('Login:', $form->getPropertyLabel('login'));
        $this->assertSame('Testme', $form->getPropertyLabel('testme'));
    }

    public function testGetAttributesLabels(): void
    {
        $form = new LoginForm();

        $expected = [
            'login' => 'Login:',
            'password' => 'Password:',
            'rememberMe' => 'remember Me:',
        ];

        $this->assertSame($expected, $form->getPropertyLabels());
    }

    public function testNestedPropertyOnNull(): void
    {
        $form = new FormWithNestedProperty();

        $this->assertFalse($form->hasProperty('id.profile'));
        $this->assertNull($form->getPropertyValue('id.profile'));
    }

    public function testNestedPropertyOnArray(): void
    {
        $form = new FormWithNestedProperty();

        $this->assertFalse($form->hasProperty('meta.profile'));
        $this->assertNull($form->getPropertyValue('meta.profile'));
    }

    public function testNestedPropertyOnString(): void
    {
        $form = new FormWithNestedProperty();

        $this->assertFalse($form->hasProperty('key.profile'));

        $this->expectException(PropertyNotSupportNestedValuesException::class);
        $this->expectExceptionMessage(
            'Property "' . FormWithNestedProperty::class . '::key" not support nested values.'
        );
        $form->getPropertyValue('key.profile');
    }

    public function testNestedPropertyOnObject(): void
    {
        $form = new FormWithNestedProperty();

        $this->assertFalse($form->hasProperty('coordinates.profile'));

        $this->expectException(UndefinedObjectPropertyException::class);
        $this->expectExceptionMessage(
            'Undefined object property: "' . FormWithNestedProperty::class . '::coordinates::profile".'
        );
        $form->getPropertyValue('coordinates.profile');
    }

    public function testGetNestedAttributeHint(): void
    {
        $form = new FormWithNestedProperty();

        $this->assertSame('Write your id or email.', $form->getPropertyHint('user.login'));
    }

    public function testGetNestedAttributeLabel(): void
    {
        $form = new FormWithNestedProperty();

        $this->assertSame('Login:', $form->getPropertyLabel('user.login'));
    }

    public function testGetNestedAttributePlaceHolder(): void
    {
        $form = new FormWithNestedProperty();

        $this->assertSame('Type Username or Email.', $form->getPropertyPlaceholder('user.login'));
    }

    public function testGetAttributePlaceHolder(): void
    {
        $form = new LoginForm();

        $this->assertSame('Type Username or Email.', $form->getPropertyPlaceholder('login'));
        $this->assertSame('Type Password.', $form->getPropertyPlaceholder('password'));
        $this->assertEmpty($form->getPropertyPlaceholder('noExist'));
    }

    public function testGetAttributeValue(): void
    {
        $form = new LoginForm();

        $form->login('admin');
        $this->assertSame('admin', $form->getPropertyValue('login'));

        $form->password('123456');
        $this->assertSame('123456', $form->getPropertyValue('password'));

        $form->rememberMe(true);
        $this->assertSame(true, $form->getPropertyValue('rememberMe'));
    }

    public function testGetAttributeValueException(): void
    {
        $form = new LoginForm();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined object property: "Yiisoft\FormModel\Tests\Support\Form\LoginForm::noExist".'
        );
        $form->getPropertyValue('noExist');
    }

    public function testGetAttributeValueWithNestedAttribute(): void
    {
        $form = new FormWithNestedProperty();

        $form->setUserLogin('admin');
        $this->assertSame('admin', $form->getPropertyValue('user.login'));
    }

    public function testHasAttribute(): void
    {
        $form = new LoginForm();

        $this->assertTrue($form->hasProperty('login'));
        $this->assertTrue($form->hasProperty('password'));
        $this->assertTrue($form->hasProperty('rememberMe'));
        $this->assertFalse($form->hasProperty('noExist'));
        $this->assertFalse($form->hasProperty('extraField'));
    }

    public function testHasNestedAttribute(): void
    {
        $form = new FormWithNestedProperty();

        $this->assertTrue($form->hasProperty('user.login'));
        $this->assertTrue($form->hasProperty('user.password'));
        $this->assertTrue($form->hasProperty('user.rememberMe'));
        $this->assertFalse($form->hasProperty('noexist'));
    }

    public function testHasNestedAttributeException(): void
    {
        $form = new FormWithNestedProperty();

        $this->assertFalse($form->hasProperty('user.noexist'));
    }

    public function testLoad(): void
    {
        $form = new LoginForm();

        $this->assertNull($form->getLogin());
        $this->assertNull($form->getPassword());
        $this->assertFalse($form->getRememberMe());

        $data = [
            'LoginForm' => [
                'login' => 'admin',
                'password' => '123456',
                'rememberMe' => true,
                'noExist' => 'noExist',
            ],
        ];

        $this->assertTrue(TestHelper::createFormHydrator()->populate($form, $data));

        $this->assertSame('admin', $form->getLogin());
        $this->assertSame('123456', $form->getPassword());
        $this->assertSame(true, $form->getRememberMe());
    }

    public function testLoadFailedForm(): void
    {
        $form1 = new LoginForm();
        $form2 = new class () extends FormModel {
        };

        $data1 = [
            'LoginForm2' => [
                'login' => 'admin',
                'password' => '123456',
                'rememberMe' => true,
                'noExist' => 'noExist',
            ],
        ];
        $data2 = [];

        $hydrator = TestHelper::createFormHydrator();

        $this->assertFalse($hydrator->populate($form1, $data1));
        $this->assertFalse($hydrator->populate($form1, $data2));

        $this->assertTrue($hydrator->populate($form2, $data1));
        $this->assertTrue($hydrator->populate($form2, $data2));
    }

    public function testLoadWithEmptyScope(): void
    {
        $form = new class () extends FormModel {
            #[Safe]
            private int $int = 1;
            #[Safe]
            private string $string = 'string';
            #[Safe]
            private float $float = 3.14;
            #[Safe]
            private bool $bool = true;
        };
        TestHelper::createFormHydrator()->populate(
            $form,
            [
                'int' => '2',
                'float' => '3.15',
                'bool' => '0',
                'string' => 555,
            ],
            scope: '',
        );

        $this->assertSame(2, $form->getPropertyValue('int'));
        $this->assertSame(3.15, $form->getPropertyValue('float'));
        $this->assertSame(false, $form->getPropertyValue('bool'));
        $this->assertSame('555', $form->getPropertyValue('string'));
    }

    public function testLoadWithNestedProperty(): void
    {
        $form = new FormWithNestedProperty();

        $data = [
            'FormWithNestedProperty' => [
                'user.login' => 'admin',
            ],
        ];

        $this->assertTrue(TestHelper::createFormHydrator()->populate($form, $data));
        $this->assertSame('admin', $form->getUserLogin());
    }

    public function testLoadObjectData(): void
    {
        $form = new LoginForm();

        $result = TestHelper::createFormHydrator()->populate($form, new stdClass());

        $this->assertFalse($result);
    }

    public function testLoadNullData(): void
    {
        $form = new LoginForm();

        $result = TestHelper::createFormHydrator()->populate($form, null);

        $this->assertFalse($result);
    }

    public function testLoadNonArrayScopedData(): void
    {
        $form = new LoginForm();

        $result = TestHelper::createFormHydrator()->populate($form, ['LoginForm' => null]);

        $this->assertFalse($result);
    }

    public function testNonNamespacedFormName(): void
    {
        $form = new \NonNamespacedForm();
        $this->assertSame('NonNamespacedForm', $form->getFormName());
    }

    public function testPublicAttributes(): void
    {
        $form = new class () extends FormModel {
            #[Safe]
            public int $int = 1;
        };

        // check row data value.
        TestHelper::createFormHydrator()->populate($form, ['int' => '2']);
        $this->assertSame(2, $form->getPropertyValue('int'));
    }

    public function testFormWithNestedStructures(): void
    {
        $form = new FormWithNestedStructures();

        TestHelper::createFormHydrator()->populate($form, [
            'FormWithNestedStructures' => [
                'array' => ['a' => 'b', 'nested' => ['c' => 'd']],
                'coordinates' => ['latitude' => '12.24', 'longitude' => '56.78'],
            ],
        ]);

        $this->assertSame(['a' => 'b', 'nested' => ['c' => 'd']], $form->getPropertyValue('array'));

        $coordinates = $form->getPropertyValue('coordinates');
        $this->assertInstanceOf(Coordinates::class, $coordinates);
        $this->assertSame('12.24', $coordinates->getLatitude());
        $this->assertSame('56.78', $coordinates->getLongitude());
    }

    /**
     * @see https://github.com/yiisoft/form-model/issues/7
     */
    public function testNestedRuleWithFormModels(): void
    {
        $form = new MainForm();

        TestHelper::createFormHydrator()->populate(
            $form,
            [
                'value' => 'main-form',
                'firstLevelForm.secondLevelForm.float' => '-7.1',
            ],
            scope: ''
        );

        $result = $form->getValidationResult();

        $this->assertFalse($result->isValid());
        $this->assertSame(
            [
                'firstLevelForm.secondLevelForm.float' => ['Value must be no less than 0.']
            ],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    /**
     * @see https://github.com/yiisoft/form-model/issues/6
     */
    public function testNestedRuleInForm(): void
    {
        $form = new NestedMixedForm();

        TestHelper::createFormHydrator()->populate(
            $form,
            [
                'body' => [
                    'shipping' => [
                        'phone' => '+790012345678'
                    ],
                ],
            ],
            scope: ''
        );

        $result = $form->getValidationResult();

        $this->assertFalse($result->isValid());
        $this->assertSame(
            [
                'body.shipping.phone' => ['Invalid phone.'],
            ],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public static function dataMapping(): array
    {
        return [
            'without-rules' => [
                ['a' => null, 'b' => null, 'c' => null],
                ['a' => 1, 'b' => 2, 'c' => 3],
                [],
                null,
                null,
            ],
            'map-null-strict-null' => [
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => new Safe(), 'b' => new Safe(), 'c' => new Safe(),],
                null,
                null,
            ],
            'map-array-strict-null' => [
                ['a' => 1, 'b' => 2, 'c' => 4],
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
                ['a' => new Safe(), 'b' => new Safe(), 'c' => new Safe()],
                ['c' => 'd'],
                null,
            ],
            'map-null-strict-true' => [
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => new Safe(), 'b' => new Safe(), 'c' => new Safe()],
                null,
                true,
            ],
            'map-array-strict-true' => [
                ['a' => null, 'b' => null, 'c' => 4],
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
                ['a' => new Safe()],
                ['c' => 'd'],
                true,
            ],
            'map-null-strict-false' => [
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => new Safe()],
                null,
                false,
            ],
            'map-array-strict-false' => [
                ['a' => 1, 'b' => 2, 'c' => 4],
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
                ['a' => new Safe()],
                ['c' => 'd'],
                false,
            ],
        ];
    }

    #[DataProvider('dataMapping')]
    public function testMapping(array $expected, array $data, array $rules, ?array $map, ?bool $strict): void
    {
        $form = new class ($rules) extends FormModel implements RulesProviderInterface {
            public ?int $a = null;
            public ?int $b = null;
            public ?int $c = null;

            public function __construct(
                private array $rules,
            ) {
            }

            public function getRules(): array
            {
                return $this->rules;
            }
        };

        TestHelper::createFormHydrator()->populate(
            $form,
            $data,
            $map,
            $strict,
            '',
        );

        $this->assertSame($expected, [
            'a' => $form->a,
            'b' => $form->b,
            'c' => $form->c,
        ]);
    }
}
