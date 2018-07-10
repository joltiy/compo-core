Contacts - Контакты
=================

Управление контактной информацией на сайте.

Панель управления
-------------------

* Список

.. figure:: ../images/contacts/list.png
    :align: center

* Редактирование

.. figure:: ../images/contacts/edit.png
    :align: center

Блоки
-------------------

.. code-block:: twig

    {{ sonata_block_render({
        'type': 'compo_contacts.block.service.contacts_main',
        'settings': {
            'template': 'CompoContactsBundle:Block:contacts_main.html.twig'
        }
    }) }}

.. figure:: ../images/contacts/block.png
    :align: center
