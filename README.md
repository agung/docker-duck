<h1 align="center">DOCKER DUCK</h1>
Duck is powered docker environment for magento or any apps, it's inspired by <a href="https://github.com/laravel/sail">Laravel Sail</a>.

# Installation
How to install docker-duck for your magento project <br>
This is an magento module provided for run your project

1. Create magento project in your local by using composer or download
2. Install docker-duck using composer
```
composer config repositories.docker-duck git git@github.com:agung/docker-duck.git
composer require "docker-duck/compose":"^1.0.0" --dev
```
3. Create aliases duck shell command in your `.bashrc` or `.zshrc`
```
alias duck='[ -f duck ] && bash duck || bash vendor/docker-duck/compose/bin/duck'
```
4. For generate docker compose file run `php bin/magento duck:compose:install`
5. Update database config in `.env` file
6. Create installation magento by command
```
duck php bin/magento setup:install --base-url=http://magento.local \
--db-host={{db service}} --db-name=magento --db-user=duck --db-password=duck \
--admin-firstname=Agung --admin-lastname=Nugraha --admin-email=nugraha.an96@gmail.com \
--admin-user=nugraha --admin-password=Password123 --language=en_US \
--currency=IDR --timezone=Asia/Jakarta --use-rewrites=1
```
