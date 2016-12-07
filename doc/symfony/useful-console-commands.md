# Часто используемые команды

**git pull**

**Установить composer зависимости из lock**

`-o` - оптимизированный autoload

```
composer install -o
php composer.phar install -o
```

**Обновить composer зависимости**

```
composer update -o
php composer.phar update -o
```

**Обновить Compo**

```
php app/console compo:update --env=prod
app/console compo:update --env=prod
```

**Генерировать бандл**

```
php app/console generate:bundle
```

**Генерировать миграции**

```
php app/console doctrine:migrations:diff
```

**Выполнить миграции**

```
php app/console doctrine:migrations:migrate
```

**Обновить sonata page маршруты**

```
php app/console sonata:page:update-core-routes
```

**Удалить кеш**

```
rm -rf app/cache/dev/*
rm -rf app/cache/prod/*
```

**Очистка и прогрев кеша**

```
php app/console cache:clear --env=dev
php app/console cache:clear --env=prod
```

**Прогрев кеша**

```
php app/console cache:warmup --env=dev
php app/console cache:warmup --env=prod
```

**Сгенерировать файл перевода**

```
php app/console translation:extract ru --config=app --output-format=yml --bundle=CompoProductBundle --domain=CompoProductBundle
```

Сгенерировать файлы переводов, для всех Compo бандлов.

```
php app/console compo:translation
```

**Генерация симлинков assets**
```
# make a hard copy of the assets in web/
php app/console assets:install

# if possible, make absolute symlinks in web/ if not, make a hard copy
php app/console assets:install --symlink

# if possible, make relative symlinks in web/ if not, make a hard copy
php app/console assets:install --symlink --relative
```
Лучше всегда использовать ключ --symlink --relative

**Дамп assets**
```
$ php bin/console assetic:dump --env=prod --no-debug
```

**Генерация моделей**
```
php app/console doctrine:generate:entities SampleBundle
```

**Генерация админки**
```
php app/console sonata:admin:generate Compo/SampleBundle/Entity/Sample
```
**Генерация переводов**
```
php app/console translation:extract ru --config=app --output-format=yml --bundle=CompoBlogBundle --domain=CompoBlogAdmin
```
**Дамп autoload файлов**
```
php composer.phar dump-autoload 
```


**Генерация супер юзера FOS**
```
php bin/console fos:user:create --super-admin
```
