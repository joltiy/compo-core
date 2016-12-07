# Server

https://notepad-plus-plus.org/

Disable autoupdate.

## XAMPP

https://www.apachefriends.org/ru/index.html

### Config

Change "Editor".
Set "Autostart": Apache, MySQL.
Set "Start Control Panel Minimized"

### PATH

```;C:\xampp\mysql\bin;C:\xampp\php;```

![](http://i.imgur.com/wcvyLlF.png)

## PHP

`C:\xampp\php\php.ini`


```
memory_limit=256M
post_max_size=128M
upload_max_filesize=128M

max_execution_time=600
max_input_time=600

default_charset = "utf-8"
request_order = "GPC"
short_open_tag = 1

error_reporting = -1
display_errors = 1
log_errors = 1
ignore_repeated_errors = 1

```

## PHP Расширения

```
extension=php_intl.dll
```