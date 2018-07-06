
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- selection
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `selection`;

CREATE TABLE `selection`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `visible` TINYINT NOT NULL,
    `position` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- selection_product
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `selection_product`;

CREATE TABLE `selection_product`
(
    `selection_id` INTEGER NOT NULL,
    `product_id` INTEGER NOT NULL,
    `position` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`selection_id`,`product_id`),
    INDEX `FI_selection_product_product_id` (`product_id`),
    CONSTRAINT `fk_selection_product_product_id`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_selection_product_selection_id`
        FOREIGN KEY (`selection_id`)
        REFERENCES `selection` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- selection_content
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `selection_content`;

CREATE TABLE `selection_content`
(
    `selection_id` INTEGER NOT NULL,
    `content_id` INTEGER NOT NULL,
    `position` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`selection_id`,`content_id`),
    INDEX `FI_selection_content_content_id` (`content_id`),
    CONSTRAINT `fk_selection_content_content_id`
        FOREIGN KEY (`content_id`)
        REFERENCES `content` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_selection_content_selection_id`
        FOREIGN KEY (`selection_id`)
        REFERENCES `selection` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- selection_image
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `selection_image`;

CREATE TABLE `selection_image`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `selection_id` INTEGER NOT NULL,
    `file` VARCHAR(255) NOT NULL,
    `visible` TINYINT DEFAULT 1 NOT NULL,
    `position` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `FI_selection_image_selection_id` (`selection_id`),
    CONSTRAINT `fk_selection_image_selection_id`
        FOREIGN KEY (`selection_id`)
        REFERENCES `selection` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- selection_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `selection_i18n`;

CREATE TABLE `selection_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` TEXT,
    `chapo` TEXT,
    `postscriptum` TEXT,
    `meta_title` VARCHAR(255),
    `meta_description` TEXT,
    `meta_keywords` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `selection_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `selection` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- selection_image_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `selection_image_i18n`;

CREATE TABLE `selection_image_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    `chapo` TEXT,
    `postscriptum` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `selection_image_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `selection_image` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
