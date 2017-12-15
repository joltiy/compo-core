# Change Log

Хронологически упорядоченный список изменений сделанных в проекте: [comporu/compo-core].

- **Added**: новая функциональность.
- **Changed**: изменения в текущей функциональности.
- **Fixed**: исправления ошибок и мелкие правки.
- **Removed**: удаление устаревшей функциональности.

## [v3.3.37] - 2017-12-15 10:35:34

### Added
- Исправлено массовое изменение коллекций. Добавлена AJAX подгрузка коллекций, в зависимости от выбранного производителя. https://trello.com/c/pGiZLwaV/142-%D0%BC%D0%B0%D1%81%D1%81%D0%BE%D0%B2%D1%8B%D0%B5-%D0%B4%D0%B5%D0%B9%D1%81%D1%82%D0%B2%D0%B8%D1%8F-%D0%B8%D0%B7%D0%BC%D0%B5%D0%BD%D0%B8%D1%82%D1%8C-%D0%BA%D0%BE%D0%BB%D0%BB%D0%B5%D0%BA%D1%86%D0%B8%D1%8E

## [v3.3.36] - 2017-12-15 09:19:02

### Added
- Ссылка на редактирование JS/CSS вынесена в раздел "Сайт". Добавлен пункт меню на редактирование Media.

### Fixed
- Незначительные правки
- Создан ConfigureTabMenuTrait.php

## [v3.3.35] - 2017-12-14 17:36:23

### Fixed
- Добавелно редактирование placeholder для поля поиска.

## [v3.3.34] - 2017-12-14 15:40:40

### Fixed
- Исправлена генерация URL на дочернии админки.

## [v3.3.33] - 2017-12-13 13:55:06

### Added
- Добавлена опция пробного импорта
- Добавлен функционал импорта. Создан SonataImportBundle
- Добавлен экспорт/импорт меток для новостей.
- Добавлена кнопка импорта
- Исправлена транслитерация для экспорта. Исправлен формат даты для экспорта. Добавлены маршруты для экспорта.
- Добавлен вывод меток для новостей в админке

### Fixed
- Исправлен импорт. Доработано импортирование при обновлении, создании, связанных сущностей. Исправлено отображение результатов импорта.
- Исправлена функция пробного импорта
- Реализована функция пробного импорта
- Исправлено отображение результатов импорта
- Исправлена форма импорта
- Отключено отображение ошибок транслитерации в консоли.
- Исправлено отображение и сохранение отображаемых столбцов в админке.

## [v3.3.32] - 2017-12-13 10:23:46

### Fixed
- Исправлена генерация slug после клонирования. https://trello.com/c/he22hQ3j/136-url-%D0%B2-%D0%BA%D0%BE%D0%BF%D0%B8%D1%8F%D1%85

## [v3.3.31] - 2017-12-13 09:31:17

### Added
- Добавлен перевод значений выборки в статистике.

## [v3.3.30] - 2017-12-13 09:07:01

### Added
- Добавлен перевод значений выборки в статистике.

## [v3.3.29] - 2017-12-13 08:23:51

### Added
- Добавлена кнопка закрытия в форму "Хочу дешевле"

### Fixed
- Форма "Хочу дешевле", добвлено дополнительное поле для названия кнопки.

## [v3.3.28] - 2017-12-13 06:17:06

### Fixed
- Исправлен экспорт. Экспортируются только отображаемые столбцы.

### Removed
- Удалён устаревшей код

## [v3.3.27] - 2017-12-12 18:03:28

### Fixed
- Fix: Не корректно считается кол-во плитки - https://trello.com/c/H5vOw454/203-%D0%BD%D0%B5-%D0%BA%D0%BE%D1%80%D1%80%D0%B5%D0%BA%D1%82%D0%BD%D0%BE-%D1%81%D1%87%D0%B8%D1%82%D0%B0%D0%B5%D1%82%D1%81%D1%8F-%D0%BA%D0%BE%D0%BB-%D0%B2%D0%BE-%D0%BF%D0%BB%D0%B8%D1%82%D0%BA%D0%B8

## [v3.3.26] - 2017-12-12 16:39:25

### Added
- Добавлен заголовок и описание формы "Хочу дешевле"
- Добавлен плагин выбора цвета в визуальный редактор

## [v3.3.25] - 2017-12-12 13:28:49

### Fixed
- Исправлен виджет статистики за период.

## [v3.3.24] - 2017-12-12 11:44:23

### Fixed
- Исправлено создание виджета статистики за периоды
- Исправлено отображения статистики по неделям

## [v3.3.23] - 2017-12-05 15:41:44

### Added
- Добавлено AJAX обновление блоков статистики
- Добавлен блок статистики по дате
- Добавлен базовый шаблон для блоков в админке.
- Добавлена генерация ссылки на список в блоке статистики
- Добавлена статистика по кол-ву.

### Changed
- Update .editorconfig
- Обновлён модуль статистики и дашбордов. Исправлено отображение выбора дошборда. Исправлено создание дашборда и виджетов. Исправлено отображение виджетов. Удалены устаревшие виджеты.

### Fixed
- Незначительные правки (+5)
- Доработаны дашборды, статистика.
- Исправлено отображение статистики
- Dashboards
- Основные зависимости перенесены в compo-core.
- Исправлена совместимость с SonataUserBundle 4.0
- Исправлены composer_options в deployer, разрешена установка dev-пакетов.

## [v3.3.22] - 2017-12-05 07:48:50

### Changed
- Update .gitignore

### Fixed
- Исправленно дробное кол-во товара в заказе

## [v3.3.21] - 2017-12-05 02:00:52

### Added
- Добавлена Twig функция project_version()

### Fixed
- Исправлено подключение JS/CSS в админке
- Исправлено подключение fosjsrouting в админке (+1)

## [v3.3.20] - 2017-12-04 18:29:05

### Added
- Добавлено AJAX редактирование связей Many-to-Many

### Fixed
- Незначительные правки

## [v3.3.19] - 2017-11-28 08:57:46

### Added
- Добавлен .php_cs.dist
- Добавлен столбце Тип, в обратную связь.

### Changed
- Update .php_cs.dist
- Upgrade vendors
- Update .gitignore (+2)
- Update .editorconfig

### Fixed
- Исправлены SMS уведомления
- Незначительные правки (+1)
- Disable runDoctrineFixturesLoadAppend
- Исправлено название товара в корзине, в заказе...
- Исправлено название товара в корзине.
- php-cs-fixer Compo
- php-cs-fixer Pix/PixSortableBehaviorBundle
- php-cs-fixer Sylius/SyliusSettingsBundle
- php-cs-fixer SyliusThemeBundle
- [NotificationBundle] Small fix.
- [NotificationBundle] Рефакторинг. Уведомления, форма обратной связи.
- [NotificationBundle] Рефакторинг
- Исправлена ошибка просмотра сообщений обратной связи, когда отсутствуют дополнительные данные.

## [v3.3.18] - 2017-11-28 08:11:12

### Changed
- Update .editorconfig

## [v3.3.17] - 2017-11-28 07:53:30

### Fixed
- Незначительные правки

## [v3.3.16] - 2017-11-27 14:21:35

### Added
- Добавлен вывод CHANGELOG и версии проекта в админке.
- [ContactsBundle] Добавлено поле имя. В блоке контактов, выводятся все контакты, а не только первый.
- Добавлено создание нескольких контактов

### Fixed
- Исправлены контакты, адрес - не обязательное поле
- Исправлен перевод label_address

## [v3.3.15] - 2017-11-27 13:33:31

### Changed
- Update LICENSE

## [v3.3.14] - 2017-11-27 11:59:37

### Changed
- Обновлён .gitignore /vendor/

## [v3.3.13] - 2017-11-27 07:39:37

### Added
- Добавлен .editorconfig
- Добавлен LICENSE
- Добавлен UPGRADE.md

### Changed
- Обновлены внешние зависимости

## [v3.3.5] - 2017-10-24 05:05:25

### Added
- Add VERSION file
- Add getAdminByClass for CRUDController.php
- Refactoring. Update translations. Fix batch actions. Add batch action change availability.
- Add additional fields for feedback admin.
- Add feedback block select type
- Fix banner block settings. Add slider template
- Add translate feedback form type
- Add translate block title CompoFeedbackBundle.ru.yml
- Add translate block title (+1)
- Add macro pagespeed2(pagespeed)
- Add supervisor:restart
- Add angulartics
- Add settings for google/yandex.
- Add JobQueueBundle for Cron commands
- Add nginx_macro.html.twig - location_build
- Add database:backup. Add behat.
- Add deployer task: database:backup
- Add ProjectVersionCreateCommand.php: compo:core:project:version:create
- Add AppKernel.php methods: getProjectName, getProjectVersionPath, getProjectVersion
- Add config for pagespeed enable. Fix path @CompoCore/Nginx/nginx_macro.html.twig Translate redirect page.
- Additional files
- Add twig macro pagespeed()
- Add twig macro for nginx. (+1)

### Changed
- Update README.md (+2)
- Update export.
- Update translations: label_is_replace, label_created_at_readonly
- Обновлены внешние зависимости (+6)
- Update admin menu
- Change exception for blocks on production.
- Change Sonata Page RequestFactory from host_with_path to host

### Fixed
- Clear noinspection
- Fix discount
- Fix filter with childs admin
- Fix Variable "batch_action_forms" does not exist.
- Fix admin css
- Fix ProductWantLowerCostFormType.php
- Fix batch action form layout.
- Fix admin.css label position.
- Fix translation Количество
- Fix photos popup, angular hash (+2)
- Small fix. (+5)
- Fix error base_template
- Fix JobRepository.php order by
- Analytics (+1)
- Fix Настойки
- Fix banner menu admin
- Незначительные правки (+8)
- Test (+14)
- Analytics.
- Fix redirects...
- Holy shit! (+2)
- Fix findLastForRelatedEntity
- Jobs...
- Fix sitemaps path.
- Fix urls for pages.
- Behat HTML format
- Fix behat path
- Revert test env
- Google Merchant
- Качество кода
- Fix bin/console compo:core:project:version:create
- Fix elasticsearch
- Fix path @CompoCore/Nginx/nginx_macro.html.twig (+1)
- Media, add pdf.
- pagespeed Disallow "*.svg"; (+2)
- Fix nginx ssl configs.
- Fix rewrite_to_domain(domain)

### Removed
- Remove "kriswallsmith/spork"
- Remove create public_html

## [v1.0.8] - 2017-10-24 05:05:25

### Added
- Add VERSION file
- Add getAdminByClass for CRUDController.php
- Refactoring. Update translations. Fix batch actions. Add batch action change availability.
- Add additional fields for feedback admin.
- Add feedback block select type
- Fix banner block settings. Add slider template
- Add translate feedback form type
- Add translate block title CompoFeedbackBundle.ru.yml
- Add translate block title (+1)
- Add macro pagespeed2(pagespeed)
- Add supervisor:restart
- Add angulartics
- Add settings for google/yandex.
- Add JobQueueBundle for Cron commands
- Add nginx_macro.html.twig - location_build
- Add database:backup. Add behat.
- Add deployer task: database:backup
- Add ProjectVersionCreateCommand.php: compo:core:project:version:create
- Add AppKernel.php methods: getProjectName, getProjectVersionPath, getProjectVersion
- Add config for pagespeed enable. Fix path @CompoCore/Nginx/nginx_macro.html.twig Translate redirect page.
- Additional files
- Add twig macro pagespeed()
- Add twig macro for nginx. (+1)
- Add layout for page codes. (+1)
- Add {{php_version}} (+1)
- Add deploy:dev deployer task (+1)
- Add FixSlugListener.php - replace "---" to "-"
- Add PageCodeBundle.
- Add Id trait for entities. (+1)
- Add router.request_context parameters.
- Add command for generate ide-twig.json, for themes.
- Add Craue\FormFlowBundle. Add WebServerBundle. Remove getRootDir()
- Add fix rollback task.
- Fix default actions. Add persist columns.
- Menu. Add target option.
- Banner item. Add description field.
- Add Keram theme. (+5)
- Add assetic_static_gzip.
- Add twig helpers for media: media_path, media_thumbnail
- Add ignore_route_patterns for Liip Image.
- Add liip_imagine filters.
- Add Liip Image routes
- Изменения. Добавлен backend ContactsBundle. Добавлен patch для Doctrine в Compo/CoreBundle/Doctrine/VendorOverride
- Add design. (+34)
- Изменение в deployer recipe Добавлен пароль в команду (+1)
- Add BasketBundle routing. (+1)
- Add BasketBundle
- Add default theme settings.
- Update convert old database. Add feature types.
- Update convert old database. Add root catalog.
- Add src/SeoBundle
- Add assets to ignore.

### Changed
- Update README.md (+2)
- Update export.
- Update translations: label_is_replace, label_created_at_readonly
- Обновлены внешние зависимости (+15)
- Update admin menu
- Change exception for blocks on production.
- Change Sonata Page RequestFactory from host_with_path to host
- Upgrade composer.
- Change log level for production.
- Update vendors.
- Update useful-console-commands.md (+1)
- Update composer vendors.

### Fixed
- Clear noinspection
- Fix discount
- Fix filter with childs admin
- Fix Variable "batch_action_forms" does not exist.
- Fix admin css
- Fix ProductWantLowerCostFormType.php
- Fix batch action form layout.
- Fix admin.css label position.
- Fix translation Количество
- Fix photos popup, angular hash (+2)
- Small fix. (+70)
- Fix error base_template
- Fix JobRepository.php order by
- Analytics (+1)
- Fix Настойки
- Fix banner menu admin
- Незначительные правки (+21)
- Test (+13)
- Analytics.
- Fix redirects...
- Holy shit! (+2)
- Fix findLastForRelatedEntity
- Jobs...
- Fix sitemaps path.
- Fix urls for pages.
- Behat HTML format
- Fix behat path
- Revert test env
- Google Merchant
- Качество кода (+5)
- Fix bin/console compo:core:project:version:create
- Fix elasticsearch
- Fix path @CompoCore/Nginx/nginx_macro.html.twig (+1)
- Media, add pdf.
- pagespeed Disallow "*.svg"; (+2)
- Fix nginx ssl configs.
- Fix rewrite_to_domain(domain)
- Define branch-alias "dev-develop": "3.3-dev"
- Fix database:sync-to-remote sql dump filename.
- Fix referer. (+1)
- Order list favicon for referer.
- Fix search. Neri
- Temp fix.
- Order: referer.
- AdminNavBar: News
- AdminNavBar: Faq
- AdminNavBar: Articles
- Canonical for Page.
- Sms notification
- Fix Contacts fileds type, length
- Product list, x-editable  availability
- Fix SeoPage column type to text: title, header
- URL, redirects, legacy. (+2)
- Legacy convert. Manufacture description. (+2)
- Legacy convert. Product - Supplier. M:M
- Fix admin Compo logo.
- Fix deployer commands
- Fuck
- Refactoring, configs. (+11)
- Refactoring, configs. Security api, api_doc.
- Fix liip image, root path.
- Fix M:M, child admins, tab menu.
- Fix error, additional columns in admin list.
- Fix fos_js_routes.js
- Temp fix
- Admin Navbar.
- Fix spaces after icons.
- Deployer speed up. Set default stage: "stage".
- Fix bower install. https://stackoverflow.com/questions/15669091/bower-install-using-only-https
- Fix. User impersonating (+1)
- Fix. User list error.
- Up (+172)
- Keram style. (+3)
- Fix default actions. Trash, untrash, history...
- Fix translations.
- Fix security ACL. (+1)
- Disable persist_filters.
- Bower.dependencies (+1)
- Fix: Ckeditor.
- Fix: Deploy shared dirs.
- Валидация форм.
- Fix: При загрузки блок "Корзина" "мигает"
- Fix: Блок телефон нужно сделать редактируемым текстом
- Fucking drag&drop
- Fix menu...
- Fix: Sylius Theme PathResolver.php
- Fix: AdminLabelTranslationDomainCompilerPass.php
- fos:js-routing:dump after deploy.
- Color picker for admin.
- Color picker for features.
- Fix: Menu tree.
- Fix: deprecated errors. (+1)
- Fix: The Sonata\AdminBundle\Admin\Admin class is deprecated since version 3.1 and will be removed in 4.0. Use Sonata\AdminBundle\Admin\AbstractAdmin instead.
- Fix: Not setting the default_formatter configuration node is deprecated since 3.2, and will no longer be supported in 4.0.
- Fix: The "framework.trusted_proxies" configuration key has been deprecated in Symfony 3.3. Use the Request::setTrustedProxies() method in your front controller instead.
- Fix: Automatic registration of annotations is deprecated since 3.14, to be removed in 4.0.
- Fix: Using the unquoted scalar value "!event"
- Fix: User Deprecated: Duplicate key (+1)
- Fix: User Deprecated: Duplicate key "label_priority" detected whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated since version 3.2 and will throw \Symfony\Component\Yaml\Exception\ParseException in 4.0.
- Fix: User Deprecated: Duplicate key "label_description" detected whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated since version 3.2 and will throw \Symfony\Component\Yaml\Exception\ParseException in 4.0.
- Fix: User Deprecated: Duplicate key "breadcrumb.link_page_tree" detected whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated since version 3.2 and will throw \Symfony\Component\Yaml\Exception\ParseException in 4.0.
- Fix: User Deprecated: Duplicate key "pages.list_mode" detected whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated since version 3.2 and will throw \Symfony\Component\Yaml\Exception\ParseException in 4.0.
- Fix: User Deprecated: The "sonata.core.slugify.cocur" service is deprecated. You should stop using it, as it will soon be removed.
- Fix: The option "criteria_manufacture_collection_price" with value array is expected to be of type "null" or "integer" or "object" or "string", but is of type "array".
- Composer (+1)
- Merge pull request #1 from jivoy1988/master
- Fix, contacts error.
- Fix legacy convert from old database. (+6)
- Изменения по задачам https://trello.com/c/O916t87Z/113-%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%BE%D0%B2
- Clear code.
- Fix contacts.
- Fix... PHP 7.1... (+2)
- 404, tags filter...
- Fix RedirectListener.php (+1)
- Yandex.Market.
- массовое удаление/добавление товаров в Я.Маркет
- Дата создания товара в админке и мобильная корзина. Тестирование.
- SEO help, product.sku
- News tags.
- Keram design (+1)
- Фильтр. Отображение фабрик, в зависимости от выбранной страны.
- Увеличение времени сессии для администратора до суток
- Keram Import.
- Alt for images.
- Default seo tags.
- Deploy sitemaps (+1)
- Discount
- Sitemaps. (+2)
- FOSElastica - max_result_window (+5)
- Liip Imagine paths.
- Search (+1)
- Small fixes. (+31)
- Big update. (+6)
- Fix menu url routing.
- small changes
- small update (+2)
- Изменения в архитектуре Angular приложений - update
- Изменение архитектуры angular-приложений
- Отсылка писем
- Обработка и проверка формы переданной через Api
- Big update. Cart, Customer. (+1)
- Contacts Api implementation
- small change
- update (+1)
- Fix deploy assets. (+2)
- Filter
- Favorites
- Fix assetic.
- SonataFormatter bug: https://github.com/sonata-project/SonataFormatterBundle/issues/248
- Fix template media, for admin.
- AppKernel.php: enable LiipImagineBundle, reorder bundles.
- Assetic for production: debug=false, cache_busting=true (+1)
- Fix menu, filter, manufacture, collections, product. (+1)
- fixes
- fix block service
- Блок для страницы контактов
- ContactsBundle direct edit action
- PSR-4 upgrade
- Fix PSR-4 (+3)
- classmap addition
- small style fix
- Delete generated entity
- Fix filter (+1)
- Fix filter.
- Фиксирование орфографии в AdvantagesBundle
- Fix advantages spelling
- Custom service for the textpages based on SonataFormatterBundle
- Fix admin login error (+2)
- Tabs Translations
- Качество миниатюр. (+1)
- Услуги
- Google Merchant add.
- Тестовый коммит
- Big update (+11)
- Basket (+1)
- Move all static to CoreBundle.
- Move doc to compo-core.
- Clear composer vendors.
- Fix namespace PixSortableBehaviorBundle
- Copy src/PixSortableBehaviorBundle
- Clear composer.
- Enable deploy:assetic:dump on deploy. (+3)
- Disable cache_busting (+1)
- Init (+4)

### Removed
- Remove "kriswallsmith/spork"
- Remove create public_html
- Remove app/config/config_test.yml
- Remove JMSSecurityExtraBundle
- Remove composer.phar (+5)



[comporu/compo-core]: https://github.com/comporu/compo-core

[v3.3.37]: https://github.com/comporu/compo-core/compare/v3.3.36...v3.3.37
[v3.3.36]: https://github.com/comporu/compo-core/compare/v3.3.35...v3.3.36
[v3.3.35]: https://github.com/comporu/compo-core/compare/v3.3.34...v3.3.35
[v3.3.34]: https://github.com/comporu/compo-core/compare/v3.3.33...v3.3.34
[v3.3.33]: https://github.com/comporu/compo-core/compare/v3.3.32...v3.3.33
[v3.3.32]: https://github.com/comporu/compo-core/compare/v3.3.31...v3.3.32
[v3.3.31]: https://github.com/comporu/compo-core/compare/v3.3.30...v3.3.31
[v3.3.30]: https://github.com/comporu/compo-core/compare/v3.3.29...v3.3.30
[v3.3.29]: https://github.com/comporu/compo-core/compare/v3.3.28...v3.3.29
[v3.3.28]: https://github.com/comporu/compo-core/compare/v3.3.27...v3.3.28
[v3.3.27]: https://github.com/comporu/compo-core/compare/v3.3.26...v3.3.27
[v3.3.26]: https://github.com/comporu/compo-core/compare/v3.3.25...v3.3.26
[v3.3.25]: https://github.com/comporu/compo-core/compare/v3.3.24...v3.3.25
[v3.3.24]: https://github.com/comporu/compo-core/compare/v3.3.23...v3.3.24
[v3.3.23]: https://github.com/comporu/compo-core/compare/v3.3.22...v3.3.23
[v3.3.22]: https://github.com/comporu/compo-core/compare/v3.3.21...v3.3.22
[v3.3.21]: https://github.com/comporu/compo-core/compare/v3.3.20...v3.3.21
[v3.3.20]: https://github.com/comporu/compo-core/compare/v3.3.19...v3.3.20
[v3.3.19]: https://github.com/comporu/compo-core/compare/v3.3.18...v3.3.19
[v3.3.18]: https://github.com/comporu/compo-core/compare/v3.3.17...v3.3.18
[v3.3.17]: https://github.com/comporu/compo-core/compare/v3.3.16...v3.3.17
[v3.3.16]: https://github.com/comporu/compo-core/compare/v3.3.15...v3.3.16
[v3.3.15]: https://github.com/comporu/compo-core/compare/v3.3.14...v3.3.15
[v3.3.14]: https://github.com/comporu/compo-core/compare/v3.3.13...v3.3.14
[v3.3.13]: https://github.com/comporu/compo-core/compare/v3.3.12...v3.3.13
[v3.3.5]: https://github.com/comporu/compo-core/compare/v3.3.1...v3.3.5
[v1.0.8]: https://github.com/comporu/compo-core/compare/5fdb2ebdc0b2f434385dd418f014670fad8b5051...v1.0.8
