CREATE DATABASE ukoo;

USE ukoo;

CREATE TABLE family_tree (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    middle_name VARCHAR(50),
    last_name VARCHAR(50),
    dob DATE,
    gender ENUM('male','female'),
    marital_status ENUM('single','married'),
    has_children BOOLEAN,
    children_male INT DEFAULT 0,
    children_female INT DEFAULT 0,
    country VARCHAR(100),
    region VARCHAR(100),
    district VARCHAR(100),
    ward VARCHAR(100),
    village VARCHAR(100),
    city VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    password VARCHAR(255),
    photo VARCHAR(255),
    parent_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES family_tree(id) ON DELETE SET NULL
);
