FROM ubuntu:21.04

LABEL maintainer="Agung Nugraha <nugraha.an96@gmail.com>"

ARG WWWGROUP

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 nginx nano \
    && mkdir -p ~/.gnupg \
    && chmod 600 ~/.gnupg \
    && echo "disable-ipv6" >> ~/.gnupg/dirmngr.conf \
    && apt-key adv --homedir ~/.gnupg --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys E5267A6C \
    && apt-key adv --homedir ~/.gnupg --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys C300EE8C \
    && echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu hirsute main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y php7.2-cli php7.2-dev php7.2-fpm \
       php7.2-pgsql php7.2-sqlite3 php7.2-gd \
       php7.2-curl php7.2-memcached \
       php7.2-imap php7.2-mysql php7.2-mbstring \
       php7.2-xml php7.2-zip php7.2-bcmath php7.2-soap \
       php7.2-intl php7.2-readline php7.2-pcov \
       php7.2-msgpack php7.2-igbinary php7.2-ldap \
       php7.2-redis php7.2-swoole php7.2-xdebug \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer --version=1.10.22 \
    && apt-get install -y mysql-client \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install ioncube
RUN cd /tmp \
    && curl -O https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_aarch64.tar.gz \
    && tar zxvf ioncube_loaders_lin_aarch64.tar.gz \
    && export PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;") \
    && export PHP_EXT_DIR=$(php-config --extension-dir) \
    && cp "./ioncube/ioncube_loader_lin_${PHP_VERSION}.so" "${PHP_EXT_DIR}/ioncube.so" \
    && echo "[PHP]\nzend_extension = ${PHP_EXT_DIR}/ioncube.so" >> /etc/php/7.2/cli/conf.d/00-ioncube.ini \
    && rm -rf ./ioncube \
    && rm ioncube_loaders_lin_aarch64.tar.gz \
    && rm -rf /tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php7.2

RUN groupadd --force -g $WWWGROUP duck
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 duck

COPY start-container /usr/local/bin/start-container
COPY default.conf /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/7.2/cli/conf.d/00-duck.ini
COPY php.ini /etc/php/7.2/fpm/conf.d/00-duck.ini
COPY www.conf /etc/php/7.2/fpm/pool.d/www.conf
RUN chmod +x /usr/local/bin/start-container

EXPOSE 80 8000 9000

ENTRYPOINT ["start-container"]
