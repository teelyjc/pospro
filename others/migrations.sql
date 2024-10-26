CREATE TABLE
  `users` (
    `id` VARCHAR(40) UNIQUE NOT NULL,
    `username` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255),
    `firstname` VARCHAR(100),
    `lastname` VARCHAR(100),
    `role` ENUM ('CUSTOMER', 'SELLER', 'ADMINISTRATOR'),
    `profile_path` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `is_deleted` BOOLEAN NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
  );

CREATE TABLE
  `product_types` (
    `id` VARCHAR(40) UNIQUE NOT NULL,
    `name` VARCHAR(100),
    `description` LONGTEXT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
  );

CREATE TABLE
  `products` (
    `id` VARCHAR(40) UNIQUE NOT NULL,
    `type_id` VARCHAR(40) NOT NULL,
    `name` VARCHAR(100),
    `description` LONGTEXT,
    `price` FLOAT NOT NULL,
    `quantity` INT NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`type_id`) REFERENCES `product_types` (`id`)
  );

CREATE TABLE
  `orders` (
    `id` VARCHAR(40) UNIQUE NOT NULL,
    `owner_id` VARCHAR(40) NOT NULL,
    `label` VARCHAR(100),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
  );

CREATE TABLE
  `products_orders` (
    `id` VARCHAR(40) UNIQUE NOT NULL,
    `product_id` VARCHAR(100),
    `order_id` VARCHAR(100),
    `quantity` INT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
  );
