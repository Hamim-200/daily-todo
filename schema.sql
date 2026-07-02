-- Run this once in phpMyAdmin (or mysql CLI) to set up the database

CREATE DATABASE IF NOT EXISTS todo_app;
USE todo_app;

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_name VARCHAR(255) NOT NULL,
    task_date DATE NOT NULL,
    start_time DATETIME NULL,
    end_time DATETIME NULL,
    status ENUM('pending','in_progress','completed') DEFAULT 'pending',
    is_fixed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
