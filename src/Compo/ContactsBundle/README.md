ContactsBundle Back end 
===========
*ContactsBundle Относится к compo-core и находится в vendor/comporu/compo-core/src/Compo/ContactsBundle* 

Реализует страницу xxx.xxx/contacts на базе pageBundle
которая несет в себе 2 блока - контакты и форму обратной связи.

Бандл представляет из себя:

1. Rest Api контроллер `Compo\ContactsBundle\Controller\Api`
   который получает форму в формате JSON и возвращает результат
   в JSON 
   
2. Модуля для администрирования контактов

3. Простой фикстуры, которая при первой установке заполняет запись о контактах. `Сompo\ContactsBundle\DataFixtures\ORM`
   
4. Формы `FeedbackFormType`
   
5. Сервиса ContactsManager - который используется в pageBundle и который отдает 
   единственную запись о настройках контактов   
   
6. 2х сущностей Feedback и Contacts 

   `Feedback` - хранит все обращения по форме обратной связи
   
   `Contacts` - хранит настройки для страницы контактов. 


ContactsBundle Front end 
================  
1. View бандла содержит блок контактов, форму обратной связи, и два почтовых шаблона

2. Валидация формы, маска, отправка формы - осуществляется с помощью Angular 1.6 приложения

Которое состоит из:
  1. Приложения app.js
  2. Сервиса ContactsApi
  3. Контроллера FeedbackForm.controller который выполняет передачу формы в back end
  4. Директивы, которая осуществляет установку маски на поле телефона
  5. Инжектирован `"angular-validation": "^1.4.3"` - который осуществляет валидацию данных на клиенте
  6. в bower.json указаны зависимости angular-приложения      
  
  
Пример: http://mirplitki.compodev24.ru/contacts