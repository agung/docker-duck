# DUCK
Duck is powered docker environment for magento or any apps, it's inspired by Laravel Sail

# Installation
1. Clone this repo `git clone git@github.com/agung/docker-duck .`
2. Edit .env file inside docker directory, change PHP_VERSION. Currently only support 2 php version 7.4 and 8.0
3. Create aliases to .zshrc or .bashrc `alias duck='[ -f duck ] && bash duck || bash docker/bin/duck'`
4. Run command `duck build` for build an image
5. Run command `duck start` for start container and `docker-sync`, `duck sync-stop` for stop docker-sync
6. Open `http://localhost.test`

# Customization