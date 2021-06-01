#!/bin/sh

/usr/bin/mysqld_safe --skip-grant-tables &

sleep 5

mysql -u root -e "CREATE DATABASE 3wa_final"
mysql -u root 3wa_final < /tmp/install.sql