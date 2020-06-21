#!/bin/bash
#mysql -hproxysql -P6032 -uradmin -pradmin < /var/www/wanderluster/build/proxysql/config.sql

echo "Configuring mysql1"
mysql_config_editor set --login-path=mysql1 --host=mysql1 --user=root --password
for i in {001..025}
do
   export DB_NUM=${i}
   envsubst < /var/www/wanderluster/build/mysql/config.sql >> /tmp/db1.sql
   mysql --login-path=mysql1 < /tmp/db1.sql
   unlink /tmp/db1.sql
done

echo "Configuring mysql2"
mysql_config_editor set --login-path=mysql2 --host=mysql2 --user=root --password
for i in {026..051}
do
   export DB_NUM=${i}
   envsubst < /var/www/wanderluster/build/mysql/config.sql >> /tmp/db2.sql
   mysql --login-path=mysql2 < /tmp/db2.sql
   unlink /tmp/db2.sql
done

echo "Configuring mysql3"
mysql_config_editor set --login-path=mysql3 --host=mysql3 --user=root --password
for i in {052..077}
do
   export DB_NUM=${i}
   envsubst < /var/www/wanderluster/build/mysql/config.sql >> /tmp/db3.sql
   mysql --login-path=mysql3 < /tmp/db3.sql
   unlink /tmp/db3.sql
done

echo "Configuring mysql4"
mysql_config_editor set --login-path=mysql4 --host=mysql4 --user=root --password
for i in {078..104}
do
   export DB_NUM=${i}
   envsubst < /var/www/wanderluster/build/mysql/config.sql >> /tmp/db4.sql
   mysql --login-path=mysql4 < /tmp/db4.sql
   unlink /tmp/db4.sql
done