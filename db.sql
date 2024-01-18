CREATE TABLE `trans` (
                         `tid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                         `uid` int(10) unsigned NOT NULL,
                         `tamid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
                         `account` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
                         `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
                         `selite` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
                         `additionalInformation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
                         `bookingDate` varchar(12) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
                         `creditorName` varchar(255) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
                         `debtorName` varchar(255) CHARACTER SET utf32 COLLATE utf32_swedish_ci DEFAULT NULL,
                         `entryReference` varchar(255) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
                         `remittanceInformationUnstructured` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
                         `transactionId` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
                         `valueDate` varchar(12) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
                         `amount` varchar(12) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
                         `currency` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'EUR',
                         `credit` tinyint(3) unsigned zerofill DEFAULT NULL,
                         `debit` tinyint(3) unsigned zerofill DEFAULT NULL,
                         `merchantCategoryCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
                         `bban` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
                         `boodate` int(20) unsigned NOT NULL,
                         `boostamp` int(20) unsigned NOT NULL,
                         `boomonth` int(20) unsigned NOT NULL,
                         `booyear` int(20) unsigned NOT NULL,
                         `status` int(10) unsigned zerofill DEFAULT NULL,
                         `kuitit` int(10) unsigned zerofill DEFAULT NULL,
                         `tilit` int(10) unsigned zerofill DEFAULT NULL,
                         `fetchdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         PRIMARY KEY (`tid`),
                         UNIQUE KEY `tamid` (`tamid`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `subtrans` (

             `sutid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
             `tid` bigint(20) unsigned NOT NULL,
             `uid` int(10) unsigned NOT NULL,
             `transactionId` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
             `name` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
             `summa` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
             `created` TIMESTAMP DEFAULT NOW(),
             `create_ip` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
             PRIMARY KEY (`sutid`)

) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4;



CREATE TABLE `puser` (
                         `uid` INT unsigned NOT NULL AUTO_INCREMENT,
                         `email` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
                         `pass` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
                         `fullname` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `firstname` VARCHAR(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `lastname` VARCHAR(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,

                         `company_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `vatid` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `company_vat` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `company_address` TEXT(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `zipcode` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `ziparea` VARCHAR(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `created` TIMESTAMP DEFAULT NOW(),
                         `lastvisit` TIMESTAMP DEFAULT NOW(),
                         `lastvisit_ip` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `payment_status` INT(1) DEFAULT '0',
                         `create_ip` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `token` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                         `tokentime` TIMESTAMP DEFAULT NOW(),
                         PRIMARY KEY (`uid`)
);



CREATE TABLE `data` (
                        `id` INT unsigned NOT NULL AUTO_INCREMENT,
                        `key` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci,
                        `value` VARCHAR(1000),
                        `aika` TIMESTAMP,
                        PRIMARY KEY (`id`)
);

CREATE TABLE `account` (
                           `aid` INT NOT NULL AUTO_INCREMENT,
                           `uid` INT NOT NULL,
                           `bankname` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                           `account` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                           `bid` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                           `create_ip` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                           `created` TIMESTAMP,
                           PRIMARY KEY (`aid`)
);



CREATE TABLE `kuitit` (
                           `kid` BIGINT NOT NULL AUTO_INCREMENT,
                           `uid` BIGINT NOT NULL,
                           `tid` BIGINT NOT NULL,
                           `transactionId` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                           `tiedosto` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                           `aika` TIMESTAMP,
                           `ip` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                           PRIMARY KEY (`kid`)
);

CREATE TABLE `alias` (
                         `uid` INT NOT NULL,
                         `bban` VARCHAR(255) NOT NULL,
                         `alias` VARCHAR(255) NOT NULL,
                         KEY `alias` (`uid`,`bban`) USING BTREE,
                         PRIMARY KEY (`bban`)
);

CREATE TABLE `tilu` (
                        `tilinro` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
                        `tilinimi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
                        `sanat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `tika` (
                        `id` BIGINT NOT NULL,
                        `tilinro` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
                        `amount` varchar(12) DEFAULT NULL,
                        `uid` INT NOT NULL,
                        `tid` BIGINT NOT NULL,
                        `sutid` BIGINT,
                        `alv` INT,
                        PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `vati` (
                        `id` BIGINT NOT NULL,
                        `amount` varchar(12) DEFAULT NULL,
                        `uid` INT NOT NULL,
                        `tid` BIGINT NOT NULL,
                        `sutid` BIGINT,
                        `alv` INT,
                        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE vati ADD COLUMN id BIGINT NOT NULL;
ALTER TABLE tika ADD COLUMN id BIGINT NOT NULL;

CREATE TABLE `alv` (
                       `aid` INT(3) NOT NULL,
                       `kanta` INT(3) NOT NULL,
                       `selite` VARCHAR(500)
);


CREATE TABLE `tiedostot` (

                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                             `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                             `file_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                             `uid` INT NOT NULL,
                             `aika` TIMESTAMP,
                             `ip` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
                             PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



ALTER TABLE regions ADD COLUMN `engine` varchar(255) NOT NULL;