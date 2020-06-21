# noinspection SqlNoDataSourceInspectionForFile

CREATE DATABASE IF NOT EXISTS wanderluster_$DB_NUM;
CREATE USER IF NOT EXISTS 'wanderluster_app'@'%' IDENTIFIED BY 'passpass';
GRANT ALL PRIVILEGES on wanderluster_$DB_NUM.* to 'wanderluster_app'@'%';
FLUSH PRIVILEGES;

/** CREATE TABLES */
CREATE TABLE IF NOT EXISTS wanderluster_$DB_NUM.route (
    PRIMARY KEY(lang,slug),
    lang VARCHAR(2) NOT NULL,
    slug VARCHAR(128) NOT NULL,
    shard SMALLINT UNSIGNED NOT NULL,
    ver MEDIUMINT UNSIGNED,
    status TINYINT NOT NULL,
    metadata BLOB NOT NULL
);

CREATE TABLE IF NOT EXISTS wanderluster_$DB_NUM.entity (
    PRIMARY KEY (uuid),
    uuid binary(16),
    shard SMALLINT UNSIGNED,
    ver MEDIUMINT UNSIGNED,
    status TINYINT NOT NULL,
    metadata BLOB NOT NULL
);

CREATE TABLE IF NOT EXISTS wanderluster_$DB_NUM.attribute (
    PRIMARY KEY (uuid),
    uuid binary(16),
    shard SMALLINT UNSIGNED,
    ver MEDIUMINT UNSIGNED,
    status TINYINT NOT NULL,
    metadata BLOB NOT NULL
);

CREATE TABLE IF NOT EXISTS wanderluster_$DB_NUM.user (
    PRIMARY KEY (user_id),
    user_id varchar(32),
    shard SMALLINT UNSIGNED NOT NULL,
    ver MEDIUMINT UNSIGNED,
    status TINYINT NOT NULL,
    metadata BLOB NOT NULL
);