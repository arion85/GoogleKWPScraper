# GoogleKWPScraper

#### Автоматический граббер keywords и значений для них с Google Adwords Keyword Planner для заданного ключа

***

На данный момент для работы парсера необходимы две БД:

1. MariaDB (MySQL) - для хранения прокси-серверов, гугл аккаунтов, настроек парсера
2. Redis - для хранения полученной информации

Для запуска парсера должны быть установлены на сервере следующие программы:

1. Apache, PHP
2. Phantomjs (v. >=2.1.1) http://phantomjs.org/build.html
3. OpenSSL (v. 1.0.2) https://packages.debian.org/sid/amd64/libssl1.0.2/download
4. Casperjs http://docs.casperjs.org/en/latest/installation.html
5. PHP module php-redis https://github.com/phpredis/phpredis

**I'm upgrading parser now. To be continued...**