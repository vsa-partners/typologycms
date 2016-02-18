SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cms_manage`
--

-- --------------------------------------------------------
--
-- Table structure for table `activity`
--

CREATE TABLE IF NOT EXISTS `activity` (
  `activity_id` int(255) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL DEFAULT '0',
  `module_id` int(10) NOT NULL DEFAULT '0',
  `user_id` varchar(10) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`activity_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


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
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_bin NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


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
(6, 'website', 'general', 'site_title', 'TypologyCMS', NULL),
(3, 'manage', 'theme', 'highlight_color', '#ff005a', NULL),
(4, 'manage', 'theme', 'header_image', '/assets/img/vsa_logo.png', '{"input_type":"file", "note":"Max Height: 55px"}'),
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
(29, 'website', 'general', 'default_path', '/home', '{"note":"Include leading slash"}'),
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
(65, 'website_layout', 'asset_sets', 'css_all', '["\\/assets\\/css\\/reset.css","\\/assets\\/css\\/style.css"]', '{"input_type":"file", "multi":"yes"}'),
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
  `import_id` int(100) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `mime` varchar(100) NOT NULL DEFAULT '',
  `ext` varchar(10) DEFAULT NULL,
  `is_image` int(1) DEFAULT '0',
  `options` text,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `file` (`file_id`, `import_id`, `title`, `file_name`, `type`, `mime`, `ext`, `is_image`, `options`, `parent_id`, `create_date`, `update_date`) VALUES
(1, 0, 'starterfiles', '', 'collection', '', NULL, 0, NULL, 0, '2016-02-18 12:18:45', '2016-02-18 12:18:45'),
(2, 0, 'InstallingTypologyCMS', 'installingtypologycms', 'file', 'application/pdf', '.pdf', 0, '{"image_size_str":"","image_width":"","image_height":""}', 1, '2016-02-18 12:19:18', '2016-02-18 12:19:18');

-- --------------------------------------------------------
--
-- Table structure for table `file_queue`
--

CREATE TABLE IF NOT EXISTS `file_queue` (
  `file_id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `mime` varchar(100) NOT NULL DEFAULT '',
  `ext` varchar(10) DEFAULT NULL,
  `is_image` int(1) DEFAULT '0',
  `options` text,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `queue_file_path` varchar(255) NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------
--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `page_id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `status` int(2) DEFAULT '1',
  `sort` int(3) DEFAULT NULL,
  `module` varchar(50) CHARACTER SET utf8 NOT NULL,
  `type` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `template_id` int(10) DEFAULT NULL,
  `editor_id` int(10) NOT NULL,
  `approver_id` int(10) DEFAULT NULL,
  `file_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `file_title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `create_date` datetime DEFAULT '0000-00-00 00:00:00',
  `update_date` datetime DEFAULT '0000-00-00 00:00:00',
  `approve_date` datetime DEFAULT '0000-00-00 00:00:00',
  `publish_date` datetime DEFAULT '0000-00-00 00:00:00',
  `queue_date` datetime DEFAULT '0000-00-00 00:00:00',
  `content_start_date` datetime NOT NULL,
  `content_end_date` datetime NOT NULL,
  `options` text CHARACTER SET utf8,
  `content` text CHARACTER SET utf8,
  `meta_title` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `meta_image` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `tracking_js` text COLLATE utf8_bin NOT NULL,
  `tracking_omniture` text COLLATE utf8_bin NOT NULL,
  `tracking_other` text COLLATE utf8_bin NOT NULL,
  `import_id` varchar(100) NOT NULL,
  `source_id` int(100) NOT NULL,
  `attribute_values` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `sPath` (`path`),
  KEY `ixParent` (`parent_id`),
  KEY `sModule+sType` (`type`),
  KEY `user_id` (`editor_id`),
  KEY `content_start_date` (`content_start_date`,`content_end_date`),
  KEY `content_start_date_2` (`content_start_date`,`content_end_date`,`template_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`page_id`, `title`, `status`, `sort`, `module`, `type`, `parent_id`, `template_id`, `editor_id`, `approver_id`, `file_name`, `file_title`, `path`, `create_date`, `update_date`, `approve_date`, `publish_date`, `queue_date`, `content_start_date`, `content_end_date`, `options`, `content`, `meta_title`, `meta_description`, `meta_image`, `tracking_js`, `tracking_omniture`, `tracking_other`, `import_id`, `source_id`, `attribute_values`) VALUES
(1, 'Website', 20, 3, 'page', 'root', 0, 81, 10, 50, 'website', 'Website', '/', '2009-08-27 15:30:14', '2012-04-03 12:35:06', '2012-04-03 12:35:06', '2012-04-03 12:35:06', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '{"include_sitemap":"no"}', '', '', '', '', '', '', '', '', 0, ''),
(2, 'My first page', 20, 0, 'page', 'page', 1, 1, 1, 1, 'home', 'home', '/home', '2016-02-17 17:32:52', '2016-02-18 12:22:20', '2016-02-18 12:22:20', '2016-02-18 12:22:20', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '{"include_sitemap":"yes"}', '\n<data>\n  <header_copy><![CDATA[Welcome to your first page]]></header_copy>\n  <body_copy><![CDATA[This is the first page of the website. We have put a demo page in here so you can see some examples of content types, how a page is structured in the CMS and how the XSL templating works.  \n\nTo get started with your CMS you can try some of the following:]]></body_copy>\n  <download>\n    <button_text><![CDATA[Download the installer guide]]></button_text>\n    <download_file file_path="/FILE/2/installingtypologycms.pdf" file_title="InstallingTypologyCMS" file_id="2"/>\n  </download>\n  <sections>\n    <section>\n      <title><![CDATA[Login to the Manage interface]]></title>\n      <text><![CDATA[The administrative interface is at /manage/. This is where you can create pages, create templates, and edit and create content. ]]></text>\n      <button link_title="Go to /manage" target="_self"><![CDATA[/manage]]></button>\n    </section>\n    <section>\n      <title><![CDATA[Look at the XSL templates]]></title>\n      <text><![CDATA[Content is rendered to HTML via XSL. Go to /cms/templates folder on your server. There you will find a bunch of .xsl files. Take a look at starter_page.xsl to see the source template for this page. ]]></text>\n      <button link_title="XSL documentation" target="_self"><![CDATA[http://www.w3schools.com/xsl/xsl_languages.asp]]></button>\n    </section>\n    <section>\n      <title><![CDATA[Edit the site CSS and JS]]></title>\n      <text><![CDATA[The header and footer are automatically generated by the CMS, allowing you to manage js and css through the CMS. If you want to change CSS and JS files, check out the config section of the manage interface.]]></text>\n      <button link_title="Manage your layout config" target="_self"><![CDATA[/manage/config/website_layout]]></button>\n    </section>\n    <section>\n      <title><![CDATA[Learn more about CI]]></title>\n      <text><![CDATA[Codeigniter is the framework underlying the TypologyCMS. You can build and customize sites without ever touching PHP, but if you want to extend TypologyCMS it would help to know CodeIgniter.]]></text>\n      <button link_title="CodeIgniter Web Framework" target="_self"><![CDATA[https://www.codeigniter.com/]]></button>\n    </section>\n  </sections>\n</data>\n', '', '', '', '{"misc":""}', '', '', '', 0, '');


-- --------------------------------------------------------
--
-- Table structure for table `page_published`
--

CREATE TABLE IF NOT EXISTS `page_published` (
  `page_id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `status` int(2) DEFAULT '1',
  `sort` int(3) DEFAULT NULL,
  `module` varchar(50) CHARACTER SET utf8 NOT NULL,
  `type` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `template_id` int(10) DEFAULT NULL,
  `editor_id` int(10) NOT NULL,
  `approver_id` int(10) DEFAULT NULL,
  `file_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `file_title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `create_date` datetime DEFAULT '0000-00-00 00:00:00',
  `update_date` datetime DEFAULT '0000-00-00 00:00:00',
  `approve_date` datetime DEFAULT '0000-00-00 00:00:00',
  `publish_date` datetime DEFAULT '0000-00-00 00:00:00',
  `queue_date` datetime DEFAULT '0000-00-00 00:00:00',
  `content_start_date` datetime NOT NULL,
  `content_end_date` datetime NOT NULL,
  `options` text CHARACTER SET utf8,
  `content` text CHARACTER SET utf8,
  `meta_title` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `meta_image` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `tracking_js` text COLLATE utf8_bin NOT NULL,
  `tracking_omniture` text COLLATE utf8_bin NOT NULL,
  `tracking_other` text COLLATE utf8_bin NOT NULL,
  `import_id` varchar(100) NOT NULL,
  `source_id` int(100) NOT NULL,
  `attribute_values` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `sPath` (`path`),
  KEY `ixParent` (`parent_id`),
  KEY `sModule+sType` (`type`),
  KEY `user_id` (`editor_id`),
  KEY `content_start_date` (`content_start_date`,`content_end_date`),
  KEY `content_start_date_2` (`content_start_date`,`content_end_date`,`template_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


-- --------------------------------------------------------
--
-- Table structure for table `page_versions`
--

CREATE TABLE IF NOT EXISTS `page_versions` (
  `version_id` int(100) NOT NULL AUTO_INCREMENT,
  `page_id` int(10) NOT NULL,
  `title` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `status` int(2) DEFAULT '1',
  `sort` int(3) DEFAULT NULL,
  `module` varchar(10) CHARACTER SET utf8 NOT NULL,
  `type` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `template_id` int(10) DEFAULT NULL,
  `editor_id` int(10) NOT NULL,
  `approver_id` int(10) NOT NULL,
  `file_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `file_title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `create_date` datetime DEFAULT '0000-00-00 00:00:00',
  `update_date` datetime DEFAULT '0000-00-00 00:00:00',
  `approve_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `queue_date` datetime DEFAULT '0000-00-00 00:00:00',
  `options` text CHARACTER SET utf8,
  `content` text CHARACTER SET utf8,
  `meta_title` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `meta_image` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `import_id` int(100) NOT NULL,
  `source_id` int(100) NOT NULL,
  `tracking_omniture` text COLLATE utf8_bin NOT NULL,
  `tracking_other` text COLLATE utf8_bin NOT NULL,
  `attribute_values` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`version_id`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


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
-- Table structure for table `publish_queue`
--

CREATE TABLE IF NOT EXISTS `publish_queue` (
  `queue_id` int(255) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL,
  `module_id` int(10) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `queue_date` datetime NOT NULL,
  `queue_type` varchar(12) NOT NULL DEFAULT 'publish',
  `object` text NOT NULL,
  `approver_id` int(10) NOT NULL,
  PRIMARY KEY (`queue_id`),
  KEY `page_id+date_queue` (`queue_date`,`module_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------
--
-- Table structure for table `template`
--

CREATE TABLE `template` (
  `template_id` int(10) NOT NULL AUTO_INCREMENT,
  `template_title` varchar(100) NOT NULL,
  `template_file_name` varchar(100) NOT NULL,
  `template_path` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `template_type` varchar(100) NOT NULL,
  `template_parent_id` int(100) NOT NULL DEFAULT '0',
  `template_html_xsl_path` varchar(200) DEFAULT NULL,
  `template_xml_xsl_path` varchar(200) DEFAULT NULL,
  `template_xml` text NOT NULL,
  `template_options` text CHARACTER SET utf8 COLLATE utf8_bin,
  `template_attributes` text NOT NULL,
  `template_sort` int(100) DEFAULT NULL,
  `template_create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `template_update_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `template_cache_time` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`template_id`),
  KEY `ixParent` (`template_parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `template` (`template_id`, `template_title`, `template_file_name`, `template_path`, `template_type`, `template_parent_id`, `template_html_xsl_path`, `template_xml_xsl_path`, `template_xml`, `template_options`, `template_attributes`, `template_sort`, `template_create_date`, `template_update_date`, `template_cache_time`) VALUES
(1, 'starter page', 'starter-page', NULL, 'template', 0, '/cms/templates/starter_page.xml', '', '<data>\n\n<header_copy type="textfield" />\n<body_copy type="textarea" />\n\n  <download type="section">\n   <button_text type="textfield"/>\n   <download_file type="file"/>\n  </download>\n\n<sections type="section" border="none">\n\n<section type="section" multi="yes">\n  <title type="textfield" />\n  <text type="textarea" />\n  <button type="href" multi="yes" />\n</section>\n\n</sections>\n\n</data>', '{"html_action":"show","html_redirect":"","xml_action":"deny","cache_time":"none","event_show_time":"no","event_show_end":"no","child_edit_style":"normal","child_sort_method":"manually","child_template":{"1":""},"child_type":"","page_id_path":"no","show_import_id":"no"}', '[]', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------
--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(100) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(200) NOT NULL,
  `type` varchar(15) NOT NULL DEFAULT 'user',
  `password` varchar(100) NOT NULL,
  `password_options_json` text,
  `permission_group` varchar(100) NOT NULL DEFAULT 'content_editor',
  `permissions` text NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT '0',
  `options` text,
  `create_date` datetime DEFAULT '0000-00-00 00:00:00',
  `update_date` datetime DEFAULT '0000-00-00 00:00:00',
  `login_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`),
  KEY `login` (`user`,`password`,`enabled`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Table structure for table `user_login_fail`
--

CREATE TABLE IF NOT EXISTS `user_login_fail` (
  `user` varchar(100) DEFAULT NULL,
  `ip` varchar(100) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ip`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


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

