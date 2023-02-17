# Use an official PHP runtime as a parent image
FROM php:8.2-apache

# Install any needed packages
RUN apt-get update && \
    apt-get install -y \
        git \
        libzip-dev \
        unzip

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    zip

# Copy the project files into the container
COPY . /var/www/html

# Set up the document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Expose the Apache port
EXPOSE 80