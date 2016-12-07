# Проблемы

## Установка

### PHP Intl

```
Symfony\Component\Intl\DateFormatter\IntlDateFormatter::__construct() method's argument $locale value 'ru' behavior is not implemented.
Only the locale "en" is supported. Please install the "intl" extension for full localization capabilities.") in SonataTimelineBundle:Block:timeline.html.twig at line 37.
```

**Решение**

Необходимо PHP-Intl расширение.
http://stackoverflow.com/questions/1451468/intl-extension-installing-php-intl-dll

```ini
extension=php_intl.dll
```

### Error on "php composer.phar install"

Ошибка появляется вследствии того, что не достаточно прав для создания символических ссылок.

**Решение**

Необходимо консоль запустить с правами администратора. Перейти в директорию проекта и заново выполнить

```
php composer.phar install -o
```

### Errors with cache, memory

**Прогрев кеша**

```
php app/console cache:warmup --env=dev
php app/console cache:warmup --env=prod
```