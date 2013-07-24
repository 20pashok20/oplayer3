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
<<<<<<< HEAD
Скачайте дистрибутив oplayer (https://github.com/uavn/oplayer3), распакуйте и залейте через FTP (или SSH) на ваш хостинг в каталог соответствующий домену. Для этого воспользуйтесь любым FTP-клиентом (FileZilla, mc). При этом, в настройках (виртуалхостах) нужно направить в папку web сайта.<br/>
<br/>
Пример виртуалхоста apache2:<br/>
&lt;VirtualHost *:80&gt;<br/>
  ServerName mysuperplayer.com<br/>
  DocumentRoot /var/www/oplayer3/web<br/>
  &lt;Directory /var/www/oplayer3/web&gt;<br/>
    AllowOverride All<br/>
  &lt;/Directory&gt;<br/>
&lt;/VirtualHost&gt;<br/>
<br/>
**Права на папку cache должны быть 777**<br/>
=======
Скачайте дистрибутив oplayer (https://github.com/uavn/oplayer3), распакуйте и залейте через FTP (или SSH) на ваш хостинг в каталог соответствующий домену. Для этого воспользуйтесь любым FTP-клиентом (FileZilla, mc). При этом, в настройках (виртуалхостах) нужно направить в папку web сайта.

Пример виртуалхоста apache2:
<code>
<VirtualHost *:80>
  ServerName mysuperplayer.com
  DocumentRoot /var/www/oplayer3/web
  <Directory /var/www/oplayer3/web>
    AllowOverride All
  </Directory>
</VirtualHost>
</code>

**Права на папку cache должны быть 777**
>>>>>>> 800c854cfa755816c6fd24172de94f974d441dab

Файл oplayer.sql — это дамп базы данных, **которую нужно развернуть** на своем MySQL сервере.
Если у вас установлен PHPMyAdmin, вам нужно зайти на вкладку Import, выбрать файл oplayer.sql и нажать кнопку "Go".
![PHPMyAdmin](http://dl.dropbox.com/u/10902867/blog/pma.png)

Откройте файл app/Confid/app.ini любым текстовым редактором.
Здесь нужно указать данные доступа к вашему аккаунту вконтакте а также ID StandAlone приложения, через которое он будет работать (Создать можно здесь — http://vk.com/dev).

<<<<<<< HEAD
В файле app/Config/build.properties нужно указать настройки доступа к базе данных:<br/>
propel.database.url = mysql:host=localhost;dbname=НАЗВАНИЕ_БАЗЫ_ДАННЫХ;charset=utf8<br/>
propel.database.user = ИМЯ_ПОЛЬЗОВАТЕЛЯ_БАЗЫ_ДАННЫХ<br/>
propel.database.password = ПАРОЛЬ<br/>
Настройки для доступа к базе данных можно посмотреть, например, в панели управления хостингом, или уточнить в тех.поддержке или системного администратора.<br/>
<br/>
Если всё было настроено правильно по ссылке должен быть доступен плеер.<br/>
<br/>
Подробности на http://bonart.org.ua/oplayer3
=======
В файле app/Config/build.properties нужно указать настройки доступа к базе данных:
propel.database.url = mysql:host=localhost;dbname=НАЗВАНИЕ_БАЗЫ_ДАННЫХ;charset=utf8
propel.database.user = ИМЯ_ПОЛЬЗОВАТЕЛЯ_БАЗЫ_ДАННЫХ
propel.database.password = ПАРОЛЬ
Настройки для доступа к базе данных можно посмотреть, например, в панели управления хостингом, или уточнить в тех.поддержке или системного администратора. 

Если всё было настроено правильно по ссылке должен быть доступен плеер. 

Подробности на http://bonart.org.ua/oplayer3
>>>>>>> 800c854cfa755816c6fd24172de94f974d441dab
