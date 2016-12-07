# Composer
Менеджер зависимостей.

https://getcomposer.org/

https://getcomposer.org/download/ - есть Windows инсталятор, прописывает в PATH. Требуется PHP, PHP-OpenSSL.

Если установлен глобально, то команды для composer вида:

```
composer install
```

## Пакеты
- https://packagist.org/ - публичный репозиторий пакетов.
- http://knpbundles.com/ - Symfony банды.
- https://github.com/

Если, какого либо пакета нет в https://packagist.org/

Но есть репозиторий, либо архив: https://getcomposer.org/doc/05-repositories.md#loading-a-package-from-a-vcs-repository


## Основные команды

`-o` - оптимизировать autoload

### Установка зависимостей из composer.lock

```
php composer.phar install
```

### Обновление зависимостей

```
php composer.phar update
```

### Генерация оптимизированного autoload

```
php composer.phar dump-autoload -o
```

### Добавление нового пакета

```
php composer.phar require knplabs/knp-paginator-bundle
```