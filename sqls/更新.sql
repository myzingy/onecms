ALTER TABLE `expert_application`
	ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT NULL AFTER `mp_verify_file_url`;
ALTER TABLE `expert_application`
	ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;

ALTER TABLE `expert_application`
	ADD COLUMN `wx_qrcode` VARCHAR(1024) NULL DEFAULT NULL AFTER `wx_img_url`,
	ADD COLUMN `mp_qrcode` VARCHAR(45) NULL DEFAULT NULL AFTER `mp_secret`,
	ADD COLUMN `mp_auth` TINYINT(1) NULL AFTER `state`;
	


ALTER TABLE `expert_application`
	ADD COLUMN `rejected_reason` TINYINT(1) NULL AFTER `mp_auth`;
	
	
	
	
	
	
ALTER TABLE `expert`
	ADD COLUMN `wx_qrcode` VARCHAR(1024) NULL DEFAULT NULL AFTER `wx_img_url`,
	ADD COLUMN `mp_qrcode` VARCHAR(45) NULL DEFAULT NULL AFTER `mp_secret`,
	ADD COLUMN `mp_auth` TINYINT(1) NULL AFTER `state`;
	

