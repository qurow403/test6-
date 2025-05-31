FROM php:7.4.9-fpm

# PHP設定ファイルのコピー
COPY php.ini /usr/local/etc/php/

# 必要なパッケージとPHP拡張のインストール
RUN apt update \
    && apt install -y \
    default-mysql-client \
    zlib1g-dev \
    libzip-dev \
    unzip \
    curl \
    gnupg \
    && docker-php-ext-install pdo_mysql zip \
    # Node.js 18.x のセットアップとインストール
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt install -y nodejs \
    # Composer インストール
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update

# Node/npm バージョン確認（ログで確認用）
RUN node -v && npm -v

# 作業ディレクトリ
WORKDIR /var/www