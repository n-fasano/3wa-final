USE 3wa_final;

CREATE TABLE `user` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(64) NOT NULL,
    `password` VARCHAR(64) NOT NULL,
    INDEX (username)
);

CREATE TABLE `thread` (
    `id` INT PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE `thread_user` (
    `id_thread` INT NOT NULL,
    `id_user` INT NOT NULL,
    FOREIGN KEY (id_thread) REFERENCES thread(id),
    FOREIGN KEY (id_user) REFERENCES user(id)
);

CREATE TABLE `message` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `id_user` INT NOT NULL,
    `id_thread` INT NOT NULL,
    `content` VARCHAR(200) NOT NULL,
    `sent_at` DATETIME NOT NULL,
    FOREIGN KEY (id_user) REFERENCES user(id),
    FOREIGN KEY (id_thread) REFERENCES thread(id)
);

CREATE TABLE `notification` (
    `id_user` INT NOT NULL,
    `id_thread` INT NOT NULL,
    `count` INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES user(id),
    FOREIGN KEY (id_thread) REFERENCES thread(id)
);