FROM php:8.2-cli

WORKDIR /app

# Install PostgreSQL support for Neon DB
RUN docker-php-ext-install pdo pdo_pgsql

# Copy your PHP files
COPY index.php .

EXPOSE 8000

# Start PHP server
CMD ["php", "-S", "0.0.0.0:8000", "index.php"]
