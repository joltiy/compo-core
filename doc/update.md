# Обновление

[UpdateCommand](./src/Compo/CoreBundle/Command/UpdateCommand.php)

```
git pull
php composer.phar install -o
php app/console compo:update --env=prod
```

## Обновление на тестовом http://engine.optipro.ru/

```
ssh w_engine@engine.optipro.ru
zsh
cd engine.optipro.ru
git pull
php composer.phar install -o
php app/console compo:update --env=prod
```

**Прогрев кеша:**

Выполнять не обязательно.

```
php app/console cache:warmup --env=dev
php app/console cache:warmup --env=prod
```
