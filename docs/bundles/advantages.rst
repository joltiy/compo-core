Advantages - Приемущества
==========================

Позволяет создавать списки приемуществ, для краткой информации о особенностях, сервисах магазина или другой полезной для покупателя информации.

Размещать на страницах сайта, в модальных окнах, уведомлениях и письмах.

Возможность вывода в различных шаблонах.

Можно использовать изображения, иконки.

Отображаются только включённые элементы списка приемуществ.

Возможна сортировка элементов по позиции.

Панель управления
-------------------

* Список

.. figure:: ../images/advantages/list.png
    :align: center

* Редактирование

.. figure:: ../images/advantages/item_edit.png
    :align: center

Блоки
-------------------

.. code-block:: bash

    {{ sonata_block_render({
        'type': 'compo_advantages.block.service.advantages',
        'settings': {
            'id': 123,
            'template': 'CompoAdvantagesBundle:Block:advantages.html.twig'
        }
    }) }}

.. figure:: ../images/advantages/block.png
    :align: center

.. figure:: ../images/advantages/block_edit.png
    :align: center

Шаблоны
-------------------

.. code-block:: yaml

    sonata_block:
        blocks:
            compo_advantages.block.service.advantages:
                cache: sonata.cache.memcached
                contexts: [sonata_page_bundle]
                templates:
                    - { name: 'advantages.template.simple', template: 'CompoAdvantagesBundle:Block:advantages_simple.html.twig' }
