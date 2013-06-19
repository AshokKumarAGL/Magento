<?php
$setup = $this;
$setup->startSetup();
$setup->run("
CREATE TABLE IF NOT EXISTS {$setup->getTable('triveni_customsize')} (
    `id` INT NOT NULL auto_increment,
    `full_name` VARCHAR( 250 ) NOT NULL,
    `email_id` VARCHAR( 250 ) NOT NULL,
    `measurement_in` VARCHAR( 50 ) NOT NULL,
    `measurement_used` VARCHAR (255) NOT NULL,
	`around_bust` VARCHAR( 100 ) NOT NULL,
	`around_above_waist` VARCHAR( 100 ) NOT NULL,
	`shoulder` VARCHAR( 100 ) NOT NULL,
	`sleeve_style` VARCHAR( 100 ) NOT NULL,
	`sleeve_length` VARCHAR( 100 ) NOT NULL,
	`around_arm_hole` VARCHAR( 100 ) NOT NULL,
	`around_arm` VARCHAR( 100 ) NOT NULL,
	`front_neck_style` VARCHAR( 100 ) NOT NULL,
	`front_neck_depth` VARCHAR( 100 ) NOT NULL,
	`back_neck_style` VARCHAR( 100 ) NOT NULL,
	`back_neck_depth` VARCHAR( 100 ) NOT NULL,
	`blouse_length` VARCHAR( 100 ) NOT NULL,
	`closing_side` VARCHAR( 100 ) NOT NULL,
	`closing_with` VARCHAR( 100 ) NOT NULL,
	`lining` VARCHAR( 100 ) NOT NULL,
	`around_waist` VARCHAR( 100 ) NOT NULL,
	`around_hips` VARCHAR( 100 ) NOT NULL,
	`petticoat_length` VARCHAR( 100 ) NOT NULL,
	`your_height` VARCHAR( 100 ) NOT NULL,
	`instructions` VARCHAR( 255 ) NOT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'inactive',
    `created_at` DATETIME NULL DEFAULT NULL,
    `modified_by` VARCHAR( 50 ) DEFAULT NULL,
    `modified_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;
");
$setup->endSetup();
