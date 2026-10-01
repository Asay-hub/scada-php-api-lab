SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS sensors_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sensors_db;

DROP TABLE IF EXISTS devices;
DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    protocol VARCHAR(50) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO categories (code) VALUES ('Temperature'), ('Pressure'), ('Flow');
INSERT INTO devices (category_id, name, protocol) VALUES
    (1, 'Pt100 Sensor', 'Analog 4-20mA'),
    (1, 'DS18B20', '1-Wire'),
    (2, 'MPX5010DP', 'Analog'),
    (3, 'YF-S201', 'Pulse');