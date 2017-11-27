# Change Log

Хронологически упорядоченный список изменений сделанных в проекте: [comporu/compo-core].

- **Added**: новая функциональность.
- **Changed**: изменения в текущей функциональности.
- **Fixed**: исправления ошибок и мелкие правки.
- **Removed**: удаление устаревшей функциональности.

## [v3.3.14] - 2017-11-27

### Added
- Добавлен .editorconfig (+1)
- Добавлен LICENSE (+1)
- Добавлен UPGRADE.md (+1)

### Changed
- Update version: v3.3.14 (+1)
- Обновлён .gitignore /vendor/ (+1)
- Update CHANGELOG.md (+3)
- Update version: v3.3.13 (+1)
- Обновлён composer.json (+1)
- Update version: v3.3.12 (+1)
- Update version: v3.3.11 (+1)
- Update version: v3.3.10 (+1)

## [v3.3.9] - 2017-11-27

### Changed
- Update version: v3.3.9
- Update version: v1.0.1

## [v3.3.8] - 2017-11-27

### Changed
- Update version: v3.3.8

## [v3.3.7] - 2017-11-27

### Changed
- Update version: v3.3.7

## [v3.3.6] - 2017-11-27

### Changed
- Update version: v3.3.6

## [v3.3.13] - 2017-11-27

### Added
- Добавлен .editorconfig
- Добавлен LICENSE
- Добавлен UPGRADE.md

### Changed
- Update CHANGELOG.md (+2)
- Update version: v3.3.13
- Обновлён composer.json

## [v3.3.12] - 2017-11-27

### Changed
- Update version: v3.3.12
- Update version: v3.3.11

## [v3.3.10] - 2017-10-24

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
- Update version: v3.3.10
- Update version: v3.3.9
- Update version: v1.0.1
- Update version: v3.3.8
- Update version: v3.3.7
- Update version: v3.3.6
- Update version: v3.3.5
- Update version: v3.3.4
- Update version: v1.0.8
- Update version: v1.0.7
- Update version: v1.0.6
- Update version: v1.0.5
- Update version: v1.0.4
- Update version: v1.0.3
- Update version: v1.0.2
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
- Fix Variable &quot;batch_action_forms&quot; does not exist.
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
- Refactoring
- Fix bin/console compo:core:project:version:create
- Fix elasticsearch
- Fix path @CompoCore/Nginx/nginx_macro.html.twig (+1)
- Media, add pdf.
- pagespeed Disallow &quot;*.svg&quot;; (+2)
- Fix nginx ssl configs.
- Fix rewrite_to_domain(domain)

### Removed
- Remove &quot;kriswallsmith/spork&quot;
- Remove create public_html

## [v1.0.8] - 2017-10-24

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
- Add FixSlugListener.php - replace &quot;---&quot; to &quot;-&quot;
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
- Update version: v1.0.7
- Update version: v1.0.6
- Update version: v1.0.5
- Update version: v1.0.4
- Update version: v1.0.3
- Update version: v1.0.2
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
- Fix Variable &quot;batch_action_forms&quot; does not exist.
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
- Refactoring (+4)
- Fix bin/console compo:core:project:version:create
- Fix elasticsearch
- Fix path @CompoCore/Nginx/nginx_macro.html.twig (+1)
- Media, add pdf.
- pagespeed Disallow &quot;*.svg&quot;; (+2)
- Fix nginx ssl configs.
- Fix rewrite_to_domain(domain)
- Define branch-alias &quot;dev-develop&quot;: &quot;3.3-dev&quot;
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
- Deployer speed up. Set default stage: &quot;stage&quot;.
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
- Fix: При загрузки блок &quot;Корзина&quot; &quot;мигает&quot;
- Fix: Блок телефон нужно сделать редактируемым текстом
- Fucking drag&amp;drop
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
- Fix: The &quot;framework.trusted_proxies&quot; configuration key has been deprecated in Symfony 3.3. Use the Request::setTrustedProxies() method in your front controller instead.
- Fix: Automatic registration of annotations is deprecated since 3.14, to be removed in 4.0.
- Fix: Using the unquoted scalar value &quot;!event&quot;
- Fix: User Deprecated: Duplicate key (+1)
- Fix: User Deprecated: Duplicate key &quot;label_priority&quot; detected whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated since version 3.2 and will throw \Symfony\Component\Yaml\Exception\ParseException in 4.0.
- Fix: User Deprecated: Duplicate key &quot;label_description&quot; detected whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated since version 3.2 and will throw \Symfony\Component\Yaml\Exception\ParseException in 4.0.
- Fix: User Deprecated: Duplicate key &quot;breadcrumb.link_page_tree&quot; detected whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated since version 3.2 and will throw \Symfony\Component\Yaml\Exception\ParseException in 4.0.
- Fix: User Deprecated: Duplicate key &quot;pages.list_mode&quot; detected whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated since version 3.2 and will throw \Symfony\Component\Yaml\Exception\ParseException in 4.0.
- Fix: User Deprecated: The &quot;sonata.core.slugify.cocur&quot; service is deprecated. You should stop using it, as it will soon be removed.
- Fix: The option &quot;criteria_manufacture_collection_price&quot; with value array is expected to be of type &quot;null&quot; or &quot;integer&quot; or &quot;object&quot; or &quot;string&quot;, but is of type &quot;array&quot;.
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
- Refactoring.
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
- Remove &quot;kriswallsmith/spork&quot;
- Remove create public_html
- Remove app/config/config_test.yml
- Remove JMSSecurityExtraBundle
- Remove composer.phar (+5)



[comporu/compo-core]: https://github.com/comporu/compo-core

[v3.3.14]: https://github.com/comporu/compo-core/compare/v3.3.9...v3.3.14
[v3.3.9]: https://github.com/comporu/compo-core/compare/v3.3.8...v3.3.9
[v3.3.8]: https://github.com/comporu/compo-core/compare/v3.3.7...v3.3.8
[v3.3.7]: https://github.com/comporu/compo-core/compare/v3.3.6...v3.3.7
[v3.3.6]: https://github.com/comporu/compo-core/compare/v3.3.5...v3.3.6
[v3.3.13]: https://github.com/comporu/compo-core/compare/v3.3.12...v3.3.13
[v3.3.12]: https://github.com/comporu/compo-core/compare/v3.3.10...v3.3.12
[v3.3.10]: https://github.com/comporu/compo-core/compare/v3.3.1...v3.3.10
[v1.0.8]: https://github.com/comporu/compo-core/compare/...v1.0.8
