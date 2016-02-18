<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exslt="http://exslt.org/common"
	xmlns:php="http://php.net/xsl"
	exclude-result-prefixes="xsl php exslt">

	<xsl:output method="html" media-type="text/html"
		indent="yes"
		omit-xml-declaration="yes"
		encoding="UTF-8" />

	<!-- -->
	<xsl:variable name="PAGE" select="/data"/>
	<xsl:variable name="PAGE_ID" select="/data/page_id"/>
	<xsl:variable name="CONTENT" select="/data/content/data"/>
	<xsl:include href="../website/application/xsl/includes.xsl"/>
	<xsl:include href="_includes.xsl"/>
	<!-- -->

	<xsl:param name="ISAJAX" />

	<!-- -->

	<xsl:template match="/">
        
        <!--
		<xsl:call-template name="site_header" />				
		<xsl:call-template name="site_footer" />
		-->

		<xsl:call-template name="displayNodeset">
			<xsl:with-param name="title" select="'Page Nodes'" />
			<xsl:with-param name="nodes" select="$PAGE" />
		</xsl:call-template>
		

	</xsl:template>
	
</xsl:stylesheet>