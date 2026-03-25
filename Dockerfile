# Sử dụng image PHP có sẵn Apache
FROM php:8.2-apache

# Cài đặt các phần mở rộng cần thiết cho MySQL (nếu bạn dùng database)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy toàn bộ code từ máy bạn vào thư mục web của Docker
COPY . /var/www/html/

# Cấp quyền cho thư mục để Apache có thể đọc ghi
RUN chown -R www-data:www-data /var/www/html

# Mở cổng 80 (cổng mặc định của web)
EXPOSE 80

# Chạy Apache ở chế độ foreground
CMD ["apache2-foreground"]