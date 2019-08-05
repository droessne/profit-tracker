#!/bin/bash

unset brokers
#host="gn400.whpservers.com"
host="127.0.0.1"
user="dersllcc_money"
psw="DERS4me"
database="dersllcc_profit_tracker"
query="select broker_id from brokers"
active_broker=$(echo "SELECT active_broker_id FROM defaults" | /usr/local/bin/mysql -h ${host} -u${user} -p${psw} ${database} | tail -1)

#cd /tmp/profit-tracker/
#sed -i 's/127.0.0.1/gn400.whpservers.com/' include/database.cfg.php

while read line
do 
    if [ $line != "broker_id" ]
	then
    	    brokers=$brokers" "$line
    fi
done < <(mysql -h ${host} -u${user} -p${psw} ${database} -e "${query}")

for b in $brokers
do
  query2="UPDATE defaults SET active_broker_id = '$b' WHERE id = 1"
  #/usr/local/bin/mysql -h ${host} -u${user} -p${psw} ${database} -e "${query2}"
  /usr/bin/mysql -h ${host} -u${user} -p${psw} ${database} -e "${query2}"
  /usr/local/bin/php /home2/dersllcc/public_html/home/uploads/money/view_open2.php
done

query2="UPDATE defaults SET active_broker_id = '$active_broker' WHERE id = 1"
/usr/local/bin/mysql -h ${host} -u${user} -p${psw} ${database} -e "${query2}"
