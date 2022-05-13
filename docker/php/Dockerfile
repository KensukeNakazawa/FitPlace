FROM php:7.4.16-fpm-buster

COPY php.ini /usr/local/etc/php

RUN apt update
#RUN apt-get shadow
# bash
RUN docker-php-ext-install pdo_mysql
RUN apt-get install -y zip unzip

RUN apt-get install -y git

# node のインストール npm のため
RUN apt install -y nodejs npm
RUN npm install n -g
RUN n 12.14

# redis
#RUN git clone -b php8 https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis
RUN pecl install redis &&\
    docker-php-ext-enable redis

#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://getcomposer.org/installer | php -- --version=1.10.15 --install-dir=/usr/local/bin --filename=composer
ARG PUID=1000
ARG PGID=1000
ARG USER_NAME="user"

ENV PUID ${PUID}
ENV PGID ${PGID}

# linuxコマンドを参照すること
# 新規グループを作成する
RUN groupadd -g ${PGID} ${USER_NAME}
# 新規ユーザーを作成し、グループに入れ、ユーザーのHomeディレクトリが無ければ作成する
RUN useradd -u ${PUID} -g ${USER_NAME} -m ${USER_NAME}
# ユーザーのパスワードとシェルを指定
RUN usermod -p "*" ${USER_NAME} -s /bin/sh

# path の設定
USER ${USER_NAME}
#RUN composer global require "laravel/installer" && composer global require "phpunit/phpunit"
ENV PATH $PATH:/home/${USER_NAME}/.composer/vendor/bin

WORKDIR /var/www/app