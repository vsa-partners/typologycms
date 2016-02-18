<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exslt="http://exslt.org/common"
	xmlns:php="http://php.net/xsl"
	xmlns:addthis="http://www.addthis.com/help/api-spec"
	exclude-result-prefixes="xsl php exslt addthis">

    

	<xsl:template name="site_header">
		<xsl:param name="class" />
		<xsl:param name="script" />
		<xsl:param name="page_url" select="'/site/header'" />
		
		<xsl:if test="$class != ''">
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'setBodyClass'" />
				<xsl:with-param name="params">
					<PARAMS>
						<class><xsl:value-of select="$class" /></class>
					</PARAMS>
				</xsl:with-param>
			</xsl:call-template>
		</xsl:if>

		<xsl:if test="$script != ''">
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'setBodyScript'" />
				<xsl:with-param name="params">
					<PARAMS>
						<script><xsl:value-of select="$script" /></script>
					</PARAMS>
				</xsl:with-param>
			</xsl:call-template>
		</xsl:if>
		
		<xsl:call-template name="loadPageHTML">
			<xsl:with-param name="path" select="$page_url" />
		</xsl:call-template>
		
	</xsl:template>

	
	
	<xsl:template name="site_footer">
		<xsl:param name="page_url" select="'/site/footer'" />

		<xsl:call-template name="loadPageHTML">
			<xsl:with-param name="path" select="$page_url" />
		</xsl:call-template>
		
		<!--		
		<xsl:call-template name="displayNodeset">
			<xsl:with-param name="title" select="'Page Nodes'" />
			<xsl:with-param name="nodes" select="$PAGE" />
		</xsl:call-template>
		-->

	</xsl:template>
	
	
</xsl:stylesheet>