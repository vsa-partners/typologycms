<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exslt="http://exslt.org/common"
	xmlns:php="http://php.net/xsl"
	exclude-result-prefixes="xsl php exslt">


	<xsl:template name="debugXML">
			<DEBUG><xsl:copy-of select="."/></DEBUG>
	</xsl:template>

	<xsl:template name="addCSS">
			<xsl:param name="file" />
			<xsl:if test="$file != ''">
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'addCSS'" />
				<xsl:with-param name="params" select="$file" />
			</xsl:call-template>
			</xsl:if>
	</xsl:template>

	<xsl:template name="addJS">
			<xsl:param name="file" />
			<xsl:if test="$file != ''">
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'addJS'" />
				<xsl:with-param name="params" select="$file" />
			</xsl:call-template>
			</xsl:if>
	</xsl:template>

	<xsl:template name="loadRelatedPages">
			<xsl:param name="template_id" />
			<xsl:param name="parent_id" />
			<xsl:param name="search" />
			<xsl:param name="term" />
			<xsl:param name="search2" />
			<xsl:param name="term2" />
			<xsl:param name="content" />
			
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'loadRelatedPages'" />
				<xsl:with-param name="params">
					<PARAMS>
						<xsl:if test="$content != ''">
							<content><xsl:value-of select="$content"/></content>
						</xsl:if>
						<xsl:if test="$template_id != ''">
							<template_id><xsl:value-of select="$template_id"/></template_id>
						</xsl:if>
						<xsl:if test="$parent_id != ''">
							<parent_id><xsl:value-of select="$parent_id"/></parent_id>
						</xsl:if>
						<xsl:if test="$search != ''">
							<search><xsl:value-of select="$search"/></search>
						</xsl:if>
						<xsl:if test="$term != ''">
							<term><xsl:value-of select="$term"/></term>
						</xsl:if>
						<xsl:if test="$search2 != ''">
							<search2><xsl:value-of select="$search2"/></search2>
						</xsl:if>
						<xsl:if test="$term2 != ''">
							<term2><xsl:value-of select="$term2"/></term2>
						</xsl:if>
					</PARAMS>
				</xsl:with-param>
			</xsl:call-template>
	</xsl:template>

	<xsl:template name="addJavaScriptVar">
			<xsl:param name="name" />
			<xsl:param name="content" />
			
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'addJavaScriptVar'" />
				<xsl:with-param name="params">
					<PARAMS>
						<name><xsl:value-of select="$name"/></name>
						<content><xsl:value-of select="$content"/></content>
					</PARAMS>
				</xsl:with-param>
			</xsl:call-template>
	</xsl:template>

	<xsl:template name="setTitle">
			<xsl:param name="title" />
			
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'setTitle'" />
				<xsl:with-param name="params" select="$title" />
			</xsl:call-template>

	</xsl:template>

	<xsl:template name="appendTitle">
			<xsl:param name="title" />
			
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'appendTitle'" />
				<xsl:with-param name="params" select="$title" />
			</xsl:call-template>

	</xsl:template>

	<xsl:template name="setBodyClass">
			<xsl:param name="class" />
			<xsl:param name="replace" select="'FALSE'" />
			
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'setBodyClass'" />
				<xsl:with-param name="params">
					<PARAMS>
						<class><xsl:value-of select="$class"/></class>
						<replace><xsl:value-of select="$replace"/></replace>
					</PARAMS>
				</xsl:with-param>
			</xsl:call-template>
	</xsl:template>

	<xsl:template name="loadPageHTML">
			<xsl:param name="path" />
			<xsl:param name="cache" />
			<xsl:param name="children" />
			<xsl:param name="id" />

			<xsl:if test="$path != '' or $id != ''">

				<xsl:variable name="loadPageHTML_html">
					<xsl:call-template name="callPhpFunction">
						<xsl:with-param name="function" select="'loadPageHTML'" />
						<xsl:with-param name="params">
							<PARAMS>
								<xsl:if test="$cache != ''">
									<cache><xsl:value-of select="$cache"/></cache>
								</xsl:if>
								<xsl:if test="$children != ''">
									<children><xsl:value-of select="$children"/></children>
								</xsl:if>
								<xsl:if test="$path != ''">
									<path><xsl:value-of select="$path"/></path>
								</xsl:if>
								<xsl:if test="$id != ''">
									<id><xsl:value-of select="$id"/></id>
								</xsl:if>
							</PARAMS>
						</xsl:with-param>
					</xsl:call-template>
				</xsl:variable>
				<xsl:value-of select="$loadPageHTML_html" disable-output-escaping="yes" />
			
			</xsl:if>
	
	</xsl:template>

	<xsl:template name="loadPageXML">
			<xsl:param name="id" />
			<xsl:param name="parent_id" />
			<xsl:param name="template_id" />
			<xsl:param name="first" />
			<xsl:param name="content" />
			<xsl:param name="path" />
			<xsl:param name="cache" />
			<xsl:param name="debug" />
			<xsl:param name="children" />
			
			<xsl:if test="($id != '') or ($parent_id != '') or ($path != '') or ($template_id != '')">
				<xsl:call-template name="callPhpFunction">
					<xsl:with-param name="function" select="'loadPageXML'" />
					<xsl:with-param name="params">
						<PARAMS>
							<xsl:if test="$debug != ''">
								<debug><xsl:value-of select="$debug"/></debug>
							</xsl:if>
							<xsl:if test="$parent_id != ''">
								<parent_id><xsl:value-of select="$parent_id"/></parent_id>
							</xsl:if>
							<xsl:if test="$id != ''">
								<id><xsl:value-of select="$id"/></id>
							</xsl:if>
							<xsl:if test="$cache != ''">
								<cache><xsl:value-of select="$cache"/></cache>
							</xsl:if>
							<xsl:if test="$template_id != ''">
								<template_id><xsl:value-of select="$template_id"/></template_id>
							</xsl:if>
							<format>xml</format>
							<xsl:if test="$path != ''">
								<path><xsl:value-of select="$path"/></path>
							</xsl:if>
							<xsl:if test="$first != ''">
								<first><xsl:value-of select="$first"/></first>
							</xsl:if>
							<xsl:if test="$content != ''">
								<content><xsl:value-of select="$content"/></content>
							</xsl:if>
							<xsl:if test="$children != ''">
								<children><xsl:value-of select="$children"/></children>
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
										<xsl:with-param name="text" select="string(node())" />
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

    <xsl:template name="getSectionAttributeOptions">
        <xsl:param name="template_id" />

        <xsl:call-template name="callPhpFunction">
            <xsl:with-param name="function" select="'getSectionAttributeOptions'" />
            <xsl:with-param name="params" select="$template_id" />
        </xsl:call-template>			
    
    </xsl:template>

    <xsl:template name="getPageByAttribute">
        <xsl:param name="key" select="'attribute'" />
        <xsl:param name="template_id" />
        <xsl:param name="parent_id" />
        <xsl:param name="content" select="'TRUE'"/>
        
        <xsl:variable name="_params">
            <xsl:call-template name="callPhpFunction">
                <xsl:with-param name="function" select="'getPostOrUrlParams'" />
            </xsl:call-template>
        </xsl:variable>

        <xsl:if test="($template_id != '') or ($parent_id != '')">

            <xsl:call-template name="callPhpFunction">
                <xsl:with-param name="function" select="'getPageByAttribute'" />
                <xsl:with-param name="params">
                    <PARAMS>
                        <xsl:if test="$content != ''">
                            <content><xsl:value-of select="$content"/></content>
                        </xsl:if>
                        <xsl:if test="$template_id != ''">
                            <template_id><xsl:value-of select="$template_id"/></template_id>
                        </xsl:if>
                        <xsl:if test="$parent_id != ''">
                            <parent_id><xsl:value-of select="$parent_id"/></parent_id>
                        </xsl:if>
                        <xsl:for-each select="exslt:node-set($_params)/data/*[name() = $key]/*">
                            <xsl:element name="{name()}">
                            	<xsl:choose>
                            		<xsl:when test="count(*)">
                            			<xsl:for-each select="*"><node><xsl:value-of select="normalize-space(.)" /></node></xsl:for-each>
                            		</xsl:when>
                            		<xsl:otherwise>
                            			<xsl:value-of select="normalize-space(.)" />
                            		</xsl:otherwise>
                            	</xsl:choose>
							</xsl:element>
                        </xsl:for-each>
                    </PARAMS>
                </xsl:with-param>
            </xsl:call-template>			
        
        </xsl:if>
    
    </xsl:template>	
</xsl:stylesheet>