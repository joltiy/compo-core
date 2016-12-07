# Переводы


http://symfony.com/doc/current/book/translation.html
https://sonata-project.org/bundles/admin/master/doc/reference/translation.html

[Создание переводов](./doc/examples/create-translations.md)

У переводов  есть: локаль, domain, параметры...
domain - это типа неймспейса.
По умолчанию, если не указан domain для перевода, то используется (fallback): messages
По умолчанию, если не указана локаль для перевода, то используется локаль пользователя, а потом перечисленные fallbacks в конфиге:

```
framework:
    translator:      { fallbacks: ["en"] }
```

Перевод осуществляется в следующем порядке:
1. domain + локаль пользователя
2. domain + fallback - локаль
3. messages + локаль пользователя, если не указан domain
4. messages + fallback локаль, если не указан domain

Все переводы кешируются, в порядке подключения бандлов. Приоритет: `app/Resource/translations`

К примеру, если вывести в шаблоне:
```
{% trans %}Enabled{% endtrans %}
```

То перевод, будет искаться в `messages`.

Если, в какой-то бандл, добавить перевод для `messages`,
```
# src/Compo/AvailabilityBundle/Resources/translations/messages.ru.yml
Enabled: Включена
```

То в шаблоне, выведется соответственно: "Включена"

Если, добавить ещё один бандл, с переводом в `messages`, 
```
# src/Compo/Availability123456Bundle/Resources/translations/messages.ru.yml
Enabled: Включить запись
```

То в шаблоне, выведется: "Включить запись". То есть, будет конфликт.
Поэтому, лучше использовать translation domain.

Если сделать так, то будет приоритетно:
```
# app/Resources/translations/messages.ru.yml
Enabled: "ВКЛЮЧИТЬ!!!!"
```

Используем domain:
```
# src/Compo/AvailabilityBundle/Resources/translations/AvailabilityBundle.ru.yml
Enabled: Включена
```

```
# src/Compo/Availability123456Bundle/Resources/translations/Availability123456Bundle.ru.yml
Enabled: Включить запись
```

А domain  в шаблонах:
```
{% trans %}Enabled{% endtrans %} - ВКЛЮЧИТЬ!!!!
{% trans from "AvailabilityBundle" %}Enabled{% endtrans %} - Включена
{% trans from "Availability123456Bundle" %}Enabled{% endtrans %} - Включить запись
```

Для каждого Admin класса, мы указываем:
```
class ProductAdmin extends Admin
{
    protected $translationDomain = 'CompoProductBundle'; // Иначе messages
}
```
По умолчанию, перевод поля, осуществляется по стратегии, `sonata.admin.label.strategy.native`, если не указана иная:
https://sonata-project.org/bundles/admin/master/doc/reference/translation.html#label-strategies

Если, в опциях для поля, не указан иной: `'translation_domain' => 'AnotherDomain',`
https://sonata-project.org/bundles/admin/master/doc/reference/translation.html#overriding-the-translation-domain

Для, реализации своей логики перевода, в частности, для перевода из `messages` если не найден перевод в указанном domain, для "Id", "Name"...
Перегружен translator сервис.
https://github.com/comporu/compo-standard/blob/master/src/Compo/CoreBundle/Translation/FallbackTranslator.php

Объявлен сервис:
https://github.com/comporu/compo-standard/blob/master/src/Compo/CoreBundle/Resources/config/services.yml#L1-L2

Для извлечение и генерации переводов, использован бандл:
http://jmsyst.com/bundles/JMSTranslationBundle

Sonata, генерирует переводы для всех полей админ класса, для указанного domain.
Поэтому, был создан свой Extractor: https://github.com/comporu/compo-standard/blob/master/src/Compo/CoreBundle/Translation/AdminExtractor.php 
Пришлось полностью скопировать, из-за private метода.
И добавить проверку, на наличие перевода в "messages", что бы не добавлял в результирующий перевод.

https://github.com/comporu/compo-standard/blob/master/src/Compo/CoreBundle/Translation/AdminExtractor.php#L170-L174

Объявлен сервис:
https://github.com/comporu/compo-standard/blob/master/src/Compo/CoreBundle/Resources/config/services.yml#L8-L12

Добавлен в конфиг:
https://github.com/comporu/compo-standard/blob/master/src/Compo/CoreBundle/Resources/config/app/config.yml#L104

Так, после генерации Админ класса, и генерации переводов для админки, в переводы не будут попадать значения добавленные в "messages"
А если для бандла, domain, не добавлен перевод, то он будет проверяться в "messages".