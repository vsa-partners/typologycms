<?xml version="1.0" encoding="utf-8" ?>
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

	<xsl:template match="*[@type = 'multilang_textarea']">
		<xsl:param name="parent_id" />
		<xsl:param name="parent_path" />
		<xsl:param name="sortkey" select="@sortkey" />

		<xsl:variable name="item_id">
			<xsl:call-template name="editForm_getItemID">
				<xsl:with-param name="parent_id" select="$parent_id"/>
				<xsl:with-param name="sortkey" select="$sortkey"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="item_path">
			<xsl:call-template name="editForm_getItemPath">
				<xsl:with-param name="parent_path" select="$parent_path"/>
			</xsl:call-template>
		</xsl:variable>

		<xsl:variable name="item_value">
			<xsl:for-each select="*">
				<xsl:element name="node">
					<xsl:attribute name="lang"><xsl:value-of select="name()"/></xsl:attribute>
					<xsl:value-of select="node()"/>
				</xsl:element>
			</xsl:for-each>
		</xsl:variable>

		<xsl:value-of select="$nl"/>

		<div class="form_row">
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->

			<xsl:call-template name="form_addTitle"/>
			<div class="field lang_cols">
			
				<xsl:variable name="_editor" select="@editor" />

				<xsl:for-each select="exslt:node-set($ADMIN_CONF)/data/lang/node">

					<xsl:variable name="lang_code" select="concat('lang_', node())" />
					<xsl:variable name="lang_value" select="exslt:node-set($item_value)/node[@lang = $lang_code]" />
				
					<div class="lang_col">
						<div class="lang_title"><xsl:value-of select="node()" /></div>

						<textarea  title="{@title}" name="{$item_id}[value][{node()}]" id="{$item_id}">
		
							<!--
							<xsl:if test="(@editor = 'true') or (@editor = 'TRUE')">
								<xsl:attribute name="class">editor</xsl:attribute>				
							</xsl:if>
							-->
							
							<xsl:if test="($_editor = 'true') or ($_editor = 'TRUE')">
								<xsl:attribute name="class">editor</xsl:attribute>				
							</xsl:if>
							
							<xsl:if test="(@editor = 'full')">
								<xsl:attribute name="class">editor editor_full</xsl:attribute>				
							</xsl:if>
							<xsl:if test="(@editor = 'basic')">
								<xsl:attribute name="class">editor editor_basic</xsl:attribute>				
							</xsl:if>
		
							<xsl:choose>
								<xsl:when test="$lang_value != ''"><xsl:value-of select="$lang_value" disable-output-escaping="yes" /></xsl:when>
								<xsl:otherwise><xsl:text> </xsl:text></xsl:otherwise>
							</xsl:choose>
						</textarea>
					
					</div>

				</xsl:for-each>
				
				<!--
				<xsl:if test="(@editor = 'true') or (@editor = 'TRUE')">
					<br/>(<a onClick="refreshCLE(); return false;" href="#">Restore</a>)
				</xsl:if>
				-->

			</div>

			<xsl:call-template name="form_addNotes" />
			<xsl:call-template name="form_addHiddentType">
				<xsl:with-param name="item_id" select="$item_id" />
			</xsl:call-template>

		</div>

	</xsl:template>
	
	
</xsl:stylesheet>