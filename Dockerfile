FROM php:8.2-apache

# 1. Cài đặt các thư viện hệ thống và PHP extension cho Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install mysqli pdo pdo_mysql mbstring

# 2. Bật mod_rewrite của Apache (Quan trọng để Laravel chạy route)
RUN a2enmod rewrite

# 3. Thay đổi DocumentRoot của Apache trỏ vào thư mục /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/htdocs!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Copy code vào container
COPY . /var/www/html/

# 5. Cấp quyền cho thư mục storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80