Feedback - Обратная связь
==========================

Модуль позволяет пользователям оставлять сообщения на сайте и отвечать отправителям в административной части модуля.

Поступившим сообщениям, можно присваивать метки.

Панель управления
-------------------

* Список

.. figure:: ../images/feedback/list.png
    :align: center

* Редактирование

.. figure:: ../images/feedback/edit.png
    :align: center

Блоки
-------------------

.. code-block:: twig

    {{ sonata_block_render({
        'type': 'compo_feedback.block.service.feedback_main',
        'settings': {
            'template': 'CompoFeedbackBundle:Block:feedback_main.html.twig'
        }
    }) }}

.. figure:: ../images/feedback/block.png
    :align: center
