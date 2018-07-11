ALTER TABLE selection_i18n MODIFY description LONGTEXT;
ALTER TABLE selection_i18n MODIFY chapo LONGTEXT;
ALTER TABLE selection_i18n MODIFY postscriptum LONGTEXT;

-- ---------------------------------------------------------------------
-- selection_container
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `selection_container`
(
  `id`         INTEGER NOT NULL AUTO_INCREMENT,
  `visible`    TINYINT NOT NULL,
  `position`   INTEGER,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
-- selection_container_associated_selection
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `selection_container_associated_selection`
(
  `id`                     INTEGER NOT NULL AUTO_INCREMENT,
  `selection_container_id` INTEGER NOT NULL,
  `selection_id`           INTEGER NOT NULL,
  `created_at`             DATETIME,
  `updated_at`             DATETIME,
  PRIMARY KEY (`id`),
  INDEX                    `idx_selection_container_associated_selection_container_id`( `selection_container_id`
),
INDEX `idx_selection_container_associated_selection_id` (`selection_id`
),
CONSTRAINT `selection_container_associated_selection_container_id`
FOREIGN KEY (`selection_container_id`
)
REFERENCES `selection_container` (`id`
)
ON UPDATE RESTRICT
ON DELETE CASCADE,
CONSTRAINT `selection_container_associated_selection_selection_id`
FOREIGN KEY (`selection_id`)
REFERENCES `selection` (`id`)
ON UPDATE RESTRICT
ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- selection_container_i18n
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `selection_container_i18n`
(
  `id`               INTEGER NOT NULL,
  `locale`           VARCHAR(5) DEFAULT 'en_US' NOT NULL,
  `title`            VARCHAR(255),
  `description`      TEXT,
  `chapo`            TEXT,
  `postscriptum`     TEXT,
  `meta_title`       VARCHAR(255),
  `meta_description` TEXT,
  `meta_keywords`    TEXT,
  PRIMARY KEY (`id`, `locale`),
  CONSTRAINT `selection_container_i18n_FK_1`
  FOREIGN KEY (`id`)
  REFERENCES `selection_container` (`id`)
    ON DELETE CASCADE
) ENGINE = InnoDB;



-- ---------------------------------------------------------------------
-- selection_container_image
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS`selection_container_image`
(
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `selection_container_id` INTEGER NOT NULL,
  `file` VARCHAR(255) NOT NULL,
  `visible` TINYINT DEFAULT 1 NOT NULL,
  `position` INTEGER,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  INDEX `FI_selection_container_image_selection_id` (`selection_container_id`),
CONSTRAINT `fk_selection_container_image_selection_id`
FOREIGN KEY (`selection_container_id`)
REFERENCES `selection_container` (`id`)
ON UPDATE RESTRICT
ON DELETE CASCADE
) ENGINE=InnoDB;


-- ---------------------------------------------------------------------
-- selection_container_image_i18n
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `selection_container_image_i18n`
(
  `id` INTEGER NOT NULL,
  `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
  `title` VARCHAR(255),
  `description` LONGTEXT,
  `chapo` TEXT,
  `postscriptum` TEXT,
  PRIMARY KEY (`id`,`locale`),
  CONSTRAINT `selection_container_image_i18n_FK_1`
  FOREIGN KEY (`id`)
  REFERENCES `selection_container_image` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
