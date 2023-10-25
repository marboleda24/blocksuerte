#Imagen
FROM ubuntu:20.04

# Para que todos lo que instalemos sea desatendido
ENV DEBIAN_FRONTEND noninteractive

ARG XDEBUG

# Instalacion de PHP v8.2
RUN apt-get clean && apt-get -y update && apt-get install -y locales wget curl software-properties-common git \
  && locale-gen en_US.UTF-8
RUN LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php
RUN apt-get update -y
RUN apt-get upgrade -y
RUN apt-get install -y nano apt-transport-https php8.2-bcmath php8.2-bz2 php8.2-cli php8.2-common php8.2-curl \
                php8.2-cgi php8.2-dev php8.2-fpm php8.2-gd php8.2-gmp php8.2-imap php8.2-intl \
                php8.2-ldap php8.2-mbstring php8.2-mysql \
                php8.2-odbc php8.2-opcache php8.2-pgsql php8.2-phpdbg php8.2-pspell \
                php8.2-readline php8.2-soap php8.2-sqlite3 \
                php8.2-tidy php8.2-xml php8.2-xmlrpc php8.2-xsl php8.2-zip \
                php8.2-mongodb php8.2 mcrypt php-pear \
                php8.2-iconv php8.2-swoole php8.2-xdebug


RUN apt-get update && apt-get install -y mysql-client && rm -rf /var/lib/apt

RUN if [ ${XDEBUG}] ; then \
    apt-get install -y php-xdebug; \
fi;

# Prerequisitos para instalar driver OBDC para Microsoft SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list
RUN apt-get update  -y
RUN apt-get upgrade  -y
RUN ACCEPT_EULA=Y apt-get install -y msodbcsql18
RUN ACCEPT_EULA=Y apt-get install -y mssql-tools
RUN echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bash_profile
RUN echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc
RUN /bin/bash -c "source ~/.bashrc"
RUN apt-get install -y unixodbc-dev


# Configuracion de PHP v8.2
RUN pear config-set php_ini /etc/php/8.2/fpm/php.ini
RUN printf "\n" | pecl install sqlsrv
RUN printf "\n" | pecl install pdo_sqlsrv
RUN printf "; priority=20\nextension=sqlsrv.so\n" > /etc/php/8.2/mods-available/sqlsrv.ini
RUN printf "; priority=30\nextension=pdo_sqlsrv.so\n" > /etc/php/8.2/mods-available/pdo_sqlsrv.ini
RUN phpenmod -v 8.2 sqlsrv pdo_sqlsrv

RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/8.2/cli/php.ini
RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/8.2/fpm/php.ini
RUN sed -i "s/memory_limit =.*/memory_limit = 1024M/" /etc/php/8.2/fpm/php.ini
RUN sed -i "s/display_errors = Off/display_errors = Off/" /etc/php/8.2/fpm/php.ini
RUN sed -i "s/upload_max_filesize = .*/upload_max_filesize = 100M/" /etc/php/8.2/fpm/php.ini
RUN sed -i "s/post_max_size = .*/post_max_size = 100M/" /etc/php/8.2/fpm/php.ini
RUN sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/8.2/fpm/php.ini

RUN sed -i -e "s/pid =.*/pid = \/var\/run\/php8.2-fpm.pid/" /etc/php/8.2/fpm/php-fpm.conf
RUN sed -i -e "s/error_log =.*/error_log = \/proc\/self\/fd\/2/" /etc/php/8.2/fpm/php-fpm.conf
RUN sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php/8.2/fpm/php-fpm.conf
RUN sed -i "s/listen = .*/listen = 9000/" /etc/php/8.2/fpm/pool.d/www.conf
RUN sed -i "s/;catch_workers_output = .*/catch_workers_output = yes/" /etc/php/8.2/fpm/pool.d/www.conf


# Instalacion de composer
RUN curl https://getcomposer.org/installer > composer-setup.php && php composer-setup.php && mv composer.phar /usr/local/bin/composer && rm composer-setup.php
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


# Directorio de trabajo del contenedor
WORKDIR /var/www/html


# Instalacion de nodeJS y NPM
RUN apt-get update
RUN apt-get -y install curl gnupg
RUN curl -sL https://deb.nodesource.com/setup_18.x  | bash -
RUN apt-get -y install nodejs


RUN apt-get update && apt-get install -y cron supervisor

RUN mkdir -p /var/log/supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.2/mods-available/xdebug.ini


# Como se va a llamar el servicio internamente
# nota: esto es necesario para poderlo comunicar con ngnix
CMD ["/usr/bin/supervisord"]

