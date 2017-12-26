ALTER TABLE `expert_application`
	ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT NULL AFTER `mp_verify_file_url`;
ALTER TABLE `expert_application`
	ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;

ALTER TABLE `expert_application`
	ADD COLUMN `wx_qrcode` VARCHAR(1024) NULL DEFAULT NULL AFTER `wx_img_url`,
	ADD COLUMN `mp_qrcode` VARCHAR(45) NULL DEFAULT NULL AFTER `mp_secret`;


ALTER TABLE `expert_application`
	ADD COLUMN `service_type` TINYINT(1) NOT NULL AFTER `state`;


ALTER TABLE `expert_application`
	ADD COLUMN `mp_auth` TINYINT(1) NOT NULL AFTER `state`;


ALTER TABLE `expert_application`
	ALTER `mp_auth` DROP DEFAULT,
	ALTER `service_type` DROP DEFAULT;
ALTER TABLE `expert_application`
	CHANGE COLUMN `mp_auth` `mp_auth` TINYINT(1) NULL AFTER `state`,
	CHANGE COLUMN `service_type` `service_type` TINYINT(1) NULL AFTER `mp_auth`;
