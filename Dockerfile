FROM php:8.2-apache

# 1. Cài đặt các thư viện hệ thống và PHP extension cho Laravel
# (Đã thêm 'git' vì Composer đôi khi cần nó để tải thư viện)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git

RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install mysqli pdo pdo_mysql mbstring

# 2. Cài đặt Composer (Công cụ quản lý thư viện của PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Bật mod_rewrite của Apache (Quan trọng để Laravel chạy route)
RUN a2enmod rewrite

# 4. Thay đổi DocumentRoot của Apache trỏ vào thư mục /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/htdocs!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Set thư mục làm việc mặc định
WORKDIR /var/www/html/

# 6. Copy code vào container
COPY . /var/www/html/

# 7. Tải toàn bộ thư viện Laravel (Giải quyết triệt để lỗi vendor/autoload.php)
RUN composer install --no-dev --optimize-autoloader

# 8. Cấp quyền cho thư mục storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. TẠO FILE KHỞI ĐỘNG GỘP (Bí quyết cho gói Render Free)
# File này sẽ chạy ngầm queue worker và đồng thời bật Apache
RUN echo '#!/bin/bash\n\
echo "Khởi động bộ xử lý Hàng đợi (Queue Worker)..."\n\
php artisan queue:work --tries=3 &\n\
echo "Khởi động Web Server (Apache)..."\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

EXPOSE 80

# 10. Chạy file khởi động khi Render deploy
CMD ["/start.sh"]