DROP TABLE IF EXISTS user;

CREATE TABLE user(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    level ENUM('admin', 'user', 'viewer') NOT NULL DEFAULT 'user',
    created_by TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified_by TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- Seed
INSERT INTO user (id, first_name, last_name, username, email, password, level) VALUES (1, 'root', 'user', 'root', 'root@user.com', '$2y$10$bNtS0FcdaYMvpC6t0I.jqeMCmU5JswoV3rlblwRW4.LyK7.QkYjEy', 'admin');