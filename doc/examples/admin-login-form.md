# Изменение формы авторизации в панель управления

Что бы добавить лого на форму авторизации, нужно перегрузить шаблон, полностью, так как там отсутствует отдельный блок для лого, и прописать своё лого.

Что бы узнать, какие шаблоны грузятся, можно посмотреть в dev-панельке.
![](http://i.prntscr.com/6a5c5ab431aa489884c85206d24b8ea0.png)
![](http://i.prntscr.com/88c03c4c7480445187e96248c37db9da.png)

1. Закидываем лого, куда нибудь, например в:
/src/Compo/Sonata/CoreBundle/Resources/public/img/compo_logo.png

2. Перегружаем полностью шаблон.
Находим его: Ctrl+Shift+N
login.html.twig

![](http://i.prntscr.com/dd047da6e41c4b51a9bfaedee9f19165.png)
/vendor/sonata-project/user-bundle/Resources/views/Admin/Security/login.html.twig

Копируем в:
/src/Compo/Sonata/UserBundle/Resources/views/Admin/Security/login.html.twig

Правим.
![](http://i.prntscr.com/51143e07e25346be96f94a03d2caaa81.png)

Ctrl+Space - автодополнение. Найдёт нужный ресурс, и пропишет путь.

```twig
{{ asset('bundles/composonatacore/img/compo_logo.png') }}
```
Данный метод необходим...
В зависимости от настроек, фильтров assetic, может быть включено сжатие изображений,
генерация абсолютных/относительных путей, подстановка домена (например http://img.site.com/bundles/composonatacore/img/compo_logo.png)

