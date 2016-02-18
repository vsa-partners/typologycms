<a href="http://www.vsapartners.com/">
    <img src="https://avatars1.githubusercontent.com/u/607395?v=3&s=100" alt="VSA Partners"
         title="VSA Partners" align="right" />
</a>
# Typology CMS
A cross channel content management system
***
**Typology CMS** is a PHP based system where content is fully abstracted from layout, making it easy to manage content across multiple channels. Content is stored in structured XML, and creating relationships between content is simple.

The content is accessible as XML to drive any variety of digital applications. Or the content can be transformed via the built in XSLT templating system to deliver content in HTML, different XML schemas, or other data formats.

The **Typology CMS** is ideal for marketing and advertising agencies looking to manage cross-channel campaigns and messaging. It has evolved over almost a decade of creating websites, applications, and digital installations, each iteration of the site yielding valuable features and functionality.

 

##Key Features

**Publishing Flow** - Administrator, Publisher, Author and Editor user levels for a high degree of control over editing and publishing privledges.

**Staging and Production Enivronments** - The system automatically supports a staging and production environment allowing users to create content in draft mode and fully preview it across the site without affecting the live environment. 

**Multi-server Support and Remote Publishing** - The CMS can publish content to multiple remote servers at once, ideal for developers creating systems that require high availability or when the editing environment is behind a firewall.

**Structured Data Types** - Several data types are built into the content templating system, such as date and booleans, making it easy to create content that can be filtered, linked, and organized. 

**Scheduled Publishing** - Scheduled publishing is built in, allowing content to be created, previewed, and queued up for publishing.


##System Requirements
* Apache 2
  *  AllowOverride All 
* Apache Modules:
  * mod_rewrite
* PHP (>= 5.2.6)
* PHP Modules:
  * curl
  * dom
  * gd
  * json
  * libxml
  * mcrypt
  * mysql
  * mbstring
  * SimpleXML
  * xmlrpc
  * xsl
* PHP Options:
  * short_open_tag = On
* MySQL (4 or 5)

##Installation

- Copy deploy directory to document root folder	- If this is a view-only production site, delete the cms/manage folder	- Don't forget the htaccess! (normally it is hidden. The hidden files can be shown by using this command in the mac terminal window `defaults write com.apple.finder AppleShowAllFiles YES`)- `chmod -R 777` the following directories:	- cms/local/files	- cms/system/logs	- cms/website/cache	- cms/manage/cache- Update cms/local/database.php with db credentials
- Visit www.domain.com/install/index.php to set all initial configuration and create a default page.
- Please do remember to remove the install folder after installation.