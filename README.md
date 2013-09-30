oplayer3 v3
========

![OpenPlayer v3](http://cs312821.vk.me/v312821696/1dc6/J9khmkOQ494.jpg)

# Что нового в v3:
<br/>
Учет последних ихменений в API вконтакте;<br/>
Обработка капчи;<br/>
Новая архитектура движка, теперь код максимально
близок к «идеалу», без излишеств;<br/>
Полностью новый дизайн;<br/>
Новая и простая система кеширования;<br/>

# Установка:
Скачайте дистрибутив oplayer (https://github.com/uavn/oplayer3), распакуйте и залейте через FTP (или SSH) на ваш хостинг в каталог соответствующий домену. Для этого воспользуйтесь любым FTP-клиентом (FileZilla, mc). При этом, в настройках (виртуалхостах) нужно направить в папку web сайта.<br/>
<br/>
Отдельно скачайте зависимости по ссылке http://ge.tt/2EwytSt и распакуйте в папку vendor проекта, или выполните команду:<br/>
«php composer.phar install» из консоли в корне проекта.<br/>
<br/>

Направьте веб-сервер в каталог web проекта, пример виртуалхоста apache2:<br/>
&lt;VirtualHost *:80&gt;<br/>
  ServerName mysuperplayer.com<br/>
  DocumentRoot /var/www/oplayer3/web<br/>
  &lt;Directory /var/www/oplayer3/web&gt;<br/>
    AllowOverride All<br/>
  &lt;/Directory&gt;<br/>
&lt;/VirtualHost&gt;<br/>
<br/>
**Права на папку cache должны быть 777**<br/>

Файл oplayer.sql — это дамп базы данных, **которую нужно развернуть** на своем MySQL сервере.
Если у вас установлен PHPMyAdmin, вам нужно зайти на вкладку Import, выбрать файл oplayer.sql и нажать кнопку "Go".
![PHPMyAdmin](http://dl.dropbox.com/u/10902867/blog/pma.png)

Откройте файл app/Confid/app.ini любым текстовым редактором.
Здесь нужно указать данные доступа к вашему аккаунту вконтакте а также ID StandAlone приложения, через которое он будет работать (Создать можно здесь — http://vk.com/dev).

В файле app/Config/build.properties нужно указать настройки доступа к базе данных:<br/>
propel.database.url = mysql:host=localhost;dbname=НАЗВАНИЕ_БАЗЫ_ДАННЫХ;charset=utf8<br/>
propel.database.user = ИМЯ_ПОЛЬЗОВАТЕЛЯ_БАЗЫ_ДАННЫХ<br/>
propel.database.password = ПАРОЛЬ<br/>
Настройки для доступа к базе данных можно посмотреть, например, в панели управления хостингом, или уточнить в тех.поддержке или системного администратора.<br/>
<br/>
Если всё было настроено правильно по ссылке должен быть доступен плеер.<br/>
<br/>
Подробности на http://bonart.org.ua/oplayer3
