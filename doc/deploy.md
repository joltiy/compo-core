# Deploy

http://deployer.org/

Выполняет удалённую установку/обновление проекта.

[/deploy.php](/deploy.php) - скрипт деплоя.

## Обновление

Создать конфиг с серверами, на который будет осуществляться деплой.

Копировать [/app/config/servers.yml.dist](/app/config/servers.yml.dist) в `/app/config/servers.yml`

Отредактировать, указать данные для подключения.

Выполнить:

```
php dep deploy
```