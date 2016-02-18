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
<!--
	<xsl:variable name="TITLE" select="/data/page/title"/>
	<xsl:variable name="PAGE_ID" select="/data/page_id"/>
-->
<!--
	<xsl:variable name="CONTENT">
		<xsl:choose>
			<xsl:when test="/data/page/content/data != ''"><xsl:copy-of select="/data/page/content/data"/></xsl:when>
			<xsl:when test="/data/content/data != ''"><xsl:copy-of select="/data/content/data"/></xsl:when>
			<xsl:otherwise></xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
-->

	<xsl:variable name="SITEPATH">
		<xsl:call-template name="getSitePath">
			<xsl:with-param name="params" select="'noslash'"/>
		</xsl:call-template>
	</xsl:variable>

	<xsl:variable name="SITE_CONF">
		<xsl:call-template name="callPhpFunction">
			<xsl:with-param name="function" select="'getConfig'" />
			<xsl:with-param name="params">
				<PARAMS>
					<config_name>site</config_name>
				</PARAMS>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:variable>


</xsl:stylesheet>