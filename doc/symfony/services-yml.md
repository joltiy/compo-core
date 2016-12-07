#  Сервисы и файлы конфигурации сервисов в YAML

YAML - простой формат. Но в Symfony он расширен: импорт, параметры, слияние конфигов, алиасы...

PhpStorm, хорошо понимает Symfony YAML, есть автодополнение Ctrl+Space, кликабельность Ctrl+Left Click.

В Symfony приложение собирается из бандлов, компонентов, классов... через Dependency Injection - Инверсию зависимостей, в Service Container.

В [/app/AppKernel.php](/app/AppKernel.php) - подключается бандл.

```php
class AppKernel extends CoreKernel
{
    public function registerBundles()
    {
        $bundles = parent::registerBundles();

        $bundles[] = new \AppBundle\AppBundle();

        return $bundles;
    }
}

```

[/src/AppBundle/AppBundle.php](/src/AppBundle/AppBundle.php) - наследуется от базового.

Бандл регистрирует различные ресурсы, осуществляя поиск по умолчанию, по путям, принятым в Symfony.

Если посмотреть базовый, то он регистрирует команды, и DependencyInjection\Extension бандла, если есть.

[/src/AppBundle/DependencyInjection/AppExtension.php](/src/AppBundle/DependencyInjection/AppExtension.php) - подключает конфиги бандда...


Обычно, [/src/Compo/AvailabilityBundle/Resources/config/services.yml](/src/Compo/AvailabilityBundle/Resources/config/services.yml) - описаны сервисы бандла.

Имя сервиса, класс, аргументы для конструктора, вызов методов для инициализации, теги (различные метки, по которым Контейнер определяет,
когда инициализировать сервис, или какие либо переменные, необходимые для других бандлов, где используется этот сервис)

Так, сервисом можно сделать любой класс, инициализировать его с необходимыми аргументами, вызвать необходимые методы.


Например:

```yaml
services:
    database:
        class: Compo\Database
        arguments:
            - "my_login"
            - "my_password"
        calls:
            - [ setType, ["mysql"]]
            - [ setHost, ["localhost"]]

```

Этот сервис, можно перегрузить в конфигах (но так не делают =))

```yaml
services:
    database:
        class: MyProject\Database
        arguments:
            - ~
            - "123"
        calls:
            - [ setHost, ["127.0.0.1"]]
```

В контроллерах, получить доступ к сервису можно так:

```php
$this->get("database")->query("...");
```

Теги, из коробки: http://symfony.com/doc/current/reference/dic_tags.html

Каждый бандл может объявить свои теги, например SonataAdmin
```yaml
        tags:
            - { name: sonata.admin...}
```

И Sonata подключает данный сервис как модуль админки.


Пример тега: kernel.event_listener - обработка событий ядра. http://symfony.com/doc/current/cookbook/event_dispatcher/event_listener.html

В общем, сервисы, инверсия зависимостей, позволяет объявлять, перегружать, инициализировать, менять логику приложения...

К примеру, в Compo, есть service контейнер: Connector.

Классы инициализируются кодом, $this->basket = new Basket($this);


Через некоторое время добавили аналог services.yml. Что бы была возможность, не трогая код Connector, объявить свой Basket, расширить логику на каком либо проекте.
Конфиг сервисов/служб...

```php
$config['classes']['basket']    = '\Compo\Logic\Basket';
```

Сервисов может быть много. Сервисы зависимы. Пердавать в каждый сервис, контейнер - будут проблемы. Инициализировать всё сразу и в одном месте - будут проблемы.


Через некоторое время добавили в Connector...
С целью ленивой инициаизации. С целью только перегрузить метод инициализации сервиса, а не весь конструктор или init()

```php
    public function getBasket()
    {
        $classes = $this->getConfig()->getValue('classes');
        if (is_null($this->basket)) {
            $this->basket = new $classes['basket']($this);
        }
        
        return $this->basket;
    }
```


А в Symfony, было бы, то что выше:

```yml
services:
    basket:
        class: \Compo\Logic\Basket
        arguments:
            - "@service_container"
```

В контроллерах, инициализировать/получить доступ к сервису, можно так:

```php
$this->get("basket")
```

Ещё пример:

```yml
services:
    # Имя сервиса
    basket:

        # Класс
        class: \Compo\Logic\Basket

        # Аргументы для конструктора
        arguments:
            # @имя_другого_сервиса, например контейнера, Connector
            - "@service_container"

        # Вызов методов после инициализации
        calls:
            - [ setHost, ["127.0.0.1"]]
            - [ setConnector, [@service_container]]

        # Теги, например, если сервис является SMS плагном/провайдером, и был бы сервсис SmsManager, который бы подключал sms плагины, то так, можно было бы объявлять SMS плагины
        tags:
            - { name: compo_sms.provider}
```



