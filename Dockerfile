FROM centos:7
RUN echo 'proxy=http://192.168.1.73:3128' >> /etc/yum.conf \
 && yum install -y yum-utils http://rpms.remirepo.net/enterprise/remi-release-7.rpm \
 && yum-config-manager --enable remi-php71 \
 && yum -y install php71-php httpd epel-release \
 && yum -y install php71-php-mysqlnd php71-php-pecl-http php71-php-curl\ 
 && yum -y update
RUN cd /var/www/html \
 && yum -y install git \
 && curl -s http://getcomposer.org/installer | php71 \
 && php71 composer.phar require "jaggedsoft/php-binance-api @dev" \
 && yum -y history undo last
EXPOSE 80
COPY . /var/www/html/
RUN chown -R apache:apache /var/www/html/ \
 && ls -l /var/www/html/
CMD /usr/sbin/httpd -DFOREGROUND
