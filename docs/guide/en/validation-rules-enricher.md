# Validation rules enricher

The package has its
[own implementation](https://github.com/yiisoft/form-model/blob/master/src/ValidationRulesEnricher.php) of validation
rules enricher. It's based on [Validator](https://github.com/yiisoft/validator) rules and helps to automatically fill
HTML attributes of the input based on the rules' configuration.

Here is the table of what is supported:

| Form field      | Validator rule / rule property | Input attribute |
|-----------------|--------------------------------|-----------------|
| [Date]          | `Required`                     | `required`      |
| [DateTimeLocal] | `Required`                     | `required`      |
| [Email]         | `Required`                     | `required`      |
| [Email]         | `Length::$min`                 | `minlength`     |
| [Email]         | `Length::$max`                 | `maxlength`     |
| [Email]         | `Regex::$pattern`              | `pattern`       |
| [File]          | `Required`                     | `required`      |
| [Number]        | `Required`                     | `required`      |
| [Number]        | `Number::$min`                 | `min`           |
| [Number]        | `Number::$max`                 | `max`           |
| [Number]        | `Integer::$min`                | `min`           |
| [Number]        | `Integer::$max`                | `max`           |
| [Password]      | `Required`                     | `required`      |
| [Password]      | `Length::$min`                 | `minlength`     |
| [Password]      | `Length::$max`                 | `maxlength`     |
| [Password]      | `Regex::$pattern`              | `pattern`       |
| [Range]         | `Required`                     | `required`      |
| [Range]         | `Number::$min`                 | `min`           |
| [Range]         | `Number::$max`                 | `max`           |
| [Range]         | `Integer::$min`                | `min`           |
| [Range]         | `Integer::$max`                | `max`           |
| [Telephone]     | `Required`                     | `required`      |
| [Telephone]     | `Length::$min`                 | `minlength`     |
| [Telephone]     | `Length::$max`                 | `maxlength`     |
| [Telephone]     | `Regex::$pattern`              | `pattern`       |
| [Text]          | `Required`                     | `required`      |
| [Text]          | `Length::$min`                 | `minlength`     |
| [Text]          | `Length::$max`                 | `maxlength`     |
| [Text]          | `Regex::$pattern`              | `pattern`       |
| [Textarea]      | `Required`                     | `required`      |
| [Textarea]      | `Length::$min`                 | `minlength`     |
| [Textarea]      | `Length::$max`                 | `maxlength`     |
| [Textarea]      | `Regex::$pattern`              | `pattern`       |
| [Select]        | `Required`                     | `required`      |
| [Time]          | `Required`                     | `required`      |
| [Url]           | `Required`                     | `required`      |
| [Url]           | `Length::$min`                 | `minlength`     |
| [Url]           | `Length::$max`                 | `maxlength`     |
| [Url]           | `Regex::$pattern`              | `pattern`       |
| [Url]           | `Url::$pattern`                | `pattern`       |

[Date]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/date.md      
[DateTimeLocal]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/date-time-local.md
[Email]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/email.md
[File]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/file.md
[Number]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/number.md
[Password]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/password.md
[Range]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/range.md
[Select]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/select.md
[Telephone]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/telephone.md
[Text]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/text.md
[Textarea]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/textarea.md
[Time]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/time.md
[Url]: https://github.com/yiisoft/form/blob/master/docs/guide/en/fields/url.md
