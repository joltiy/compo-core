# CRUD

Можно сгенерировать CRUD действия для Front-end (Front-office). Например, только для просмотра списка и подробного просмотра.

Если требуются действия для создания, редактирования, удаления, то добавить аргумент:

`--with-write` - Whether or not to generate create, new and delete actions

Лишнии можно будет удалить.


```
bin/console generate:doctrine:crud --no-interaction --format=yml --route-prefix=compo_sample --entity=CompoSampleBundle:Sample
```

Это будет основой для дальнейших действий.

При генерации будет ошибка.

Будет сгенерирован новый контроллер.

Будут сгенерированы новые шаблоны, для просмотра списка и подробного просмотра элемента.

Будет сгенерирован файл маршрутов.

Необходимо, маршруты из [/src/Compo/SampleBundle/Resources/config/routing/sample.yml]([/src/Compo/SampleBundle/Resources/config/routing/sample.yml]) перенести в [/src/Compo/SampleBundle/Resources/config/routing.yml] ([/src/Compo/SampleBundle/Resources/config/routing.yml])


Можно удалить [/src/Compo/SampleBundle/Resources/config/routing/]([/src/Compo/SampleBundle/Resources/config/routing/])

```
php bin/console compo:update --env=prod
```

http://engine.optipro.ru/sample/ - все элементы
http://engine.optipro.ru/sample/7/show - просмотр элемента


В шаблонах, есть примеры использования функции `path()`:

[/src/Compo/SampleBundle/Resources/views/Sample/index.html.twig]([/src/Compo/SampleBundle/Resources/views/Sample/index.html.twig])

[/src/Compo/SampleBundle/Resources/views/Sample/show.html.twig]([/src/Compo/SampleBundle/Resources/views/Sample/show.html.twig])

```twig
<a href="{{ path('compo_sample_show', { 'id': entity.id }) }}">show</a>
<a href="{{ path('compo_sample') }}">show index</a>
<a href="{{ path('compo_sample_list_by_enabled', {'enabled': 'enabled'}) }}">show enabled</a>
```