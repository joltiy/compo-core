# Модель

http://symfony.com/doc/current/book/doctrine.html

http://symfony.com/doc/current/bundles/SensioGeneratorBundle/commands/generate_doctrine_entity.html

http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/index.html

Описывается структура таблицы. Типы полей, значения по умолчанию.

Указываются связи с другими таблицами.

Так, модель базы данных, будет задокументирована, описана структура и связи, какая-то базовая локига (валидация, хуки).

В IDE имеется автодополнение, проще осуществлять выборку.

Сложные запросы вынесены в Репозиторий.


Актуальность на проектах, поддерживается через миграции.

Генерация сущности

`-n` - Do not ask any interactive question

```
php bin/console generate:doctrine:entity --format=annotation --entity=CompoSampleBundle:Sample -n
```

Будет сгенерировано два новых класса.

[src/Compo/SampleBundle/Entity/Sample.php](/src/Compo/SampleBundle/Entity/Sample.php)

Необходимо сгенерировать get/set.

## Validation
С помощью анотаций, можно... расширить логику работы с сущнотью. Например, валидация полей.

Можно прописать правила валиации для полей, например, используется в формах.

http://symfony.com/doc/current/book/validation.html

## Связи

...

Если у сущности есть связь:

```php
    /**
     * @Orm\ManyToOne(targetEntity="Catalog", inversedBy="products")
     * @Orm\JoinColumn(name="catalog_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $catalog;
```

То при удалении, каталога, товары тоже будут удалены. Или, можно onDelete="SET NULL", тогда если каталог удалён, у товаров catalog_id будет NULL
Это на уровне БД, InnoDB


## События

...

Можно указать callback, при каких либо событиях.

http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/events.html#lifecycle-events


Указать хук postRemove. Который, например, удалит изображения....

## Репозиторий

[src/Compo/SampleBundle/Entity/SampleRepository.php](/src/Compo/SampleBundle/Entity/SampleRepository.php)

Когда, SQL запрос непростой, а тем более во многих местах востребован, его стоит создавать в репозитории сущности.

К примеру... Всего один критерий выборки.

```php
$samples = $this->getDoctrine()->getManager()->getRepository('CompoSampleBundle:Sample')->findBy(array('enabled' => true));
```

А когда условий будет много + сортировки + лимиты, да раскидано по разным местам приложения... Добавим в репозиторий метод `findAllEnabled`.

```php
$samples = $this->getDoctrine()->getManager()->getRepository('CompoSampleBundle:Sample')->findAllEnabled();
```

Если, репозиторий часто востребован, его можно добавить в сервисы.

```yaml
parameters:
    compo_sample.sample_entity: "CompoSampleBundle:Sample"

services:
    compo_sample.sample_repository.:
        class: Compo\SampleBundle\Entity\SampleRepository
        factory: ["@doctrine", getRepository]
        arguments:
            - "%compo_sample.sample_entity%"
```

```php
$samples = $this->get("compo_sample.sample_repository")->findAllEnabled();
```

## Выборка

Доступны простые методы выборки:

http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/working-with-objects.html#querying

```php
$samples = $this->getDoctrine()->getManager()->getRepository('CompoSampleBundle:Sample')->findBy(array('enabled' => true));
```

Для более сложных, QueryBuilder:

http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/query-builder.html

```php
// /src/Compo/SampleBundle/Entity/SampleRepository.php

        $qb = $this->createQueryBuilder('s');

        $qb->select('s');

        $qb->where('s.enabled = 1');

        return $qb->getQuery()->getResult();
```