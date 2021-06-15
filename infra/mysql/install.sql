USE 3wa_final;

CREATE TABLE `user` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(64),
    `password` VARCHAR(64),
    INDEX (`username`) `username`
);

CREATE TABLE `thread` (
    `id` INT PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE `thread_user` (
    `id_thread` INT,
    `id_user` INT,
    FOREIGN KEY (`id_thread`) REFERENCES `thread`(`id_thread`),
    FOREIGN KEY (`id_user`) REFERENCES `user`(`id_user`)
);

CREATE TABLE `message` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `id_user` INT,
    `id_thread` INT,
    `content` VARCHAR(64),
    FOREIGN KEY (`id_user`) REFERENCES `user`(`id_user`),
    FOREIGN KEY (`id_thread`) REFERENCES `thread`(`id_thread`)
);

CREATE TABLE `notification` (
    `id_user` INT,
    `id_thread` INT,
    `count` INT,
    FOREIGN KEY (`id_user`) REFERENCES `user`(`id_user`),
    FOREIGN KEY (`id_thread`) REFERENCES `thread`(`id_thread`)
);