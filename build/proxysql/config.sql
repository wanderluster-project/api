/* Configuring replication groups */
INSERT INTO
mysql_group_replication_hostgroups (writer_hostgroup, backup_writer_hostgroup, reader_hostgroup, offline_hostgroup, active, max_writers, writer_is_also_reader, max_transactions_behind)
VALUES (2, 4, 3, 1, 1, 3, 1, 100);

/* Add child nodes */
INSERT INTO mysql_servers(hostgroup_id, hostname, port) VALUES (2, 'mysql1', 3306);
INSERT INTO mysql_servers(hostgroup_id, hostname, port) VALUES (2, 'mysql2', 3306);
INSERT INTO mysql_servers(hostgroup_id, hostname, port) VALUES (2, 'mysql3', 3306);
INSERT INTO mysql_servers(hostgroup_id, hostname, port) VALUES (3, 'mysql1', 3306);
INSERT INTO mysql_servers(hostgroup_id, hostname, port) VALUES (3, 'mysql2', 3306);
INSERT INTO mysql_servers(hostgroup_id, hostname, port) VALUES (3, 'mysql3', 3306);
LOAD MYSQL SERVERS TO RUNTIME;
SAVE MYSQL SERVERS TO DISK;

/* Add user to connect to child nodes */
INSERT INTO mysql_users(username, password, default_hostgroup) VALUES ('wanderluster_app', 'passpass', 2);
LOAD MYSQL USERS TO RUNTIME;
SAVE MYSQL USERS TO DISK;