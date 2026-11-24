-- php-native-crud — database schema
-- Import this file first (phpMyAdmin > Import, or: mysql -u root < schema.sql)

CREATE DATABASE IF NOT EXISTS php_native_crud
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE php_native_crud;

CREATE TABLE IF NOT EXISTS students (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nim             VARCHAR(20)  NOT NULL,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL,
    major           VARCHAR(100) NOT NULL,
    enrollment_year YEAR         NOT NULL,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
                                 ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_students_nim (nim),
    UNIQUE KEY uq_students_email (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
