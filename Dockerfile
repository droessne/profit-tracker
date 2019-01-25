FROM centos:7
RUN echo 'proxy=http://192.168.1.73:3128' >> /etc/yum.conf \
 && yum install -y yum-utils http://rpms.remirepo.net/enterprise/remi-release-7.rpm \
 && yum-config-manager --enable remi-php56 \
 && yum -y install php httpd epel-release \
 && yum -y install php-mysqlnd \ 
 && yum -y update
EXPOSE 80
COPY . /var/www/html/
RUN chown -R apache:apache /var/www/html/ \
 && ls -l /var/www/html/
CMD /usr/sbin/httpd -DFOREGROUND
