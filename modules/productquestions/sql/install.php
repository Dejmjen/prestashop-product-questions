<?php

if (!defined('_PS_VERSION_')){
    exit;
}

$sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'product_question` (
`id_product_question` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`id_product` INT UNSIGNED NOT NULL,
`question` TEXT NOT NULL,
`answer` TEXT NULL,
`is_approved` TINYINT(1) NOT NULL DEFAULT 0,
`date_add` DATETIME NOT NULL,
PRIMARY KEY (`id_product_question`),
KEY `idx_product_question_product` (`id_product`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

return Db::getInstance()->execute($sql);