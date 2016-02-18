SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `cms_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `params` varchar(255) NOT NULL,
  `format` varchar(10) NOT NULL DEFAULT '',
  `module_id` int(100) NOT NULL DEFAULT '0',
  `module` varchar(255) NOT NULL DEFAULT '',
  `expire_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data` longtext NOT NULL,
  `headers` text NOT NULL,
  PRIMARY KEY (`params`,`format`,`module`,`expire_date`,`module_id`),
  KEY `module` (`module_id`,`module`),
  FULLTEXT KEY `headers_json` (`headers`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `config_id` int(100) NOT NULL AUTO_INCREMENT,
  `zone` varchar(50) DEFAULT NULL,
  `zone_group` varchar(50) NOT NULL DEFAULT 'general',
  `name` varchar(100) NOT NULL,
  `value` text,
  `options` text,
  PRIMARY KEY (`config_id`),
  KEY `zone` (`zone`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=84 ;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`config_id`, `zone`, `zone_group`, `name`, `value`, `options`) VALUES
(6, 'website', 'general', 'site_title', 'CMS', NULL),
(3, 'manage', 'theme', 'highlight_color', '#ff005a', NULL),
(4, 'manage', 'theme', 'header_image', '/files/admin_logo.png', '{"input_type":"file", "note":"Max Height: 55px"}'),
(5, 'website', 'general', 'site_active', 'TRUE', '{"input_type":"bool"}'),
(7, 'website', 'general', 'title_seperator', ' : ', NULL),
(8, 'website', 'general', 'cache_enabled', 'FALSE', '{"input_type":"bool"}'),
(10, 'file', 'general', 'force_name_in_uri', 'FALSE', '{"input_type":"bool"}'),
(63, 'website_layout', 'assets', 'compression_minify', 'TRUE', '{"input_type":"bool"}'),
(11, 'file', 'general', 'allowed_types', 'gif|jpg|png|pdf|flv|jpeg|mp4|m4v|docx|doc|xlsx|mp3', NULL),
(12, 'file', 'general', 'max_size', '40000', NULL),
(13, 'file', 'general', 'max_width', '2400', NULL),
(14, 'file', 'general', 'max_height', '2400', NULL),
(15, 'page', 'general', 'trim_input_values', 'FALSE', '{"input_type":"bool"}'),
(53, 'website', 'custom_404', 'enabled', 'FALSE', '{"input_type":"bool"}'),
(19, 'page', 'general', 'load_page_links', 'TRUE', '{"input_type":"bool"}'),
(20, 'page', 'general', 'allow_page_links', 'TRUE', '{"input_type":"bool"}'),
(62, 'website_layout', 'assets', 'compression_enabled', 'FALSE', '{"input_type":"bool"}'),
(23, 'manage', 'general', 'google_api_key', '', '{"note":"Google API JS will be embedded in the admin if there is a key value is provided"}'),
(54, 'website', 'custom_404', 'path', '/site/404', NULL),
(40, 'manage', 'login', 'limit_inactive_days', '', NULL),
(27, 'manage', 'general', 'site_title', 'Website', NULL),
(29, 'website', 'general', 'default_path', '/', '{"note":"Include leading slash"}'),
(61, 'website_layout', 'assets', 'cache_directory', 'cache/', ''),
(60, 'website_layout', 'assets', 'add_app_path', 'FALSE', '{"input_type":"bool"}'),
(32, 'website', 'general', 'google_analytics_id', '', NULL),
(34, 'manage', 'login', 'req_password_len', '6', NULL),
(35, 'manage', 'login', 'expiration_mins', '', '{"note":"Leave blank not to expire logged in users."}'),
(36, 'manage', 'login', 'limit_failed_login', 'FALSE', '{"input_type":"bool"}'),
(37, 'manage', 'login', 'limit_failed_mins', '', NULL),
(38, 'manage', 'login', 'limit_inactive_login', 'FALSE', '{"input_type":"bool"}'),
(39, 'manage', 'login', 'instructions', '', NULL),
(49, 'manage', 'publish', 'remote_url', '', NULL),
(55, 'manage', 'workflow', 'send_immediate_emails', 'TRUE', '{"input_type":"bool"}'),
(56, 'manage', 'workflow', 'send_daily_emails', 'TRUE', '{"input_type":"bool"}'),
(57, 'manage', 'publish', 'send_publish_report_email', 'TRUE', '{"input_type":"bool"}'),
(46, 'manage', 'publish', 'remote_auth_code', '', ''),
(41, 'manage', 'publish', 'publish_url', '[""]', '{"multi":"yes"}'),
(58, 'manage', 'publish', 'publish_report_address', 'email@domain.com', NULL),
(64, 'website_layout', 'asset_sets', 'js_top', '[""]', '{"input_type":"file", "multi":"yes"}'),
(82, 'website_layout', 'asset_sets', 'js_bottom', '[""]', '{"input_type":"file", "multi":"yes"}'),
(71, 'website', 'meta', 'head_extra', '', '{"input_type":"textarea"}'),
(65, 'website_layout', 'asset_sets', 'css_all', '[""]', '{"input_type":"file", "multi":"yes"}'),
(72, 'website_layout', 'asset_sets', 'css_pdf', '[""]', '{"input_type":"file", "multi":"yes"}'),
(66, 'website_layout', 'asset_sets', 'cssiphone', '', '{"input_type":"file", "multi":"yes"}'),
(67, 'website_layout', 'asset_sets', 'css_print', '', '{"input_type":"file", "multi":"yes"}'),
(68, 'website', 'meta', 'meta_description', '', '{"input_type":"text", "note":"(Global)"}'),
(69, 'website', 'meta', 'meta_image', '', '{"input_type":"text", "note":"(Global)"}'),
(70, 'manage', 'general', 'lang', '["EN"]', '{"multi":"yes","note":"WARNING: Changing this could result in data loss. Be careful."}'),
(73, 'manage', 'publish', 'staging_shows', 'draft', '{"input_type":"select","options":"draft,published"}'),
(74, 'manage', 'publish', 'publish_method', 'local_table', '{"input_type":"select","options":"local_table,remote_db"}'),
(75, 'manage', 'publish', 'live_domain', 'www.domain.com', '""'),
(76, 'manage', 'publish', 'staging_domain', 'staging.domain.com', '""'),
(77, 'website', 'password', 'password', '123', '""'),
(78, 'website', 'password', 'protect_site', 'FALSE', '{"input_type":"bool"}'),
(79, 'website', 'password', 'domain', 'staging.domain.com', '{''note'':''Just domain, no http. Example staging.domain.com''}'),
(80, 'file', 'general', 'jpg_quality', '100', '""'),
(81, 'page', 'general', 'allowed_html_tags', '<b><i><sup><sub><em><strong><u><br><iframe><a><ul><ol><li><p><div><h1><h2><h3><h4><h5><script><embed>', '""'),
(83, 'website', 'general', 'author', '', '""');

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `file_id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `mime` varchar(100) NOT NULL DEFAULT '',
  `ext` varchar(10) DEFAULT NULL,
  `is_image` int(1) DEFAULT '0',
  `import_id` int(100) NOT NULL,
  `options` text,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `page_id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `sort` int(3) DEFAULT NULL,
  `type` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `template_id` int(10) DEFAULT NULL,
  `file_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `file_title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `approve_date` datetime DEFAULT '0000-00-00 00:00:00',
  `publish_date` datetime DEFAULT '0000-00-00 00:00:00',
  `update_date` datetime DEFAULT '0000-00-00 00:00:00',
  `content_start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `content_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `options` text CHARACTER SET utf8,
  `content` text CHARACTER SET utf8,
  `meta_title` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `meta_image` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `import_id` varchar(100) NOT NULL,
  `source_id` int(100) NOT NULL,
  `tracking_js` text COLLATE utf8_bin NOT NULL,
  `tracking_omniture` text COLLATE utf8_bin NOT NULL,
  `tracking_other` text COLLATE utf8_bin NOT NULL,
  `attribute_values` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `sPath` (`path`),
  KEY `ixParent` (`parent_id`),
  KEY `sModule+sType` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

CREATE TABLE IF NOT EXISTS `template` (
  `template_id` int(10) NOT NULL AUTO_INCREMENT,
  `template_file_name` varchar(100) NOT NULL,
  `template_html_xsl_path` varchar(200) DEFAULT NULL,
  `template_xml_xsl_path` varchar(200) DEFAULT NULL,
  `template_options` text CHARACTER SET utf8 COLLATE utf8_bin,
  `template_attributes` text DEFAULT NULL,  
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `page_attributegroup`
--

CREATE TABLE `page_attributegroup` (
  `page_attributegroup_id` int(200) NOT NULL AUTO_INCREMENT,
  `group_title` varchar(200) NOT NULL,
  `group_key` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL DEFAULT 'attribute_group',
  PRIMARY KEY (`page_attributegroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `page_attributejoin`
--

CREATE TABLE `page_attributejoin` (
  `page_attributejoin_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `page_attributegroup_id` int(200) NOT NULL,
  `page_attributevalue_id` int(200) NOT NULL,
  PRIMARY KEY (`page_attributejoin_id`),
  KEY `page_id` (`page_id`,`template_id`,`page_attributegroup_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `page_attributevalue`
--


CREATE TABLE `page_attributevalue` (
  `page_attributevalue_id` int(100) NOT NULL AUTO_INCREMENT,
  `page_attributegroup_id` int(100) NOT NULL,
  `value_title` varchar(200) NOT NULL,
  `value_key` varchar(200) NOT NULL,
  `value_short_title` varchar(255) NOT NULL,
  `value_image` varchar(255) NOT NULL,
  `value_image_id` int(100) NOT NULL,
  `value_text` text NOT NULL,
  `type` varchar(200) NOT NULL DEFAULT 'attribute_value',
  PRIMARY KEY (`page_attributevalue_id`),
  KEY `page_attributegroup_id` (`page_attributegroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



-- --------------------------------------------------------
--
-- Table structure for table `page_redirect
--

CREATE TABLE `page_redirect` (
  `redirect_id` int(100) NOT NULL AUTO_INCREMENT,
  `old_path` varchar(255) NOT NULL,
  `new_path` varchar(255) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `editor_id` int(10) NOT NULL,
  `type` varchar(100) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`redirect_id`),
  KEY `old_path` (`old_path`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;