# Nested form models

Form models can be nested.

- For one-to-one relations, you can add type hint of related class name to corresponding property.
- For one-to-many relation, use
  [`Collection`](https://github.com/yiisoft/hydrator/blob/master/docs/guide/en/typecasting.md#collection) attribute from
  [hydrator](https://github.com/yiisoft/hydrator) package. The class name of related collection must be specified as
  parameter.

```php
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Type\StringType;

final class PostCategory extends FormModel
{
    public function __construct(
        #[Required]
        #[Length(max: 255)]
        private string $name = '',
        #[Collection(Post::class)]
        #[Each([new Nested(Post::class)])]
        private array $posts = [],
    ) {
    }
}

final class Post extends FormModel
{
    public function __construct(
        #[Required]
        #[Length(max: 255)]
        private string $name,
        #[StringType]
        private string $description = '',
        #[Required]
        private User $author,
    ) {
    }
}

final class User extends FormModel
{
    public function __construct(
        #[Required]
        #[Integer(min: 1)]
        private int $id,
    ) {
    }
}
```
