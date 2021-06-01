CREATE TABLE `user` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(64),
    `password` VARCHAR(64)
);

CREATE TABLE `thread` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
);

CREATE TABLE `thread_user` (
    `id_thread` INT,
    `id_user` INT
);

CREATE TABLE `message` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `id_user` INT,
    `id_thread` INT,
    `content` VARCHAR(64)
);

CREATE TABLE `user_notification` (
    `id_user` INT,
    `id_message` INT,
);