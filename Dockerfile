FROM php:7.2-apache
MAINTAINER Siggi Bjarnason <siggi@supergeek.us>
RUN docker-php-ext-install pdo pdo_mysql mysqli
