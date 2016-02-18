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

	<xsl:template name="debugXML">
			<DEBUG><xsl:copy-of select="."/></DEBUG>
	</xsl:template>


	<xsl:template name="loadPageXML">
			<xsl:param name="id" />
			<xsl:param name="first" />
			<xsl:param name="content" />
			<xsl:param name="sort" />
			
			<xsl:if test="$id != ''">
				<xsl:call-template name="callPhpFunction">
					<xsl:with-param name="function" select="'loadPageXML'" />
					<xsl:with-param name="params">
						<PARAMS>
							<id><xsl:value-of select="$id"/></id>
							<format>xml</format>
							<xsl:if test="$first != ''">
								<first><xsl:value-of select="$first"/></first>
							</xsl:if>
							<xsl:if test="$sort != ''">
								<sort><xsl:value-of select="$sort"/></sort>
							</xsl:if>
							<xsl:if test="$content != ''">
								<content><xsl:value-of select="$content"/></content>
							</xsl:if>
						</PARAMS>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:if>
	</xsl:template>

	<xsl:template name="loadFileItem">
			<xsl:param name="id" />
			
			<xsl:if test="$id != ''">
				<xsl:call-template name="callPhpFunction">
					<xsl:with-param name="function" select="'loadFileItem'" />
					<xsl:with-param name="params">
						<PARAMS>
							<id><xsl:value-of select="$id"/></id>
						</PARAMS>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:if>
	</xsl:template>

	<!--
	
	Currently not used
	
	<xsl:template name="displaySectionPages">
			<xsl:param name="page" select="/data/page" />
			
	
			<xsl:if test="(exslt:node-set($page)/type = 'section')
							and not(exslt:node-set($page)/template_options/html_action = 'nothing')">
	
				<xsl:variable name="page_id" select="exslt:node-set($page)/page_id"/>
				<xsl:variable name="section_action" select="exslt:node-set($page)/template_options/html_action"/>
				<xsl:variable name="section_action_page" select="exslt:node-set($page)/options/section_pages/default_page"/>
				<xsl:variable name="section_xsl" select="exslt:node-set($page)/options/section_pages/alt_xsl"/>
	
				<xsl:variable name="return">
					<xsl:call-template name="callPhpFunction">
						<xsl:with-param name="function" select="'loadSectionPages'" />
						<xsl:with-param name="params">
							<PARAMS>
								<page_id><xsl:value-of select="$page_id"/></page_id>
								<section_action><xsl:value-of select="$section_action"/></section_action>
								<section_xsl><xsl:value-of select="$section_xsl"/></section_xsl>
								<section_action_page><xsl:value-of select="$section_action_page"/></section_action_page>
							</PARAMS>
						</xsl:with-param>
					</xsl:call-template>
				</xsl:variable>
				
				<xsl:value-of select="$return" disable-output-escaping="yes"/>
		
			</xsl:if>
		
	</xsl:template>
	-->


	<xsl:template name="getSitePath">
			<xsl:param name="params" />
			
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'getSitePath'" />
				<xsl:with-param name="params" select="$params" />
			</xsl:call-template>
		
	</xsl:template>


	<xsl:template name="callPhpFunction">
			<xsl:param name="function" />
			<xsl:param name="params" />
			
			<xsl:choose>
				<xsl:when test="$function = ''">	
					<ERROR>callPhpFunction:: Missing 'function' param</ERROR>
				</xsl:when>
				<xsl:otherwise>
					<xsl:variable name="new_params">
						<xsl:choose>
							<xsl:when test="count(exslt:node-set($params)/PARAMS/*)">
								<xsl:text>{</xsl:text>
								<xsl:for-each select="exslt:node-set($params)/PARAMS/*">
									<xsl:if test="position() != 1">,</xsl:if>
									<xsl:text>"</xsl:text>
									<xsl:value-of select="name()"/>
									<xsl:text>":"</xsl:text>
									<xsl:call-template name="escape-quotes">
										<xsl:with-param name="text" select="node()" />
									</xsl:call-template>
									<xsl:text>"</xsl:text>
								</xsl:for-each>
								<xsl:text>}</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="$params"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:variable>
					<xsl:copy-of select="php:function(concat('XSL_Functions::',$function),string($new_params))" />
				</xsl:otherwise>
			</xsl:choose>

	</xsl:template>


	
</xsl:stylesheet>