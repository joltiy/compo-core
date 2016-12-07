# Структура директорий и файлов

[/.bowerrc](/.bowerrc) - настройки Bower http://bower.io/

[/bower.json](/bower.json) - зависимости bower, пустой.

[/composer.json](/composer.json) - зависимости Composer.

[/composer.lock](/composer.lock) - установленные зависимости.

[/composer.phar](/composer.phar) - исполняемый файл Composer

[/Procfile](/Procfile) - Heroku...

[/app/](/app/)

[/app/cache](/app/cache) - кеш приложения, временные файлы. В зависимости от окружения (prod/dev/test...) - своя директория кеша.
В кеше: контейнер зависимостей, конфигов, шаблоны, и прочее… 
многое компилируется и кешируется, при включёном APC - выше производительность.

В случае работы в dev-окружении, cache почти не используется. Но иногда необходимо его чистить

```
php app/console cache:clear
```

Или


```
rm -rf app/cache/dev/*
```

Для prod окружения, после правок в шаблоны… или модели, кеш чистить нужно каждый раз

```
php app/console cache:clear --env=prod
```

Или

```
rm -rf app/cache/prod/*
```


[/app/cache/sessions](/app/cache/sessions) - сессии пользователей.
  
[/app/logs](/app/logs) - логи, в зависимости от окружения. Level (info, notice, error) - настраивается в конфиге. Для каждого окружения.

[/app/migraions](/app/migraions) - миграции БД.

[/app/Resources](/app/Resources) - шаблоны, переводы… базовые для приложения, либо перегруженые для бандлов.

[/app/AppCache.php](/app/AppCache.php) - Ядро, подключаемое в [/web/app.php](/web/app.php) в случае использования HTTP кеширования

[/app/AppKernel.php](/app/AppKernel.php) - Ядро приложения. Наследуется от [Compo\CoreBundle\Kernel\AppKernel](./src/Compo/CoreBundle/Kernel/AppKernel.php). Осуществляет сборку приложения, подключение конфигов, подключение бандлов. Все базовые бандлы вынесены в
Compo\CoreBundle\Kerne\AppKernel. 

[/app/autoload.php](/app/autoload.php) - Автозагрузка классов. Composer autoload.

[/app/bootstrap.php.cache](/app/bootstrap.php.cache) - генерируемое минимальное ядро Symfony, после обновления зависимостей.

[/app/check.php](/app/check.php) - проверка готовности окружения для запуска приложения. Например, установлены ли необходимые PHP-расширения.

[/app/SymfonyRequirements.php](/app/SymfonyRequirements.php) - генерируемый после обновления зависимостей, для check.php

    
[/app/config/](/app/config/) - конфиги. Все базовые конфиги вынесены в [/src/Compo/CoreBundle/Resources/config/app](/src/Compo/CoreBundle/Resources/config/app)
    
[/app/config/config.yml](/app/config/config.yml) - базовый. Подключает параметры и CompoCoreBundle/Resources/config/app/config.yml

[/app/config/config_dev.yml](/app/config/config_dev.yml) - dev окружение. Подключает базовый и CompoCoreBundle/..../config_dev.yml
Перегружены настройки логов, включена dev панель…

[/app/config/config_prod.yml](/app/config/config_prod.yml) - dev окружение. Подключает базовый и CompoCoreBundle/.../config_prodyml . Настройки логов для prod-окружения

[/app/config/config_test.yml](/app/config/config_test.yml) - ….

[/app/config/parameters.yml](/app/config/parameters.yml) - параметры, настройки БД и т.д..

[/app/config/parameters.yml.dist](/app/config/parameters.yml.dist) - шаблон для параметров. Параметры по умолчанию. Используется для создания parameters.yml после обновления зависимостей.

[/app/config/routing.yml](/app/config/routing.yml) - маршрутизация

[/app/config/routing_dev.yml](/app/config/routing_dev.yml)

[/bin/](/bin/) - создаётся после обновления зависимостей. Симлинки на исполняемые файлы из зависимостей.

[/src/](/src/) - Код приложения.
    
[/src/AppBundle](/src/AppBundle) - пустой бандл, для примера.

[/src/Compo](/src/Compo) - Движок. Будет вынесен в vendor - зависимость.

[/web/](/web/) - Document root

[/web/assetic](/web/assetic) - ресурсы, JS/CSS/Images…. компилированные, сжатые…
http://symfony.com/doc/current/cookbook/assetic/asset_management.html
Прописаны в: [/src/Compo/CoreBundle/Resources/config/app/assetic.yml](/src/Compo/CoreBundle/Resources/config/app/assetic.yml)

Компилировать

```
php app/console assetic:dump
```

Следить и компилировать. Запускать при разработке, в случае работы с CSS/JS...

```
php app/console assetic:watch
```

По умолчанию, assetic настроен с фильтром для компрессии с **yuicompressor.jar**, необходима Java


[/web/bundles](/web/bundles) - ресурсы бандлов

Если добавлен новый бандл, или создан новый ресурс, то выполнить:

```
app/console assets:install
```

[/web/uploads](/web/uploads) - данные пользователей

[/web/.htaccess](/web/.htaccess) - настроен для работы Symfony приложения

[/web/app.php](/web/app.php) - production точка входа

[/web/app_dev.php](/web/app_dev.php) - dev точка входа, для отладки.