# Pull base image.
FROM ubuntu:20.04

# Dependencies
RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get update --fix-missing && \
    apt -y upgrade && \
    apt-get install -y curl wget gzip git zip && \
    apt-get install -y apache2 && \
    apt-get install -y php libapache2-mod-php php-mysql php-dom php-xml php-mbstring php-intl && \
    rm -Rf /var/www/html/* && \
    rmdir /var/www/html && \
    ln -sfn /var/www/wanderluster/public /var/www/html && \
    apt-get install -y libsodium-dev php-pear php-dev && \
    pecl install libsodium && \
    echo 'extension=sodium.so' >>  /etc/php/7.4/apache2/php.ini

# Copy data into container
COPY . /var/www/wanderluster

# Dev Dependencies
RUN apt-get install -y php7.4-phpdbg vim && \
    wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony && \
    git config --global user.email "simpkevin@gmail.com" && \
    git config --global user.name "Kevin Simpson" && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'e0012edf3e80b6978849f5eff0d4b4e4c79ff1609dd1e613307e16318854d24ae64f26d17af3ef0bf7cfb710ca74755a') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/bin/composer && \
    chmod +x /usr/bin/composer && \
    mkdir -p /var/www/wanderluster/bin && \
    wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O /var/www/wanderluster/bin/php-cs-fixer && \
    chmod +x /var/www/wanderluster/bin/php-cs-fixer && \
    ln -sfn /var/www/wanderluster/var/storage /var/www/wanderluster/public/storage

WORKDIR /var/www/wanderluster
EXPOSE 80
CMD apachectl -D FOREGROUND