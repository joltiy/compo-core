# Git. SSH.

Необходимо наличие GitHub аккаунта для доступа к приватным репозиториям, если разработчик.

## Git

### For Windows:

http://babun.github.io/


Отредактировать ZSH профиль:

```
nano ~/.zshrc
```

В конце добавить, для смены текущий директории, на директорию в которой запущен zsh:

```
cd $OLDPWD

ИЛИ

cd -
```

Заменить `plugins` на:

```
plugins=(symfony2 ssh-agent history-substring-search git complete)
```
Эти плагины, реализуют автодополенение Symfony команд, поиск и навигация по истории команд, алиасы и что-то ещё для git

Подробнее, https://github.com/robbyrussell/oh-my-zsh
Можно сменить тему, или подключить плагины...

Добавить в конце, для автодоплнения аргументов коамнд Symfony^

```
php app/console _completion --generate-hook --shell-type=zsh | source /dev/stdin
```


### For Ubuntu
```apt-get install git zsh```

ZSH: https://github.com/robbyrussell/oh-my-zsh

## Collaborators

Разработчиков необходимо добавить как Collaborators, в репозиторий ядра и репозитории проектов:
https://github.com/comporu/compo/settings/collaboration


## SSH ключи

Сгенерировать SSH ключи, если отсутствуют:
https://help.github.com/articles/generating-a-new-ssh-key/
https://help.github.com/articles/adding-a-new-ssh-key-to-the-ssh-agent/#platform-windows

Passphrase - необязателен.

```
ssh-keygen -t rsa -b 4096 -C "USER@gmail.com"
chmod 0600 /home/USER/.ssh/id_rsa
chmod 0644 /home/USER/.ssh/id_rsa.pub
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_rsa
cat /home/USER/.ssh/id_rsa.pub
```

![](http://i.imgur.com/nQLpYqu.png)

## GitHub

```
git config --global user.name "USER"
git config --global user.email "USER@gmail.com"
```

**Если разработчик**

Добавить свой public key, в свой GitHub аккаунт:
https://github.com/settings/ssh


**Если сервер**

Добавить public key в Deploy keys репозиториев, без возможности или с возможностью, делать push.

В репозиторий ядра и репозитории проектов:
https://github.com/comporu/compo/settings/keys


### Проверить

```ssh -T git@github.com```

![](http://i.imgur.com/XQAJSNj.png)