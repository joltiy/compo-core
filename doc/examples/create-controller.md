# Контроллер

http://symfony.com/doc/current/book/controller.html

http://symfony.com/doc/current/book/routing.html

Ранее, при генерации бандла, был создан чистый контроллер, с одним действием для примера. [Создание бандла](/doc/examples/create-bundle.md)

Создана модель Sample. [Создание модели](/doc/examples/create-entity.md)

Добавим два новых действия, для наполнения и выборки.

[/src/Compo/SampleBundle/Controller/DefaultController.php](/src/Compo/SampleBundle/Controller/DefaultController.php)

Для этого, добавим сами действия в контроллер. И объявим их в конфиге маршрутизации.

[/src/Compo/SampleBundle/Resources/config/routing.yml](/src/Compo/SampleBundle/Resources/config/routing.yml)

После добавления новых действий, изменения маршрутов, необходимо обновлять маршрутизацию в Sonata.

```
php bin/console sonata:page:update-core-routes --site=all
php bin/console sonata:page:create-snapshots --site=all
```

Или

```
php bin/console compo:core:update
```

http://engine.optipro.ru/sample/create/random - создание случайного кол-ва элементов.
http://engine.optipro.ru/sample/list - вывод всех элементов.


Контроллер, набор каких-то связаных по смыслу действий. Так, один контроллер может создержать несколько действий для работы с каким либо модулем/сущностью (create/read/uodate/delete...)

Маршрутизация - все пути в конфигах, это позволит их перегружать, генеририровать ссылки.

Генерировать документацию. Валидация аргументов, значения по умолчанию, тип запроса GET/POST...


http://engine.optipro.ru/sample/list/all - все элементы
http://engine.optipro.ru/sample/list/enabled - только включённые
