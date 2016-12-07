# Composer schema

https://getcomposer.org/doc/04-schema.md

[/composer.json](/composer.json)


```json
{
  "name": "compo/compo-standard",

  "type": "project", // Для бандлов будет library
 
  "minimum-stability": "dev", // Разрешить dev-версии пакетов
 
  "prefer-stable": false,
 
  "autoload": { // Пути и неймспейсы для генерации автозагрузки
    "psr-4": {
      "": "src/"
    },
    "classmap": [ // дополнительные классы, для генерации автозагрузки
     "app/AppKernel.php",
     "app/AppCache.php"
   ]
 },
 "repositories": [ // Дополнительные репозитории, которых нет на https://packagist.org/
   {
     "type": "git",
     "url": "https://github.com/alexchichin/SonataAdminTreeBundle.git"
   }
 ],
 "require": { // Зависимости

    // PHP расширения
    "ext-intl": "*",
    "ext-mbstring": "*",
    "ext-gd": "*",
    "ext-openssl": "*"

 },

 "scripts": { // Хуки, выполняемые после обновления зависимостей
   "post-install-cmd": [
// Создание parameters.yml из parameters.yml.dist
// Если, в parameters.yml - отсутствует какой либо из параметров, добавленных в  parameters.yml.dist
// То будет вопрос, для добавления этого параметра
     "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters",
// Генерация app/bootstrap.php.cache
     "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",

// Очиска кеша
     "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",

// Создания симлинков для TwitterBootstrap
// https://github.com/phiamo/MopaBootstrapBundle - дополнительная функциональность, для создания каких либо виджетов.
     "Mopa\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::postInstallSymlinkTwitterBootstrap",

// Создание симлинков для ресурсов бандлов (JS/CSS/Images...) /web/bundles
     "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",

// Генерация app/SymfonyRequirements.php
     "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile",

// Хз...
     "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::prepareDeploymentTarget"
   ],
   "post-update-cmd": [

   ]
 },
 "config": {

// Путь для создания симлинков, для исполняемых файлов.
   "bin-dir": "bin",
   "process-timeout": 3600, // Таймаут выполнения
   "preferred-install": {
     "comporu/*": "source", // для наших пакетов, делать только git clone
     "*": "dist" // для остальных выкачивать архивы
   }
 },
 "extra": {

   // Различные переменные, для хуков.
   "symfony-app-dir": "app",
   "symfony-web-dir": "web",
   "symfony-assets-install": "relative",
   "incenteev-parameters": {
     "file": "app/config/parameters.yml",

      // Подставляет переменные поумолчанию, в parameters.yml, если объявлены как переменная окружения в консоли
      "env-map": {
        "database_name": "SYMFONY__DATABASE_NAME",
        "database_user": "SYMFONY__DATABASE_USER",
        "database_host": "SYMFONY__DATABASE_HOST",
        "database_password": "SYMFONY__DATABASE_PASSWORD"
      }
    },

    // Настройки Heroku.
    "heroku": {
      "framework": "symfony2",
      "document-root": "web",

      // php.ini
      "php-config": [
        "date.timezone=Europe/Moscow",
        "display_errors=off",
        "short_open_tag=off"
      ]
    }
  }


 }
}
```