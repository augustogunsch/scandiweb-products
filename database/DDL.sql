CREATE DATABASE `products`;

USE `products`;

CREATE TABLE `product` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `sku` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`)
)

CREATE TABLE `book` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `weight` float NOT NULL,
  `product_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `book_UN` (`product_id`),
  CONSTRAINT `book_FK` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
)

CREATE TABLE `dvd` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `size` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dvd_UN` (`product_id`),
  CONSTRAINT `dvd_FK` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
)

CREATE TABLE `furniture` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `width` float NOT NULL,
  `height` float NOT NULL,
  `length` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `furniture_UN` (`product_id`),
  CONSTRAINT `furniture_FK` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
)

