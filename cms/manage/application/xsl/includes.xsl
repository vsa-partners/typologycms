<?xml version="1.0"?>
<!--
    Typology CMS

    @author	VSA Partners / Louis D Walch (lwalch@vsapartners.com)
    @link	http://www.vsapartners.com
-->
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exslt="http://exslt.org/common"
	xmlns:php="http://php.net/xsl"
	exclude-result-prefixes="xsl php exslt">

	<xsl:include href="application_helpers.xsl" />
	<xsl:include href="utilities.xsl" />

	<!-- Variables used in page templates. Maybe add some error checking in here -->

	<xsl:variable name="PAGE" select="/data/page" />
	<xsl:variable name="CONTENT" select="exslt:node-set($PAGE)/edit_xml/data"/>
	<xsl:variable name="TEMPLATE" select="/data/template" />


	<xsl:variable name="ADMIN_CONF">
		<xsl:call-template name="callPhpFunction">
			<xsl:with-param name="function" select="'getConfig'" />
			<xsl:with-param name="params">
				<PARAMS>
					<config_name>manage</config_name>
				</PARAMS>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:variable>
	
	<!--
	<xsl:variable name="MODULE_CONF">
		<xsl:call-template name="callPhpFunction">
			<xsl:with-param name="function" select="'getModuleConfig'" />
		</xsl:call-template>
	</xsl:variable>
	-->

	<xsl:variable name="CI_VARS">
		<xsl:call-template name="callPhpFunction">
			<xsl:with-param name="function" select="'getApplicationVars'" />
		</xsl:call-template>
	</xsl:variable>

	<xsl:variable name="asset_path" select="exslt:node-set($CI_VARS)/data/asset_path" />

</xsl:stylesheet>