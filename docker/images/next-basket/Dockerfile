FROM php:8.1.4-apache
MAINTAINER Nikola Kozhuharov <nikolakozhuharovv@gmail.com>

RUN apt-get update && \
    apt-get install -y \
    vim libxml2-dev libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    zlib1g-dev \
    libgeoip-dev \
    gnupg \
    wget \
    zip \
    mc \
    htop \
    gsfonts \
    curl \
    openssh-server

RUN apt-get update &&\
    apt-get install -y --no-install-recommends gnupg &&\
    curl -sL https://deb.nodesource.com/setup_14.x | bash - &&\
    apt-get update &&\
    apt-get install -y --no-install-recommends nodejs

RUN apt-get install -y --no-install-recommends libzip-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) zip mysqli soap gd pdo pdo_mysql opcache
RUN docker-php-ext-enable opcache

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp

RUN echo 'date.timezone = "Europe/Sofia"' > /usr/local/etc/php/php.ini
RUN echo 'short_open_tag = 0' > /usr/local/etc/php/php.ini

RUN echo "<VirtualHost *:80>" > /etc/apache2/sites-available/next-basket.conf \
    && echo "ServerName local.next-basket.com" >> /etc/apache2/sites-available/next-basket.conf \
    && echo "ServerAlias local.next-basket.com" >> /etc/apache2/sites-available/next-basket.conf \
    && echo "UseCanonicalName On" >> /etc/apache2/sites-available/next-basket.conf \
    && echo "DocumentRoot /var/www/next-basket/public" >> /etc/apache2/sites-available/next-basket.conf \
    && echo "</VirtualHost>" >> /etc/apache2/sites-available/next-basket.conf

RUN yes | pecl install xdebug
RUN echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo 'xdebug.client_port="9003"' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo 'xdebug.idekey="PHPSTORM"' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo 'xdebug.mode=debug' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo 'xdebug.discover_client_host=1' >> /usr/local/etc/php/conf.d/xdebug.ini

# Configure Apache
RUN a2dissite 000-default
RUN a2ensite next-basket
RUN a2enmod rewrite
RUN a2enmod authn_dbm
RUN a2enmod authn_anon
RUN a2enmod authn_dbd
RUN a2enmod authz_groupfile
RUN a2enmod authz_dbm
RUN a2enmod authz_owner
RUN a2enmod auth_digest
RUN a2enmod file_cache
RUN a2enmod reqtimeout
RUN a2enmod ext_filter
RUN a2enmod include
RUN a2enmod substitute
RUN a2enmod log_forensic
RUN a2enmod mime_magic
RUN a2enmod expires
RUN a2enmod headers
RUN a2enmod ident
RUN a2enmod usertrack
RUN a2enmod unique_id
RUN a2enmod proxy
RUN a2enmod proxy_connect
RUN a2enmod proxy_ftp
RUN a2enmod proxy_http
RUN a2enmod proxy_scgi
RUN a2enmod proxy_ajp
RUN a2enmod proxy_balancer
RUN a2enmod ssl
RUN a2enmod dav
RUN a2enmod asis
RUN a2enmod info
RUN a2enmod cgi
RUN a2enmod dav_fs
RUN a2enmod vhost_alias
RUN a2enmod actions
RUN a2enmod speling
RUN a2enmod userdir

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN echo "alias ll='ls -lah'" >> /root/.bashrc
