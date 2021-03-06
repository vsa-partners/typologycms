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

	<xsl:template match="*[@type = 'textarea']">
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

		<xsl:value-of select="$nl"/>

		<div class="form_row">
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->

			<xsl:call-template name="form_addTitle"/>
			<div class="field">
				
				<textarea  title="{@title}" name="{$item_id}[value]" id="{$item_id}">
					<xsl:attribute name="class">
						<xsl:if test="(@editor = 'true') or (@editor = 'TRUE')">editor </xsl:if>
						<xsl:if test="(@editor = 'full')">editor editor_full </xsl:if>
						<xsl:if test="(@editor = 'basic')">editor editor_basic </xsl:if>
						<xsl:if test="@size != ''"><xsl:value-of select="@size"/> </xsl:if>
					</xsl:attribute>

					<xsl:choose>
						<xsl:when test="node() != ''"><xsl:value-of select="node()" disable-output-escaping="yes" /></xsl:when>
						<xsl:when test="@value != ''"><xsl:value-of select="@value"/></xsl:when>
						<xsl:otherwise><xsl:text> </xsl:text></xsl:otherwise>
					</xsl:choose>
				</textarea>

				<xsl:if test="(@editor = 'true') or (@editor = 'TRUE')">
					<br/>
					(<a onClick="refreshCLE(); return false;" href="#">Restore</a>)
				</xsl:if>


			</div>
			<xsl:call-template name="form_addMultiControl">
				<xsl:with-param name="item_path" select="$item_path" />
				<xsl:with-param name="parent_path" select="$parent_path" />
				<xsl:with-param name="parent_id" select="$parent_id" />
			</xsl:call-template>

			<xsl:call-template name="form_addNotes" />
			<xsl:call-template name="form_addHiddentType">
				<xsl:with-param name="item_id" select="$item_id" />
			</xsl:call-template>

		</div>

	</xsl:template>
	
	
</xsl:stylesheet>