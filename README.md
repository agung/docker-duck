<h1 align="center">DOCKER DUCK</h1>
Duck is powered docker environment for magento or any apps, it's inspired by Laravel Sail

# Installation
How to install docker-duck for your magento project <br>
This is an magento module provided for run your project

1. Create magento project in your local by using composer or download
2. Install docker-duck using composer
```
composer config repositories.docker-duck git git@github.com:agung/docker-duck.git
composer require "agung/docker-duck":"dev-magento" --dev
```
3. Create aliases duck shell command in your `.bashrc` or `.zshrc`
```
alias duck='[ -f duck ] && bash duck || bash vendor/agung/docker-duck/bin/duck'
```
4. For generate docker compose file run `php bin/magento duck:compose:install`
5. Create `.env` file in your root project, the environment variable should be contain MYSQL, see environment variable in `docker-compose.yml`
