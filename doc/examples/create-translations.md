# Генерация локализации

После генерации admin-класса, необходимо указать **$translationDomain**

```php
class ProductAdmin extends Admin
{
// ...
    protected $translationDomain = 'CompoProductBundle';
// ...
}
```

Создать директорию для переводов:

```
/src/Compo/ProductBundle/Resources/translations
```

Сгенерировать файл перевода:

```
php bin/console translation:extract ru --config=app --output-format=yml --bundle=CompoProductBundle --domain=CompoProductBundle
```

Будет создан или обновлён файл локализации. Который необходимо перевести.
[/src/Compo/ProductBundle/Resources/translations/CompoProductBundle.ru.yml](/src/Compo/ProductBundle/Resources/translations/CompoProductBundle.ru.yml)


В перевод, поподают те поля, которые отсутствуют в:
[/src/Compo/CoreBundle/Resources/translations/messages.ru.yml](/src/Compo/CoreBundle/Resources/translations/messages.ru.yml)


Можно сгенерировать/обновить файлы переводов, для всех Compo бандлов, у которых указан **$translationDomain**:

```
php bin/console compo:translation
```

## Файл переводов по умолчанию

[/src/Compo/CoreBundle/Resources/translations/messages.ru.yml](/src/Compo/CoreBundle/Resources/translations/messages.ru.yml)


## Формат файла первода YML

Если перевод состоит из двух и более слов, то заключить в одинарные кавычки.

```yml
Action: Действия
Availability: Наличие
Bonus: Бонус
Code1c: 'Код 1C'
```

### Ссылки

- [Symfony, Переводы](/doc/symfony/translations.md)
- http://symfony.com/doc/current/book/translation.html
- https://sonata-project.org/bundles/admin/master/doc/reference/translation.html
- http://jmsyst.com/bundles/JMSTranslationBundle