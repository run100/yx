#FROM registry.cn-hangzhou.aliyuncs.com/wanjia/phprunner:7.1-noalpine
FROM wjdocker.lab/wanjia/phprunner:7.1-noalpine

RUN  pecl install redis && \
     docker-php-ext-enable redis

RUN  apt-get install -y libpcre3-dev re2c automake
RUN  docker-php-ext-install pcntl sockets soap xsl pdo_mysql mysqli exif

RUN  pecl install apcu && \
     docker-php-ext-enable apcu

RUN  apt-get install -y libtidy-dev && docker-php-ext-install tidy

COPY imagick-3.4.3.tgz /usr/local/src/phpext/
RUN  apt-get install -y libmagickwand-dev  && \
     cd /usr/local/src/phpext && \
     tar zxvfp imagick-3.4.3.tgz && \
     cd /usr/local/src/phpext/imagick-3.4.3 && \
     phpize && \
     ./configure  && \
     make install && \
     docker-php-ext-enable imagick

RUN  docker-php-ext-configure bcmath --enable-bcmath && docker-php-ext-install bcmath

COPY Excel-1.0.2-PHP7.zip /usr/local/src/phpext/
COPY libxl-lin-3.8.0.tar.gz /usr/local/src/phpext/
RUN  cd /usr/local/src/phpext && \
     tar zxvfp libxl-lin-3.8.0.tar.gz && unzip Excel-1.0.2-PHP7.zip && \
     cd /usr/local/src/phpext/php_excel-Excel-1.0.2-PHP7 && \
     phpize && \
     ./configure --with-excel --with-libxl-incdir=/usr/local/src/phpext/libxl-3.8.0.0/include_c --with-libxl-libdir=/usr/local/src/phpext/libxl-3.8.0.0/lib64 --with-libxml-dir=/usr/include/libxml2 && \
     make install && \
     docker-php-ext-enable excel

RUN  pecl install msgpack && \
     docker-php-ext-enable msgpack

COPY yar-2.0.4.tgz /usr/local/src/phpext/
RUN  cd /usr/local/src/phpext && \
     tar zxvfp yar-2.0.4.tgz && \
     cd /usr/local/src/phpext/yar-2.0.4 && \
     phpize && \
     ./configure --enable-msgpack && \
     make install && \
     docker-php-ext-enable yar

RUN  luarocks install lua-messagepack

RUN  pecl install xdebug

RUN  apt update && apt install -y openjdk-7-jdk

RUN  mkdir /var/log/app /var/log/nginx /var/log/php && chmod a+rw /var/log/app /var/log/nginx /var/log/php

COPY ssh_config /root/.ssh/config
RUN  chmod -R 700 /root/.ssh && \
     chmod -R 600 /root/.ssh/config
COPY ssh_known_hosts /root/.ssh/known_hosts
COPY app_check /usr/local/bin/
COPY app_init /usr/local/bin/
COPY zz-docker.conf /usr/local/etc/php-fpm.d/
COPY crontab /var/spool/cron/crontabs/root
RUN  chown -R root:crontab /var/spool/cron/crontabs/root \
     && chmod 600 /var/spool/cron/crontabs/root
RUN  touch /var/log/cron.log

WORKDIR /var/www

