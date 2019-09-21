/*
Navicat MySQL Data Transfer

Source Server         : local host
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : business_document

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2019-09-20 20:02:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for approval_requests
-- ----------------------------
DROP TABLE IF EXISTS `approval_requests`;
CREATE TABLE `approval_requests` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `procedure_id` int(11) DEFAULT NULL,
  `comment` longtext,
  `users` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_foreighn_key_01` (`user_id`),
  KEY `procedure_foreighn_key_01` (`procedure_id`),
  CONSTRAINT `procedure_foreighn_key_01` FOREIGN KEY (`procedure_id`) REFERENCES `folder_and_procedure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_foreighn_key_01` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of approval_requests
-- ----------------------------
INSERT INTO `approval_requests` VALUES ('4', '1', '49', 'ddfd', '1,4', 'review', '2019-09-16 15:57:00', '2019-09-17 17:11:47');
INSERT INTO `approval_requests` VALUES ('9', '5', '51', 'afafj', '1,4', 'approval', '2019-09-17 12:45:40', '2019-09-20 19:57:02');

-- ----------------------------
-- Table structure for data_rows
-- ----------------------------
DROP TABLE IF EXISTS `data_rows`;
CREATE TABLE `data_rows` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `data_type_id` int(10) NOT NULL,
  `field` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `browse` tinyint(1) NOT NULL DEFAULT '1',
  `read` tinyint(1) NOT NULL DEFAULT '1',
  `edit` tinyint(1) NOT NULL DEFAULT '1',
  `add` tinyint(1) NOT NULL DEFAULT '1',
  `delete` tinyint(1) NOT NULL DEFAULT '1',
  `details` text COLLATE utf8mb4_unicode_ci,
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `data_rows_data_type_id_foreign` (`data_type_id`),
  CONSTRAINT `data_rows_data_type_id_foreign` FOREIGN KEY (`data_type_id`) REFERENCES `data_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of data_rows
-- ----------------------------
INSERT INTO `data_rows` VALUES ('1', '1', 'id', 'number', 'ID', '1', '0', '0', '0', '0', '0', '{}', '1');
INSERT INTO `data_rows` VALUES ('2', '1', 'name', 'text', 'Name', '1', '1', '1', '1', '1', '1', '{}', '2');
INSERT INTO `data_rows` VALUES ('3', '1', 'email', 'text', 'Email', '1', '1', '1', '1', '1', '1', '{}', '3');
INSERT INTO `data_rows` VALUES ('4', '1', 'password', 'password', 'Password', '1', '0', '0', '1', '1', '0', '{}', '4');
INSERT INTO `data_rows` VALUES ('5', '1', 'remember_token', 'text', 'Remember Token', '0', '0', '0', '0', '0', '0', '{}', '5');
INSERT INTO `data_rows` VALUES ('6', '1', 'created_at', 'timestamp', 'Created At', '0', '0', '0', '0', '0', '0', '{}', '6');
INSERT INTO `data_rows` VALUES ('7', '1', 'updated_at', 'timestamp', 'Updated At', '0', '0', '0', '0', '0', '0', '{}', '7');
INSERT INTO `data_rows` VALUES ('8', '1', 'avatar', 'image', 'Avatar', '0', '0', '0', '0', '0', '1', '{}', '8');
INSERT INTO `data_rows` VALUES ('9', '1', 'user_belongsto_role_relationship', 'relationship', 'Role', '0', '1', '1', '1', '1', '0', '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Role\",\"table\":\"roles\",\"type\":\"belongsTo\",\"column\":\"role_id\",\"key\":\"id\",\"label\":\"display_name\",\"pivot_table\":\"roles\",\"pivot\":\"0\",\"taggable\":\"0\"}', '10');
INSERT INTO `data_rows` VALUES ('10', '1', 'user_belongstomany_role_relationship', 'relationship', 'Roles', '0', '0', '0', '0', '0', '0', '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Role\",\"table\":\"roles\",\"type\":\"belongsToMany\",\"column\":\"id\",\"key\":\"id\",\"label\":\"display_name\",\"pivot_table\":\"user_roles\",\"pivot\":\"1\",\"taggable\":\"0\"}', '11');
INSERT INTO `data_rows` VALUES ('11', '1', 'settings', 'hidden', 'Settings', '0', '0', '0', '0', '0', '0', '{}', '12');
INSERT INTO `data_rows` VALUES ('16', '3', 'id', 'number', 'ID', '1', '0', '0', '0', '0', '0', null, '1');
INSERT INTO `data_rows` VALUES ('17', '3', 'name', 'text', 'Name', '1', '1', '1', '1', '1', '1', null, '2');
INSERT INTO `data_rows` VALUES ('18', '3', 'created_at', 'timestamp', 'Created At', '0', '0', '0', '0', '0', '0', null, '3');
INSERT INTO `data_rows` VALUES ('19', '3', 'updated_at', 'timestamp', 'Updated At', '0', '0', '0', '0', '0', '0', null, '4');
INSERT INTO `data_rows` VALUES ('20', '3', 'display_name', 'text', 'Display Name', '1', '1', '1', '1', '1', '1', null, '5');
INSERT INTO `data_rows` VALUES ('21', '1', 'role_id', 'text', 'Role', '0', '1', '1', '1', '1', '1', '{}', '9');
INSERT INTO `data_rows` VALUES ('22', '1', 'email_verified_at', 'timestamp', 'Email Verified At', '0', '0', '0', '0', '0', '1', '{}', '6');
INSERT INTO `data_rows` VALUES ('28', '5', 'id', 'hidden', 'Id', '1', '0', '0', '0', '0', '0', '{}', '1');
INSERT INTO `data_rows` VALUES ('29', '5', 'name', 'text', 'Name', '0', '1', '1', '1', '1', '1', '{\"validation\":{\"rule\":\"required\"}}', '2');
INSERT INTO `data_rows` VALUES ('30', '5', 'description', 'text', 'Description', '0', '0', '0', '0', '0', '1', '{}', '3');
INSERT INTO `data_rows` VALUES ('31', '5', 'created_at', 'timestamp', 'Created At', '0', '0', '0', '0', '0', '1', '{}', '4');
INSERT INTO `data_rows` VALUES ('32', '5', 'updated_at', 'timestamp', 'Updated At', '0', '0', '0', '0', '0', '0', '{}', '5');

-- ----------------------------
-- Table structure for data_types
-- ----------------------------
DROP TABLE IF EXISTS `data_types`;
CREATE TABLE `data_types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_singular` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_plural` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controller` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generate_permissions` tinyint(1) NOT NULL DEFAULT '0',
  `server_side` tinyint(4) NOT NULL DEFAULT '0',
  `details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_types_name_unique` (`name`),
  UNIQUE KEY `data_types_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of data_types
-- ----------------------------
INSERT INTO `data_types` VALUES ('1', 'users', 'users', 'User', 'Users', 'voyager-person', 'TCG\\Voyager\\Models\\User', 'TCG\\Voyager\\Policies\\UserPolicy', 'App\\Http\\Controllers\\Voyager\\VoyagerUserController', null, '1', '0', '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"desc\",\"default_search_key\":null,\"scope\":null}', '2019-07-24 21:22:10', '2019-09-14 09:46:31');
INSERT INTO `data_types` VALUES ('3', 'roles', 'roles', 'Role', 'Roles', 'voyager-lock', 'TCG\\Voyager\\Models\\Role', null, '', '', '1', '0', null, '2019-07-24 21:22:10', '2019-07-24 21:22:10');
INSERT INTO `data_types` VALUES ('5', 'departments', 'departments', 'Department', 'Departments', 'voyager-company', 'App\\Department', null, 'App\\Http\\Controllers\\Voyager\\VoyagerDepartmentController', null, '1', '0', '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2019-07-26 03:19:19', '2019-08-28 11:43:23');

-- ----------------------------
-- Table structure for departments
-- ----------------------------
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of departments
-- ----------------------------
INSERT INTO `departments` VALUES ('2', 'Backend Department renamed', null, '2019-07-26 03:31:30', '2019-09-02 16:13:22');
INSERT INTO `departments` VALUES ('3', 'dept. Administrativ', null, '2019-07-26 03:43:55', '2019-09-18 18:02:50');
INSERT INTO `departments` VALUES ('4', 'new department testingadfaf', null, '2019-08-28 10:45:08', '2019-09-02 11:26:52');
INSERT INTO `departments` VALUES ('5', 'admin department', null, '2019-09-06 16:43:30', '2019-09-06 16:43:30');

-- ----------------------------
-- Table structure for favourites
-- ----------------------------
DROP TABLE IF EXISTS `favourites`;
CREATE TABLE `favourites` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fold_proc_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of favourites
-- ----------------------------
INSERT INTO `favourites` VALUES ('1', '1', '1', '1');
INSERT INTO `favourites` VALUES ('2', '3', '1', '1');
INSERT INTO `favourites` VALUES ('3', '4', '1', '1');
INSERT INTO `favourites` VALUES ('4', '6', '1', '1');
INSERT INTO `favourites` VALUES ('5', '5', '1', '1');
INSERT INTO `favourites` VALUES ('6', '7', '1', '1');
INSERT INTO `favourites` VALUES ('7', '32', '1', '1');
INSERT INTO `favourites` VALUES ('8', '45', '1', '0');
INSERT INTO `favourites` VALUES ('9', '41', '1', '1');
INSERT INTO `favourites` VALUES ('10', '42', '1', '1');

-- ----------------------------
-- Table structure for folder_and_procedure
-- ----------------------------
DROP TABLE IF EXISTS `folder_and_procedure`;
CREATE TABLE `folder_and_procedure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `owner` bigint(20) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `description` text,
  `edit` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `delete` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_01_foreign_key` (`department_id`),
  KEY `procedure_01_foreign_key` (`parent_id`),
  KEY `user_01_foreign_key` (`owner`),
  CONSTRAINT `department_01_foreign_key` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `procedure_01_foreign_key` FOREIGN KEY (`parent_id`) REFERENCES `folder_and_procedure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_01_foreign_key` FOREIGN KEY (`owner`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of folder_and_procedure
-- ----------------------------
INSERT INTO `folder_and_procedure` VALUES ('1', '2', 'testing folder ', null, '1', 'folder', null, null, null, '2019-07-27 10:00:51', null, null);
INSERT INTO `folder_and_procedure` VALUES ('4', '2', 'testing Procedure na', '1', '1', 'procedure', null, 'this is testing Procedure', '2', '2019-07-30 23:48:09', '2019-09-14 21:02:42', null);
INSERT INTO `folder_and_procedure` VALUES ('36', '2', 'testing', null, '1', 'folder', '1', null, '1', '2019-08-24 09:03:36', null, null);
INSERT INTO `folder_and_procedure` VALUES ('37', '3', '2344', null, '1', 'procedure', null, 'test', '2', '2019-08-27 09:49:17', '2019-09-11 20:37:33', null);
INSERT INTO `folder_and_procedure` VALUES ('38', '3', '12345678', null, '1', 'procedure', null, 'testing', '1', '2019-08-27 10:28:07', null, null);
INSERT INTO `folder_and_procedure` VALUES ('39', '2', 'teston', '1', '1', 'procedure', null, 'dsggs', '2', '2019-08-27 15:09:46', '2019-09-06 17:07:02', null);
INSERT INTO `folder_and_procedure` VALUES ('40', '2', 'renamed procedure', '1', '5', 'procedure', null, 'jkhjkh', '2', '2019-08-27 15:19:13', '2019-09-19 12:16:53', null);
INSERT INTO `folder_and_procedure` VALUES ('41', '2', 'kjhjk', '1', '1', 'procedure', null, 'jkhjkh', '2', '2019-08-27 15:24:00', '2019-09-17 14:05:14', null);
INSERT INTO `folder_and_procedure` VALUES ('42', '2', 'tersgs', null, '1', 'procedure', null, 'r4stws', '3', '2019-08-27 15:40:55', '2019-09-18 16:01:56', null);
INSERT INTO `folder_and_procedure` VALUES ('43', '4', '12345', null, '1', 'procedure', null, '87654', '3', '2019-09-02 11:39:12', '2019-09-17 14:50:35', '1');
INSERT INTO `folder_and_procedure` VALUES ('44', '2', 'test 1243', '1', '1', 'procedure', null, '123', null, '2019-09-02 11:53:03', '2019-09-02 16:54:10', null);
INSERT INTO `folder_and_procedure` VALUES ('45', '2', 'contributor proc', '1', '5', 'procedure', null, 'contributor procedure', '3', '2019-09-06 11:03:30', '2019-09-19 12:16:54', null);
INSERT INTO `folder_and_procedure` VALUES ('46', '5', 'testing admin folder', null, '1', 'folder', null, null, '2', '2019-09-14 14:24:27', '2019-09-16 20:02:57', null);
INSERT INTO `folder_and_procedure` VALUES ('47', '5', 'testing procedure admin', null, '1', 'procedure', null, 'lorem ipsum', '2', '2019-09-14 14:34:45', '2019-09-17 17:37:02', null);
INSERT INTO `folder_and_procedure` VALUES ('48', '2', 'amin', null, '5', 'procedure', null, 'description', '3', '2019-09-16 10:16:11', '2019-09-19 12:16:55', null);
INSERT INTO `folder_and_procedure` VALUES ('49', '2', '123456787654', 'reject', '5', 'procedure', null, '234567876543', '2', '2019-09-16 15:56:24', '2019-09-19 12:16:56', null);
INSERT INTO `folder_and_procedure` VALUES ('50', '4', 'afjaklfj', null, '5', 'procedure', null, 'klajdfklaj', '2', '2019-09-17 09:50:41', '2019-09-19 13:01:36', null);
INSERT INTO `folder_and_procedure` VALUES ('51', '2', 'contributor', null, '5', 'procedure', null, 'hajkhf', '3', '2019-09-17 12:39:28', '2019-09-20 19:54:06', null);
INSERT INTO `folder_and_procedure` VALUES ('52', '3', 'Achizitii Clover', null, '1', 'procedure', null, 'test daniel', '2', '2019-09-18 18:03:16', '2019-09-18 19:03:17', null);

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menus_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menus
-- ----------------------------
INSERT INTO `menus` VALUES ('1', 'admin', '2019-07-24 21:22:12', '2019-07-24 21:22:12');

-- ----------------------------
-- Table structure for menu_items
-- ----------------------------
DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self',
  `icon_class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `route` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parameters` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `menu_items_menu_id_foreign` (`menu_id`),
  CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menu_items
-- ----------------------------
INSERT INTO `menu_items` VALUES ('1', '1', 'Dashboard', '', '_self', 'voyager-boat', null, null, '1', '2019-07-24 21:22:12', '2019-07-24 21:22:12', 'voyager.dashboard', null);
INSERT INTO `menu_items` VALUES ('2', '1', 'Media', '', '_self', 'voyager-images', null, null, '5', '2019-07-24 21:22:12', '2019-07-24 21:22:12', 'voyager.media.index', null);
INSERT INTO `menu_items` VALUES ('3', '1', 'Users', '', '_self', 'voyager-person', null, null, '3', '2019-07-24 21:22:12', '2019-07-24 21:22:12', 'voyager.users.index', null);
INSERT INTO `menu_items` VALUES ('4', '1', 'Roles', '', '_self', 'voyager-lock', null, null, '2', '2019-07-24 21:22:12', '2019-07-24 21:22:12', 'voyager.roles.index', null);
INSERT INTO `menu_items` VALUES ('5', '1', 'Tools', '', '_self', 'voyager-tools', null, null, '9', '2019-07-24 21:22:12', '2019-07-24 21:22:12', null, null);
INSERT INTO `menu_items` VALUES ('7', '1', 'Database', '', '_self', 'voyager-data', null, '5', '11', '2019-07-24 21:22:12', '2019-07-24 21:22:12', 'voyager.database.index', null);
INSERT INTO `menu_items` VALUES ('8', '1', 'Compass', '', '_self', 'voyager-compass', null, '5', '12', '2019-07-24 21:22:12', '2019-07-24 21:22:12', 'voyager.compass.index', null);
INSERT INTO `menu_items` VALUES ('9', '1', 'BREAD', '', '_self', 'voyager-bread', null, '5', '13', '2019-07-24 21:22:12', '2019-07-24 21:22:12', 'voyager.bread.index', null);
INSERT INTO `menu_items` VALUES ('10', '1', 'Settings', '', '_self', 'voyager-settings', null, null, '14', '2019-07-24 21:22:12', '2019-07-24 21:22:12', 'voyager.settings.index', null);
INSERT INTO `menu_items` VALUES ('14', '1', 'Hooks', '', '_self', 'voyager-hook', null, '5', '13', '2019-07-24 21:22:26', '2019-07-24 21:22:26', 'voyager.hooks', null);
INSERT INTO `menu_items` VALUES ('16', '1', 'Departments', '', '_self', 'voyager-company', null, null, '15', '2019-07-26 03:19:20', '2019-07-26 03:19:20', 'voyager.departments.index', null);

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('1', '2014_10_12_000000_create_users_table', '1');
INSERT INTO `migrations` VALUES ('2', '2014_10_12_100000_create_password_resets_table', '1');
INSERT INTO `migrations` VALUES ('3', '2016_01_01_000000_add_voyager_user_fields', '1');
INSERT INTO `migrations` VALUES ('4', '2016_01_01_000000_create_data_types_table', '1');
INSERT INTO `migrations` VALUES ('5', '2016_05_19_173453_create_menu_table', '1');
INSERT INTO `migrations` VALUES ('6', '2016_10_21_190000_create_roles_table', '1');
INSERT INTO `migrations` VALUES ('7', '2016_10_21_190000_create_settings_table', '1');
INSERT INTO `migrations` VALUES ('8', '2016_11_30_135954_create_permission_table', '1');
INSERT INTO `migrations` VALUES ('9', '2016_11_30_141208_create_permission_role_table', '1');
INSERT INTO `migrations` VALUES ('10', '2016_12_26_201236_data_types__add__server_side', '1');
INSERT INTO `migrations` VALUES ('11', '2017_01_13_000000_add_route_to_menu_items_table', '1');
INSERT INTO `migrations` VALUES ('12', '2017_01_14_005015_create_translations_table', '1');
INSERT INTO `migrations` VALUES ('13', '2017_01_15_000000_make_table_name_nullable_in_permissions_table', '1');
INSERT INTO `migrations` VALUES ('14', '2017_03_06_000000_add_controller_to_data_types_table', '1');
INSERT INTO `migrations` VALUES ('15', '2017_04_21_000000_add_order_to_data_rows_table', '1');
INSERT INTO `migrations` VALUES ('16', '2017_07_05_210000_add_policyname_to_data_types_table', '1');
INSERT INTO `migrations` VALUES ('17', '2017_08_05_000000_add_group_to_settings_table', '1');
INSERT INTO `migrations` VALUES ('18', '2017_11_26_013050_add_user_role_relationship', '1');
INSERT INTO `migrations` VALUES ('19', '2017_11_26_015000_create_user_roles_table', '1');
INSERT INTO `migrations` VALUES ('20', '2018_03_11_000000_add_user_settings', '1');
INSERT INTO `migrations` VALUES ('21', '2018_03_14_000000_add_details_to_data_types_table', '1');
INSERT INTO `migrations` VALUES ('22', '2018_03_16_000000_make_settings_value_nullable', '1');
INSERT INTO `migrations` VALUES ('23', '2016_01_01_000000_create_pages_table', '2');
INSERT INTO `migrations` VALUES ('24', '2016_01_01_000000_create_posts_table', '2');
INSERT INTO `migrations` VALUES ('25', '2016_02_15_204651_create_categories_table', '2');
INSERT INTO `migrations` VALUES ('26', '2017_04_11_000000_alter_post_nullable_fields_table', '2');

-- ----------------------------
-- Table structure for notification
-- ----------------------------
DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `body` text,
  `type` varchar(255) DEFAULT NULL,
  `department_id` int(10) DEFAULT NULL,
  `procedure_id` int(10) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `assigned_to` int(10) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of notification
-- ----------------------------
INSERT INTO `notification` VALUES ('6', 'Test published changes to procedure renamed procedure in department Backend Department renamed.', 'Test published changes to procedure <a href=\"/procedure/40\">renamed procedure</a> in department <a href=\"/departments/2\">Backend Department renamed</a>.', 'department', '2', '40', '2', '1', 'read', '2019-09-02 16:45:40', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('7', 'Test published changes to procedure renamed procedure in department Backend Department renamed.', 'Test published changes to procedure <a href=\"/procedure/40\">renamed procedure</a> in department <a href=\"/departments/2\">Backend Department renamed</a>.', 'department', '2', '40', '2', '5', 'unread', '2019-09-02 16:45:40', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('81', 'messages.procedure.submitApproval', 'messages.procedure.submitApproval', 'procedure', null, '50', '2', '5', 'unread', '2019-09-17 10:14:52', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('82', 'messages.procedure.cancelRequest', 'messages.procedure.cancelRequest', 'procedure', null, '50', '2', '5', 'unread', '2019-09-17 11:09:44', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('83', 'messages.procedure.requestApproval', 'messages.procedure.requestApproval', 'procedure', null, '50', '2', '5', 'unread', '2019-09-17 11:10:13', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('84', 'messages.procedure.cancelRequest', 'messages.procedure.cancelRequest', 'procedure', null, '50', '2', '5', 'unread', '2019-09-17 11:10:40', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('85', 'messages.procedure.cancelRequest', 'messages.procedure.cancelRequest', 'procedure', null, '50', '2', '1', 'unread', '2019-09-17 11:10:40', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('86', 'messages.procedure.submitApproval', 'messages.procedure.submitApproval', 'procedure', null, '50', '2', '5', 'unread', '2019-09-17 11:12:46', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('87', 'Test has canceled the approval request to you for procedure afjaklfj in department  new department testingadfaf .', 'Test has canceled the approval request to you for procedure <a href=\"/procedure/50\">afjaklfj</a> in department  <a href=\"/departments/4\">new department testingadfaf</a> .', 'procedure', null, '50', '2', '5', 'unread', '2019-09-17 11:18:36', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('88', 'Test has requested an approval for procedure afjaklfj in department new department testingadfaf .', 'Test has requested an approval for procedure <a href=\"/procedure/50\">afjaklfj</a> in department <a href=\"/departments/4\">new department testingadfaf</a> .', 'procedure', null, '50', '2', '5', 'unread', '2019-09-17 11:19:10', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('89', 'Test has canceled the approval request to you for procedure afjaklfj in department  new department testingadfaf .', 'Test has canceled the approval request to you for procedure <a href=\"/procedure/50\">afjaklfj</a> in department  <a href=\"/departments/4\">new department testingadfaf</a> .', 'procedure', null, '50', '2', '5', 'unread', '2019-09-17 11:20:29', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('90', 'Test has canceled the approval request to you for procedure afjaklfj in department  new department testingadfaf .', 'Test has canceled the approval request to you for procedure <a href=\"/procedure/50\">afjaklfj</a> in department  <a href=\"/departments/4\">new department testingadfaf</a> .', 'procedure', null, '50', '2', '1', 'unread', '2019-09-17 11:20:29', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('91', 'Test has requested an approval for procedure contributor in department Backend Department renamed .', 'Test has requested an approval for procedure <a href=\"/procedure/51\">contributor</a> in department <a href=\"/departments/2\">Backend Department renamed</a> .', 'procedure', null, '51', '2', '1', 'unread', '2019-09-17 12:45:40', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('92', 'Test has requested an approval for procedure contributor in department Backend Department renamed .', 'Test has requested an approval for procedure <a href=\"/procedure/51\">contributor</a> in department <a href=\"/departments/2\">Backend Department renamed</a> .', 'procedure', null, '51', '2', '4', 'unread', '2019-09-17 12:45:40', '2019-09-19 12:43:21');
INSERT INTO `notification` VALUES ('93', 'Admin has renamed department testing to dept. Administrativ.', 'Admin has renamed department <a href=\"/departments/3\">testing</a> to <a href=\"/departments/3\">dept. Administrativ</a>.', 'department', '3', null, '1', '3', 'unread', '2019-09-18 18:02:50', null);

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permissions_key_index` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES ('1', 'browse_admin', null, '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('2', 'browse_bread', null, '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('3', 'browse_database', null, '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('4', 'browse_media', null, '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('5', 'browse_compass', null, '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('11', 'browse_roles', 'roles', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('12', 'read_roles', 'roles', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('13', 'edit_roles', 'roles', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('14', 'add_roles', 'roles', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('15', 'delete_roles', 'roles', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('16', 'browse_users', 'users', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('17', 'read_users', 'users', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('18', 'edit_users', 'users', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('19', 'add_users', 'users', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('20', 'delete_users', 'users', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('21', 'browse_settings', 'settings', '2019-07-24 21:22:13', '2019-07-24 21:22:13');
INSERT INTO `permissions` VALUES ('22', 'read_settings', 'settings', '2019-07-24 21:22:14', '2019-07-24 21:22:14');
INSERT INTO `permissions` VALUES ('23', 'edit_settings', 'settings', '2019-07-24 21:22:14', '2019-07-24 21:22:14');
INSERT INTO `permissions` VALUES ('24', 'add_settings', 'settings', '2019-07-24 21:22:14', '2019-07-24 21:22:14');
INSERT INTO `permissions` VALUES ('25', 'delete_settings', 'settings', '2019-07-24 21:22:14', '2019-07-24 21:22:14');
INSERT INTO `permissions` VALUES ('41', 'browse_hooks', null, '2019-07-24 21:22:26', '2019-07-24 21:22:26');
INSERT INTO `permissions` VALUES ('47', 'browse_departments', 'departments', '2019-07-26 03:19:20', '2019-07-26 03:19:20');
INSERT INTO `permissions` VALUES ('48', 'read_departments', 'departments', '2019-07-26 03:19:20', '2019-07-26 03:19:20');
INSERT INTO `permissions` VALUES ('49', 'edit_departments', 'departments', '2019-07-26 03:19:20', '2019-07-26 03:19:20');
INSERT INTO `permissions` VALUES ('50', 'add_departments', 'departments', '2019-07-26 03:19:20', '2019-07-26 03:19:20');
INSERT INTO `permissions` VALUES ('51', 'delete_departments', 'departments', '2019-07-26 03:19:20', '2019-07-26 03:19:20');

-- ----------------------------
-- Table structure for permission_role
-- ----------------------------
DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE `permission_role` (
  `permission_id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_permission_id_index` (`permission_id`),
  KEY `permission_role_role_id_index` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of permission_role
-- ----------------------------
INSERT INTO `permission_role` VALUES ('1', '1');
INSERT INTO `permission_role` VALUES ('1', '2');
INSERT INTO `permission_role` VALUES ('2', '1');
INSERT INTO `permission_role` VALUES ('2', '2');
INSERT INTO `permission_role` VALUES ('3', '1');
INSERT INTO `permission_role` VALUES ('4', '1');
INSERT INTO `permission_role` VALUES ('5', '1');
INSERT INTO `permission_role` VALUES ('11', '1');
INSERT INTO `permission_role` VALUES ('12', '1');
INSERT INTO `permission_role` VALUES ('13', '1');
INSERT INTO `permission_role` VALUES ('14', '1');
INSERT INTO `permission_role` VALUES ('15', '1');
INSERT INTO `permission_role` VALUES ('16', '1');
INSERT INTO `permission_role` VALUES ('17', '1');
INSERT INTO `permission_role` VALUES ('18', '1');
INSERT INTO `permission_role` VALUES ('18', '2');
INSERT INTO `permission_role` VALUES ('19', '1');
INSERT INTO `permission_role` VALUES ('20', '1');
INSERT INTO `permission_role` VALUES ('21', '1');
INSERT INTO `permission_role` VALUES ('22', '1');
INSERT INTO `permission_role` VALUES ('23', '1');
INSERT INTO `permission_role` VALUES ('24', '1');
INSERT INTO `permission_role` VALUES ('25', '1');
INSERT INTO `permission_role` VALUES ('41', '1');
INSERT INTO `permission_role` VALUES ('47', '1');
INSERT INTO `permission_role` VALUES ('47', '2');
INSERT INTO `permission_role` VALUES ('48', '1');
INSERT INTO `permission_role` VALUES ('48', '2');
INSERT INTO `permission_role` VALUES ('49', '1');
INSERT INTO `permission_role` VALUES ('49', '2');
INSERT INTO `permission_role` VALUES ('50', '1');
INSERT INTO `permission_role` VALUES ('50', '2');
INSERT INTO `permission_role` VALUES ('51', '1');
INSERT INTO `permission_role` VALUES ('51', '2');

-- ----------------------------
-- Table structure for procedure_data
-- ----------------------------
DROP TABLE IF EXISTS `procedure_data`;
CREATE TABLE `procedure_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folder_and_procedure_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `order` varchar(255) DEFAULT NULL,
  `content` longtext,
  `status` varchar(255) DEFAULT NULL,
  `attach` longtext,
  `user_id` bigint(20) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `additional_data` longtext,
  `step` varchar(255) DEFAULT NULL,
  `expand` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `folder_foreign_key` (`folder_and_procedure_id`),
  KEY `parent_foreign_key` (`parent_id`),
  KEY `user_foreign_key` (`user_id`),
  CONSTRAINT `folder_foreign_key` FOREIGN KEY (`folder_and_procedure_id`) REFERENCES `folder_and_procedure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `parent_foreign_key` FOREIGN KEY (`parent_id`) REFERENCES `procedure_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_foreign_key` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of procedure_data
-- ----------------------------
INSERT INTO `procedure_data` VALUES ('170', '4', 'BlockText', '1', '<p>testing</p>', 'active', null, '1', null, null, null, 'in', '2019-08-26 11:26:15', '2019-08-26 16:38:33');
INSERT INTO `procedure_data` VALUES ('171', '4', 'BlockImage', '2', '<div class=\"image-show- image-show-alignment-container171\" id=\"image-show-alignment-container-Image_171\">\n                  <img id=\"block_image-Image_171\" style=\"margin-top:1em;\" src=\"/procedure_images/15668216335d63cd01467d9.png\">\n                </div>', 'active', 'download.png', '1', null, '{\"size\":\"475\",\"align\":\"center\",\"file\":\"15668216335d63cd01467d9.png\"}', null, 'in', '2019-08-26 11:26:18', '2019-08-26 21:13:54');
INSERT INTO `procedure_data` VALUES ('172', '4', 'BlockAttachment', '3', '<div id=\"block-contents-Attachment_172\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n                    <div class=\"procedure-block\" id=\"procedure-block-172\">\n                      <a href=\"/block-attachments/15668838555d64c00f24e08.png/download\" class=\"download_attachment\"> Download user 1.png</a>\n                    </div>\n                </div>', 'active', 'user 1.png', '1', null, null, null, 'in', '2019-08-26 11:26:21', '2019-08-27 14:30:55');
INSERT INTO `procedure_data` VALUES ('173', '4', 'BlockProcedure', '4', '<div id=\"block-contents-Procedure_173\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n                 <h4><i class=\"fa fa-file-text\"></i> <a target=\"_blank\" href=\"/procedure/37\">External Procedure: 2344</a> <i class=\"fa fa-arrow-right\"></i></h4>\n              </div>', 'active', '2344', '1', null, '{\"embed\":\"37\"}', null, 'in', '2019-08-26 11:38:57', '2019-08-27 14:49:33');
INSERT INTO `procedure_data` VALUES ('174', '4', 'BlockVideo', '5', '<div class=\"procedure-block\" id=\"procedure-block-174\"><iframe width=\"480\" height=\"295\" frameborder=\"0\" allowfullscreen src=\"https://www.youtube.com/embed/_2SWI7kJrj0?wmode=transparent\"></iframe></div>', 'active', 'https://www.youtube.com/watch?v=_2SWI7kJrj0', '1', null, null, null, 'in', '2019-08-26 11:39:04', '2019-08-27 18:51:42');
INSERT INTO `procedure_data` VALUES ('185', '4', 'BlockText', '1', '<p>adfafaafaf</p>', 'pending', null, '1', '170', null, null, 'in', '2019-08-26 16:02:41', '2019-08-27 19:41:07');
INSERT INTO `procedure_data` VALUES ('206', '4', 'BlockVideo', '5', '<div class=\"procedure-block\" id=\"procedure-block-174\"><iframe width=\"480\" height=\"295\" frameborder=\"0\" allowfullscreen src=\"https://www.youtube.com/embed/gIBCXIyVy_8?wmode=transparent\"></iframe></div>', 'pending', 'event.mp4', '1', '174', null, null, 'in', '2019-08-27 14:28:45', '2019-08-27 19:42:42');
INSERT INTO `procedure_data` VALUES ('207', '4', 'BlockProcedure', '4', '<div id=\"block-contents-Procedure_173\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n             <h4><i class=\"fa fa-file-text\"></i> <a target=\"_blank\" href=\"/procedure/37\">External Procedure: 2344</a> <i class=\"fa fa-arrow-right\"></i></h4>\n          </div>', 'update', null, '1', '173', '{\"embed\":\"37\"}', null, 'in', '2019-08-27 14:36:15', '2019-09-14 21:24:30');
INSERT INTO `procedure_data` VALUES ('208', '4', 'BlockImage', '2', '<div class=\"image-show- image-show-alignment-container171\" id=\"image-show-alignment-container-Image_171\">\n                  <img id=\"block_image-Image_171\" style=\"margin-top:1em;\" src=\"/procedure_images/15684626305d7cd726420de.jpg\">\n                </div>', 'update', 'john_blakinger_3.jpg', '5', '171', '{\"size\":\"475\",\"align\":\"center\",\"file\":\"15684626305d7cd726420de.jpg\"}', null, 'in', '2019-08-27 14:41:31', '2019-09-19 12:23:38');
INSERT INTO `procedure_data` VALUES ('209', '4', 'BlockAttachment', '3', '<div id=\"block-contents-Attachment_172\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n                    <div class=\"procedure-block\" id=\"procedure-block-172\">\n                      <a href=\"/block-attachments/15684630595d7cd8d39537a.txt/download\" class=\"download_attachment\"> Download remaining works business documents.txt</a>\n                    </div>\n                </div>', 'update', 'remaining works business documents.txt', '5', '172', null, null, 'in', '2019-08-27 14:41:59', '2019-09-19 12:23:39');
INSERT INTO `procedure_data` VALUES ('210', '44', 'BlockImage', '1', '<h3 align=\"center\"> No image uploaded </h3>', 'active', null, null, null, null, null, 'in', '2019-09-02 11:55:19', null);
INSERT INTO `procedure_data` VALUES ('211', '40', 'BlockText', '1', '<p>parag</p>', 'active', null, '1', null, null, null, 'in', '2019-09-02 15:01:28', '2019-09-02 20:01:35');
INSERT INTO `procedure_data` VALUES ('215', '40', 'BlockText', '1', '<p>testing1234</p>', 'pending', null, '1', '211', null, null, 'in', '2019-09-02 15:37:13', null);
INSERT INTO `procedure_data` VALUES ('216', '40', 'BlockImage', '2', '<div class=\"image-show- image-show-alignment-container216\" id=\"image-show-alignment-container-Image_216\">\n                <img id=\"block_image-Image_216\" style=\"margin-top:1em;\" src=\"/procedure_images/15674253465d6d0342d8130.jpg\">\n              </div>', 'active', '2019-05-04-072759-Gerard_Donahue_02.jpg', '1', null, '{\"size\":\"475\",\"align\":\"center\",\"file\":\"15674253465d6d0342d8130.jpg\"}', null, 'in', '2019-09-02 15:55:35', '2019-09-02 20:55:48');
INSERT INTO `procedure_data` VALUES ('217', '40', 'BlockAttachment', '3', '<div id=\"block-contents-Attachment_217\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n                  <div class=\"procedure-block\" id=\"procedure-block-217\">\n                    <a href=\"/block-attachments/15674253615d6d035145965.txt/download\" class=\"download_attachment\"> Download Business.txt</a>\n                  </div>\n              </div>', 'active', 'Business.txt', '1', null, null, null, 'in', '2019-09-02 15:55:52', '2019-09-02 20:56:02');
INSERT INTO `procedure_data` VALUES ('218', '40', 'BlockProcedure', '4', '<div id=\"block-contents-Procedure_218\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n                 <h4><i class=\"fa fa-file-text\"></i> <a target=\"_blank\" href=\"/procedure/38\">External Procedure: 12345678</a> <i class=\"fa fa-arrow-right\"></i></h4>\n              </div>', 'active', '12345678', '1', null, '{\"embed\":\"38\"}', null, 'in', '2019-09-02 15:56:06', '2019-09-02 20:56:11');
INSERT INTO `procedure_data` VALUES ('219', '40', 'BlockVideo', '5', '<div class=\"procedure-block\" id=\"procedure-block-219\"><iframe width=\"480\" height=\"295\" frameborder=\"0\" allowfullscreen src=\"https://www.youtube.com/embed/gIBCXIyVy_8?wmode=transparent\"></iframe></div>', 'active', 'https://www.youtube.com/watch?v=gIBCXIyVy_8', '1', null, null, null, 'in', '2019-09-02 15:56:13', '2019-09-02 20:56:18');
INSERT INTO `procedure_data` VALUES ('220', '40', 'BlockAttachment', '3', '<div id=\"block-contents-Attachment_217\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n                    <div class=\"procedure-block\" id=\"procedure-block-217\">\n                      <a href=\"/block-attachments/15674283805d6d0f1c2ee22.png/download\" class=\"download_attachment\"> Download user 1.png</a>\n                    </div>\n                </div>', 'pending', 'user 1.png', '5', '217', null, null, 'in', '2019-09-02 16:46:20', '2019-09-19 12:23:40');
INSERT INTO `procedure_data` VALUES ('221', '40', 'BlockProcedure', '4', '<div id=\"block-contents-Procedure_218\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n             <h4><i class=\"fa fa-file-text\"></i> <a target=\"_blank\" href=\"/procedure/39\">External Procedure: teston</a> <i class=\"fa fa-arrow-right\"></i></h4>\n          </div>', 'pending', null, '1', '218', '{\"embed\":\"39\"}', null, 'in', '2019-09-02 16:48:51', '2019-09-02 21:48:52');
INSERT INTO `procedure_data` VALUES ('222', '40', 'BlockVideo', '5', '<div class=\"procedure-block\" id=\"procedure-block-219\"><iframe width=\"480\" height=\"295\" frameborder=\"0\" allowfullscreen src=\"https://www.youtube.com/embed/_2SWI7kJrj0?wmode=transparent\"></iframe></div>', 'pending', null, '5', '219', null, null, 'in', '2019-09-02 16:50:38', '2019-09-19 12:23:41');
INSERT INTO `procedure_data` VALUES ('223', '41', 'BlockText', '1', '<p>tests</p>', 'active', null, '1', null, null, null, 'in', '2019-09-11 09:47:50', '2019-09-11 14:47:52');
INSERT INTO `procedure_data` VALUES ('224', '41', 'BlockImage', '2', '<div class=\"image-show- image-show-alignment-container224\" id=\"image-show-alignment-container-Image_224\">\n                  <img id=\"block_image-Image_224\" style=\"margin-top:1em;\" src=\"/procedure_images/15682915575d7a3ae5b1dc9.png\">\n                </div>', 'active', 'john_blakinger_3.jpg', '1', null, '{\"size\":\"171.31\",\"align\":\"center\",\"file\":\"15681808785d788a8e353d0.jpg\"}', null, 'in', '2019-09-11 09:47:54', '2019-09-13 22:12:47');
INSERT INTO `procedure_data` VALUES ('226', '40', 'BlockText', '1', '<p>parag</p>', 'active', null, '1', null, null, null, 'in', '2019-09-11 11:53:48', '2019-09-11 11:53:48');
INSERT INTO `procedure_data` VALUES ('227', '40', 'BlockText', '1', '<p>testing 123</p>', 'accept', null, '5', '211', null, null, 'in', '2019-09-11 14:26:47', '2019-09-19 12:23:43');
INSERT INTO `procedure_data` VALUES ('228', '37', 'BlockText', '1', null, 'active', null, null, null, null, null, 'in', '2019-09-11 15:35:41', null);
INSERT INTO `procedure_data` VALUES ('229', '40', 'BlockText', '1', '<p>testing 56789</p>', 'pending', null, '5', '211', null, null, 'in', '2019-09-11 14:26:47', '2019-09-19 12:23:56');
INSERT INTO `procedure_data` VALUES ('230', '41', 'BlockText', '1', '<p>User 87656</p>', 'reject', null, '5', '223', null, null, 'in', '2019-09-11 09:56:54', '2019-09-19 12:23:57');
INSERT INTO `procedure_data` VALUES ('232', '41', 'BlockAttachment', '3', '<div id=\"block-contents-Attachment_232\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n                  <div class=\"procedure-block\" id=\"procedure-block-232\">\n                    <a href=\"/block-attachments/15682733265d79f3ae0e3c0.txt/download\" class=\"download_attachment\"> Download remaining works business documents.txt</a>\n                  </div>\n              </div>', 'active', 'remaining works business documents.txt', '1', null, null, null, 'in', '2019-09-12 11:28:40', '2019-09-12 16:28:46');
INSERT INTO `procedure_data` VALUES ('233', '41', 'BlockProcedure', '4', '<div id=\"block-contents-Procedure_\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n             <h4><i class=\"fa fa-file-text\"></i> <a target=\"_blank\" href=\"/procedure/38\">External Procedure: 12345678</a> <i class=\"fa fa-arrow-right\"></i></h4>\n          </div>', 'pending', '12345678', '1', null, '{\"embed\":\"38\"}', null, 'in', '2019-09-12 11:28:48', '2019-09-14 22:00:11');
INSERT INTO `procedure_data` VALUES ('234', '41', 'BlockVideo', '5', '<div class=\"procedure-block\" id=\"procedure-block-234\"><iframe width=\"480\" height=\"295\" frameborder=\"0\" allowfullscreen src=\"https://www.youtube.com/embed/RP8oirg6DNg?wmode=transparent\"></iframe></div>', 'active', 'https://www.youtube.com/watch?v=RP8oirg6DNg', '1', null, null, null, 'in', '2019-09-12 11:28:54', '2019-09-12 16:28:58');
INSERT INTO `procedure_data` VALUES ('237', '41', 'BlockText', '1', '<p>testsdfdfe</p>', 'pending', null, '1', '223', null, null, 'in', '2019-09-12 11:44:31', '2019-09-13 22:11:59');
INSERT INTO `procedure_data` VALUES ('242', '41', 'BlockAttachment', '3', '<div id=\"block-contents-Attachment_232\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n                    <div class=\"procedure-block\" id=\"procedure-block-232\">\n                      <a href=\"/block-attachments/15682854865d7a232e8ebbd.png/download\" class=\"download_attachment\"> Download user 1.png</a>\n                    </div>\n                </div>', 'accept', 'user 1.png', '5', '232', null, null, 'in', '2019-09-12 14:51:26', '2019-09-19 12:24:01');
INSERT INTO `procedure_data` VALUES ('243', '41', 'BlockVideo', '5', '<div id=\"block-contents-Video_234\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n                    <video width=\"400\" controls>\n                      <source src=\"/procedure_videos/15686135165d7f248c1d9d3.mp4\" type=\"video/mp4\">\n                    </video>\n                </div>', 'pending', 'testing_video.mp4', '1', '234', null, null, 'in', '2019-09-12 14:53:04', '2019-09-16 14:58:36');
INSERT INTO `procedure_data` VALUES ('245', '40', 'BlockText', '1', '<p>parag</p>', 'pending', null, '1', '226', null, null, 'in', '2019-09-14 15:52:34', null);
INSERT INTO `procedure_data` VALUES ('246', '4', 'BlockProcedure', '4', '<div id=\"block-contents-Procedure_173\" class=\"portlet-body procedure-block-body\" style=\"display:block\">\n             <h4><i class=\"fa fa-file-text\"></i> <a target=\"_blank\" href=\"/procedure/45\">External Procedure: contributor proc</a> <i class=\"fa fa-arrow-right\"></i></h4>\n          </div>', 'pending', null, '5', '173', '{\"embed\":\"45\"}', null, 'in', '2019-09-14 17:19:00', '2019-09-19 12:24:00');
INSERT INTO `procedure_data` VALUES ('247', '41', 'BlockVideo', '5', '<div class=\"procedure-block\" id=\"procedure-block-234\"><iframe width=\"480\" height=\"295\" frameborder=\"0\" allowfullscreen src=\"https://www.youtube.com/embed/RP8oirg6DNg?wmode=transparent\"></iframe></div>', 'accept', null, '5', '234', null, null, 'in', '2019-09-16 10:14:38', '2019-09-19 12:24:02');
INSERT INTO `procedure_data` VALUES ('248', '50', 'BlockText', '1', '<p>afsf</p>', 'active', null, '5', null, null, null, 'in', '2019-09-17 09:50:44', '2019-09-19 12:24:03');
INSERT INTO `procedure_data` VALUES ('249', '51', 'BlockText', '1', '<p>afaf</p>', 'active', null, '5', null, null, null, 'in', '2019-09-17 12:39:41', '2019-09-19 12:24:03');

-- ----------------------------
-- Table structure for procedure_data_comment
-- ----------------------------
DROP TABLE IF EXISTS `procedure_data_comment`;
CREATE TABLE `procedure_data_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `procedure_data_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `procedure_data_foreign_key` (`procedure_data_id`),
  KEY `user_foreign_key_011` (`user_id`),
  CONSTRAINT `procedure_data_foreign_key` FOREIGN KEY (`procedure_data_id`) REFERENCES `procedure_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_foreign_key_011` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of procedure_data_comment
-- ----------------------------
INSERT INTO `procedure_data_comment` VALUES ('15', '211', '1', 'comments', '2019-09-02 15:01:56', null);

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES ('1', 'admin', 'Admin', '2019-07-24 21:22:12', '2019-07-25 22:25:37');
INSERT INTO `roles` VALUES ('2', 'user', 'User', '2019-07-24 21:22:12', '2019-07-25 22:25:24');

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `details` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of settings
-- ----------------------------
INSERT INTO `settings` VALUES ('1', 'site.title', 'Site Title', 'Business Documentation Software', '', 'text', '1', 'Site');
INSERT INTO `settings` VALUES ('2', 'site.description', 'Site Description', 'Welcome to Business Documentation Software', '', 'text', '2', 'Site');
INSERT INTO `settings` VALUES ('3', 'site.logo', 'Site Logo', '', '', 'image', '3', 'Site');
INSERT INTO `settings` VALUES ('4', 'site.google_analytics_tracking_id', 'Google Analytics Tracking ID', null, '', 'text', '4', 'Site');
INSERT INTO `settings` VALUES ('5', 'admin.bg_image', 'Admin Background Image', '', '', 'image', '5', 'Admin');
INSERT INTO `settings` VALUES ('6', 'admin.title', 'Admin Title', 'SCHWARZ', '', 'text', '1', 'Admin');
INSERT INTO `settings` VALUES ('7', 'admin.description', 'Admin Description', 'Welcome to SCHWARZ', '', 'text', '2', 'Admin');
INSERT INTO `settings` VALUES ('8', 'admin.loader', 'Admin Loader', '', '', 'image', '3', 'Admin');
INSERT INTO `settings` VALUES ('9', 'admin.icon_image', 'Admin Icon Image', 'settings\\July2019\\Xu8TQxFzTNwjipIY8wA3.png', '', 'image', '4', 'Admin');
INSERT INTO `settings` VALUES ('10', 'admin.google_analytics_client_id', 'Google Analytics Client ID (used for admin dashboard)', null, '', 'text', '1', 'Admin');

-- ----------------------------
-- Table structure for translations
-- ----------------------------
DROP TABLE IF EXISTS `translations`;
CREATE TABLE `translations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `column_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foreign_key` int(10) NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `translations_table_name_column_name_foreign_key_locale_unique` (`table_name`,`column_name`,`foreign_key`,`locale`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of translations
-- ----------------------------
INSERT INTO `translations` VALUES ('1', 'data_types', 'display_name_singular', '5', 'pt', 'Post', '2019-07-24 21:22:23', '2019-07-24 21:22:23');
INSERT INTO `translations` VALUES ('2', 'data_types', 'display_name_singular', '6', 'pt', 'Página', '2019-07-24 21:22:23', '2019-07-24 21:22:23');
INSERT INTO `translations` VALUES ('3', 'data_types', 'display_name_singular', '1', 'pt', 'Utilizador', '2019-07-24 21:22:23', '2019-07-24 21:22:23');
INSERT INTO `translations` VALUES ('4', 'data_types', 'display_name_singular', '4', 'pt', 'Categoria', '2019-07-24 21:22:23', '2019-07-24 21:22:23');
INSERT INTO `translations` VALUES ('5', 'data_types', 'display_name_singular', '2', 'pt', 'Menu', '2019-07-24 21:22:23', '2019-07-24 21:22:23');
INSERT INTO `translations` VALUES ('6', 'data_types', 'display_name_singular', '3', 'pt', 'Função', '2019-07-24 21:22:23', '2019-07-24 21:22:23');
INSERT INTO `translations` VALUES ('7', 'data_types', 'display_name_plural', '5', 'pt', 'Posts', '2019-07-24 21:22:23', '2019-07-24 21:22:23');
INSERT INTO `translations` VALUES ('8', 'data_types', 'display_name_plural', '6', 'pt', 'Páginas', '2019-07-24 21:22:23', '2019-07-24 21:22:23');
INSERT INTO `translations` VALUES ('9', 'data_types', 'display_name_plural', '1', 'pt', 'Utilizadores', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('10', 'data_types', 'display_name_plural', '4', 'pt', 'Categorias', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('11', 'data_types', 'display_name_plural', '2', 'pt', 'Menus', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('12', 'data_types', 'display_name_plural', '3', 'pt', 'Funções', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('13', 'categories', 'slug', '1', 'pt', 'categoria-1', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('14', 'categories', 'name', '1', 'pt', 'Categoria 1', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('15', 'categories', 'slug', '2', 'pt', 'categoria-2', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('16', 'categories', 'name', '2', 'pt', 'Categoria 2', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('17', 'pages', 'title', '1', 'pt', 'Olá Mundo', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('18', 'pages', 'slug', '1', 'pt', 'ola-mundo', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('19', 'pages', 'body', '1', 'pt', '<p>Olá Mundo. Scallywag grog swab Cat o\'nine tails scuttle rigging hardtack cable nipper Yellow Jack. Handsomely spirits knave lad killick landlubber or just lubber deadlights chantey pinnace crack Jennys tea cup. Provost long clothes black spot Yellow Jack bilged on her anchor league lateen sail case shot lee tackle.</p>\r\n<p>Ballast spirits fluke topmast me quarterdeck schooner landlubber or just lubber gabion belaying pin. Pinnace stern galleon starboard warp carouser to go on account dance the hempen jig jolly boat measured fer yer chains. Man-of-war fire in the hole nipperkin handsomely doubloon barkadeer Brethren of the Coast gibbet driver squiffy.</p>', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('20', 'menu_items', 'title', '1', 'pt', 'Painel de Controle', '2019-07-24 21:22:24', '2019-07-24 21:22:24');
INSERT INTO `translations` VALUES ('21', 'menu_items', 'title', '2', 'pt', 'Media', '2019-07-24 21:22:25', '2019-07-24 21:22:25');
INSERT INTO `translations` VALUES ('22', 'menu_items', 'title', '12', 'pt', 'Publicações', '2019-07-24 21:22:25', '2019-07-24 21:22:25');
INSERT INTO `translations` VALUES ('23', 'menu_items', 'title', '3', 'pt', 'Utilizadores', '2019-07-24 21:22:25', '2019-07-24 21:22:25');
INSERT INTO `translations` VALUES ('24', 'menu_items', 'title', '11', 'pt', 'Categorias', '2019-07-24 21:22:25', '2019-07-24 21:22:25');
INSERT INTO `translations` VALUES ('25', 'menu_items', 'title', '13', 'pt', 'Páginas', '2019-07-24 21:22:25', '2019-07-24 21:22:25');
INSERT INTO `translations` VALUES ('26', 'menu_items', 'title', '4', 'pt', 'Funções', '2019-07-24 21:22:25', '2019-07-24 21:22:25');
INSERT INTO `translations` VALUES ('27', 'menu_items', 'title', '5', 'pt', 'Ferramentas', '2019-07-24 21:22:25', '2019-07-24 21:22:25');
INSERT INTO `translations` VALUES ('28', 'menu_items', 'title', '6', 'pt', 'Menus', '2019-07-24 21:22:25', '2019-07-24 21:22:25');
INSERT INTO `translations` VALUES ('29', 'menu_items', 'title', '7', 'pt', 'Base de dados', '2019-07-24 21:22:25', '2019-07-24 21:22:25');
INSERT INTO `translations` VALUES ('30', 'menu_items', 'title', '10', 'pt', 'Configurações', '2019-07-24 21:22:25', '2019-07-24 21:22:25');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'users/default.png',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settings` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', '1', 'Admin', 'admin@admin.com', 'users/default.png', null, '$2y$10$pLMnKiwv9orgII4CrXcMF.FtBo5dtm5JskbmvmtWjHUoFdDGGTt9e', 'euwWgvoTFTYEcYUIGwRJwCIg8F5w9x3u7zG3OrqiDNO1RvtwGkd7DyPXOvvD', null, '2019-07-25 21:37:39', '2019-09-19 10:15:26');
INSERT INTO `users` VALUES ('5', '1', 'Daniel Landa', 'danielracu189@gmail.com', 'users/default.png', null, '$2y$10$E35H3NuLPKaOAxkI4VcHB.Cw8pVD1kuEfKHRDCq5IZi/zFcdJIDa6', null, null, '2019-09-18 17:29:50', '2019-09-18 18:06:57');
INSERT INTO `users` VALUES ('6', '2', 'Ciprian Duca', 'ciprian.duca@schwarzgruppeint.ro', 'users/default.png', null, '$2y$10$ufUxoZWDE8XDQLbtnLph.eMWx0RAISFSXhlhX4aw2OkRPlLlVuIsW', null, null, '2019-09-18 18:01:04', '2019-09-18 18:01:04');
INSERT INTO `users` VALUES ('7', '2', 'amin shoukat', 'aminshoukat4@gmail.com', 'users/default.png', null, '$2y$10$udcC7S1FgRIvz5Ct7e7.LOuLXwqCVaZwZtB.1uYzMVqDRHtkU7Kti', null, null, '2019-09-19 09:54:23', '2019-09-19 10:01:43');

-- ----------------------------
-- Table structure for user_department
-- ----------------------------
DROP TABLE IF EXISTS `user_department`;
CREATE TABLE `user_department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_foreign_key` (`department_id`),
  KEY `user_foreing` (`user_id`),
  CONSTRAINT `department_foreign_key` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_foreing` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_department
-- ----------------------------
INSERT INTO `user_department` VALUES ('1', '1', '2', 'publisher');
INSERT INTO `user_department` VALUES ('4', '5', '2', 'contributor');
INSERT INTO `user_department` VALUES ('9', '5', '3', 'publisher');

-- ----------------------------
-- Table structure for user_roles
-- ----------------------------
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_roles_user_id_index` (`user_id`),
  KEY `user_roles_role_id_index` (`role_id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of user_roles
-- ----------------------------
SET FOREIGN_KEY_CHECKS=1;
