# Админка

https://sonata-project.org/bundles/admin/master/doc/index.html

Админка генерируется из описания модели.

```
php bin/console sonata:admin:generate Compo/SampleBundle/Entity/Sample
```

Необходимо отметить, что бы был сгенерирован класс контроллера для админки, потом пригодится.
```
Do you want to generate a controller [no]? yes
```


Или
```
php bin/console sonata:admin:generate --no-interaction --controller=SampleAdminController Compo/SampleBundle/Entity/Sample
```

Сгеннерированао два файла, и обновлён один.

```
The admin class "Compo\SampleBundle\Admin\SampleAdmin" has been generated under the file "/src/Compo/SampleBundle/Admin/SampleAdmin.php".

The controller class "Compo\SampleBundle\Controller\SampleAdminController" has been generated under the file "/src/Compo/SampleBundle/Controller/SampleAdminController.php".

The service "compo_sample.admin.sample" has been appended to the file "/src/Compo/SampleBundle/Resources/config/services.yml".
```

[/src/Compo/SampleBundle/Resources/config/services.yml](/src/Compo/SampleBundle/Resources/config/services.yml)

Добавлен сервис админки.

Можем добавить вручную, в меню админки

[/src/Compo/CoreBundle/Resources/config/app/sonata/sonata_admin.yml](/src/Compo/CoreBundle/Resources/config/app/sonata/sonata_admin.yml)

Например, в `sonata.admin.group.content` добавим имя сервиса `compo_sample.admin.sample`


По умолчанию, для каждой админки, включён аудит, история действия. Который можно отключить.

Поэтому, необходимо сделать ещё одну миграцию, для создания таблицы для аудита.

```
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Класс админки

[/src/Compo/SampleBundle/Admin/SampleAdmin.php](/src/Compo/SampleBundle/Admin/SampleAdmin.php)

После генерации, там будут добавлены все поля. Требуется убрать лишнии, настроить отображение текущих. Например, уберём поле `id` в форме редактирования.