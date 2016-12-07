# Symfony. Создание бандла

http://symfony.com/doc/current/bundles/SensioGeneratorBundle/commands/generate_bundle.html
http://symfony.com/doc/current/book/page_creation.html


## Генерация

```
php app/console generate:bundle --no-interaction --structure --format=yml --dir=src --namespace="Compo\SampleBundle"
```

Будет сгенерирован бандл по шаблону.
При генерации, будет ошибка: `Enabling the bundle inside the Kernel: FAILED`
Необходимо будет вручную подключить в [/app/AppKernel.php](/app/AppKernel.php)

## Подключение

**app/AppKernel.php**

```php
class AppKernel extends CoreKernel
{
   public function registerBundles()
   {
//...
        $bundles[] = new \Compo\SampleBundle\CompoSampleBundle();
//...
  }
}
```

## Бандл

[/src/Compo/SampleBundle/CompoSampleBundle.php](/src/Compo/SampleBundle/CompoSampleBundle.php)

## Контроллер

[/src/Compo/SampleBundle/Controller/DefaultController.php](/src/Compo/SampleBundle/Controller/DefaultController.php)

Простой контроллер, с одним действием `indexAction`, обязательным GET аргументом. И передачей этого аргумента в шаблон.

## DependencyInjection

[/src/Compo/SampleBundle/DependencyInjection](/src/Compo/SampleBundle/DependencyInjection)

[/src/Compo/SampleBundle/DependencyInjection/Configuration.php](/src/Compo/SampleBundle/DependencyInjection/Configuration.php)
Служит для описания схемы конфига бандла, значений по умолчанию, валидации значений конфига.

[/src/Compo/SampleBundle/DependencyInjection/SandboxSampleExtension.php](/src/Compo/SampleBundle/DependencyInjection/CompoSampleExtension.php)
Подключает конфиги, конфиг сервисов бандла.

## Resources

Конфиги, документация, шаблоны, переводы, js/css/images...

[/src/Compo/SampleBundle/Resources](/src/Compo/SampleBundle/Resources)

### Routing

[/src/Compo/SampleBundle/Resources/config/routing.yml](/src/Compo/SampleBundle/Resources/config/routing.yml)

Конфиг маршрутизации бандла. Имя маршрута, путь ( обязательный аргумент), действие.

Данный конфиг, будет добавлен в [/app/config/routing.yml](/app/config/routing.yml)

Если, это бандл нашего движка, то, подключение маршрутов данного бандла, нужно вынести в CompoCore. И добавим префикс для маршрутов.
[/src/Compo/CoreBundle/Resources/config/app/routing.yml](/src/Compo/CoreBundle/Resources/config/app/routing.yml)

### Сервисы

[/src/Compo/SampleBundle/Resources/config/services.yml](/src/Compo/SampleBundle/Resources/config/services.yml)

Конфиг сервисов бандла, очистим, или оставим пока как есть.

### Документация

[/src/Compo/SampleBundle/Resources/doc/index.rst](/src/Compo/SampleBundle/Resources/doc/index.rst)

Документация для бандла, удалим.

### Переводы

[/src/Compo/SampleBundle/Resources/translations/messages.fr.xlf](/src/Compo/SampleBundle/Resources/translations/messages.fr.xlf)

Перевод, удалим

### Шаблоны

[/src/Sandbox/SampleBundle/Resources/views/Default](/src/Sandbox/SampleBundle/Resources/views/Default)

## Тесты

[/src/Sandbox/SampleBundle/Tests/Controller/DefaultControllerTest.php](/src/Sandbox/SampleBundle/Tests/Controller/DefaultControllerTest.php)

Тесты, удалим, или пока оставим...

## Финиш

http://compo-standard.prononaserver.ru/app_dev.php/sample - 404. `sample` - просто префикс.
http://compo-standard.prononaserver.ru/app_dev.php/sample/hello - 404. - у нас иной маршрут.
http://compo-standard.prononaserver.ru/app_dev.php/sample/hello/USERNAME - 404. - у нас иной маршрут. В dev-окружении, сообщает, что маршрут есть такой, но неопубликован.

При изменении маршрутов, добавлении новых, необходимо:

```
php app/console sonata:page:update-core-routes --site=all
php app/console sonata:page:create-snapshots --site=all
```

Или:

```
php app/console compo:update --env=prod
```

http://compo-standard.prononaserver.ru/app_dev.php/sample/hello/USERNAME - теперь всё ок.