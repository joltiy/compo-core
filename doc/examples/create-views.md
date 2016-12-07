# Шаблоны

http://symfony.com/doc/current/book/templating.html

http://twig.sensiolabs.org/

Используется Twig шаблонизатор.

Twig - имеет различные хелперы из коробки, для работы с датами, ссылками, числами...

Много функций, упрощающих написание шаблонов. Вырезаются лишние отступы, переносы строк.

Можно добавлять свои функции. Symfony добавляет свои, например для генерации ссылок.

Шаблоны компилируются в PHP классы.

По умолчанию, при выводе данные приводятся к HTML сущностям. Типа защиты от XSS...

Вывести как есть, http://twig.sensiolabs.org/doc/filters/raw.html:

```twig
{{ my_var|raw }}
```

Есть возможность создавать макросы - шаблон-функция.

Наследование шаблонов, и перегрузка блоков.

Если, на проекте, потребуется перегрузить шаблон, то это можно сделать создав:

```
/app/Resources/CompoSampleBundle/views/list.html.twig
```

Добавим базовый шаблон, для бандла `CompoSampleBundle`

[/src/Compo/SampleBundle/Resources/views/Default/base.html.twig](/src/Compo/SampleBundle/Resources/views/Default/base.html.twig)

Где, создадим блок для заголовка и основного вывода.

Сделаем наследование:

[/src/Compo/SampleBundle/Resources/views/Default/list.html.twig](/src/Compo/SampleBundle/Resources/views/Default/list.html.twig)

