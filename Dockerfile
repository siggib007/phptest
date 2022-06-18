FROM php:7.2-apache
LABEL author="Siggi Bjarnason <siggi@supergeek.us>"
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN pecl install xdebug \
  && docker-php-ext-enable xdebug
COPY xdebug-php.ini $PHP_INI_DIR/conf.d/