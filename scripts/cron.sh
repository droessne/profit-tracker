#!/bin/bash

unset brokers
host="gn400.whpservers.com"
user="dersllcc_money"
psw="DERS4me"
database="dersllcc_profit_tracker"
query="select broker_id from brokers"

cd /tmp/profit-tracker/
sed -i 's/127.0.0.1/gn400.whpservers.com/' include/database.cfg.php

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
  mysql -h ${host} -u${user} -p${psw} ${database} -e "${query2}"
  php71 view_open2.php
done

query2="UPDATE defaults SET active_broker_id = '1' WHERE id = 1"
mysql -h ${host} -u${user} -p${psw} ${database} -e "${query2}"
