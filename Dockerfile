FROM centos:7
RUN echo 'proxy=http://192.168.1.73:3128' >> /etc/yum.conf \
 && yum install -y yum-utils http://rpms.remirepo.net/enterprise/remi-release-7.rpm \
 && yum-config-manager --enable remi-php71 \
 && yum -y install php71-php httpd mod_ssl epel-release \
 && yum -y install php71-php-mysqlnd php71-php-pecl-http php71-php-curl\ 
 && yum -y update
RUN cd /root \
 && yum -y install git zip unzip \
 && curl -x http://192.168.1.73:3128 -s http://getcomposer.org/installer | php71 \
 && php71 ~/composer.phar require "jaggedsoft/php-binance-api @dev" -d /var/www/html \
 && yum -y history undo last
EXPOSE 80
EXPOSE 443
COPY . /var/www/html/
RUN chown -R apache:apache /var/www/html/ \
 && ls -l /var/www/html/ \
 && mv /var/www/html/conf.d/* /etc/httpd/conf.d/ \
 && rm -rf /var/www/html/conf.d
CMD /usr/sbin/httpd -DFOREGROUND
