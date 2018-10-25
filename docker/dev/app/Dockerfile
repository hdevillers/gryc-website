FROM php:7.2.5-fpm

# PHP_CPPFLAGS is used by the docker-php-ext-* scripts (avoid bug during compilation)
ENV MAFFT_VERSION=7.313 \
    BLAST_VERSION=2.7.1 \
    NODE_VERSION=10.1.0 \
    PHP_CPPFLAGS="$PHP_CPPFLAGS -std=c++11" \
    COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Install packages dependencies
RUN set -ex; \
    \
    apt-get update -q; \
    apt-get install -qy --no-install-recommends \
            zlib1g-dev \
            git \
            supervisor \
    ; \
    # Compile ICU (required by intl php extension)
    curl -sS -o /tmp/icu.tar.gz -L http://download.icu-project.org/files/icu4c/59.1/icu4c-59_1-src.tgz; \
    tar -zxf /tmp/icu.tar.gz -C /tmp; \
    cd /tmp/icu/source ; \
    ./configure --prefix=/usr/local; \
    make clean; \
    make ; \
    make install; \
    # Install the PHP extensions
    \
    docker-php-source extract; \
    docker-php-ext-configure intl --with-icu-dir=/usr/local; \
    docker-php-ext-install  -j "$(nproc)" \
            intl \
            pdo \
            pdo_mysql \
            zip \
            bcmath \
    ; \
    pecl install \
            apcu-5.1.8 \
    ; \
    docker-php-ext-enable \
            opcache \
            apcu \
    ; \
    docker-php-source delete; \
    \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*;\
    curl -SS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && chmod +x /usr/local/bin/composer

# Install BLAST
RUN set -ex; \
    \
    curl -L -o /tmp/ncbi-blast-${BLAST_VERSION}+-x64-linux.tar.gz http://gryc.inra.fr/download/ncbi-blast-${BLAST_VERSION}+-x64-linux.tar.gz; \
    tar -zxf /tmp/ncbi-blast-${BLAST_VERSION}+-x64-linux.tar.gz -C /tmp; \
    mv /tmp/ncbi-blast-${BLAST_VERSION}+/bin/* /usr/local/bin; \
    rm -R /tmp/*

# Install MAFFT
RUN set -ex; \
    \
    curl -L -o /tmp/mafft-${MAFFT_VERSION}-without-extensions-src.tgz https://mafft.cbrc.jp/alignment/software/mafft-${MAFFT_VERSION}-without-extensions-src.tgz; \
    tar -zxf /tmp/mafft-${MAFFT_VERSION}-without-extensions-src.tgz -C /tmp; \
    cd /tmp/mafft-${MAFFT_VERSION}-without-extensions/core; \
    make clean; \
    make; \
    make install; \
    rm -R /tmp/*

# Install Node
RUN set -ex; \
    curl -L -o /tmp/nodejs.tar.gz https://nodejs.org/dist/v${NODE_VERSION}/node-v${NODE_VERSION}-linux-x64.tar.gz && \
    tar xfvz /tmp/nodejs.tar.gz -C /usr/local --strip-components=1 && \
    rm -f /tmp/nodejs.tar.gz && \
    npm install yarn -g

COPY php.ini /usr/local/etc/php/
COPY php-cli.ini /usr/local/etc/php/
COPY supervisord.conf /etc/supervisor/conf.d/

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
