# Установка



## Clone repository

```
git clone git@github.com:comporu/compo-standard.git
```

Либо, в PhpStorm Checkout from VCS и указать: **git@github.com:comporu/compo-standard.git**

## Создать пустую БД

```
compo_standard
```

## Install

В консоли, перейти в папку с проектом.

```
cd compo-standard
```

### Установить зависимости

Если под Windows, то выполнять в консоли, с правами администратора.

**For development:**

```
php composer.phar update
```

**For production:**

```
php composer.phar install -o
```


Диалог первоначальной настройки, в случае отсутствия конфига.

Создание необходимых конфигов, директорий и файлов.

Выполнить, на всякий случай, проверка требований:

```
php app/check.php
```


**Выполнить установку. Создание структуры БД.**

[InstallCommand](./src/Compo/CoreBundle/Command/InstallCommand.php)

```
php bin/console compo:core:install --env=prod
```

**Настроить HTTP-сервер, либо запустить встроенный PHP-WEB сервер.**

```
php bin/console server:run localhost:9091
```


### Настройка HTTP сервера

Настроить http-хост для сайта.

`DOCUMENT_ROOT` - web


**Добавление в hosts**

```
127.0.0.1 compo-standard.ru
```

**For Linux:**

```
nano /etc/hosts
```

**For Windows:**

```
nano /c/Windows/System32/drivers/etc/hosts
```

**Прогрев кеша:**

Выполнять не обязательно.

```
php bin/console cache:warmup --env=dev
php bin/console cache:warmup --env=prod
```
