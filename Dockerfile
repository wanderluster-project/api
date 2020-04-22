# Pull base image.
FROM ubuntu:18.04

# Dependencies
RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get update && \
    apt -y upgrade && \
    apt-get install -y curl wget gzip git zip && \
    apt-get install -y apache2 && \
    apt-get install -y php libapache2-mod-php php-mysql php-dom php-xml php-mbstring php-intl && \
    rm -Rf /var/www/html/* && \
    rmdir /var/www/html && \
    ln -s /var/www/wanderluster/public /var/www/html


# Dev Dependencies
RUN apt-get install -y php7.2-phpdbg && \
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
    ln -s /var/www/wanderluster/var/storage /var/www/wanderluster/public/storage

WORKDIR /var/www/wanderluster
EXPOSE 80
CMD apachectl -D FOREGROUND