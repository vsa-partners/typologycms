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


	<xsl:template match="*[@type = 'checklist']">
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
				<xsl:call-template name="editForm_addChildren">
					<xsl:with-param name="parent_id" select="$item_id"/>
					<xsl:with-param name="parent_path" select="$item_path"/>
				</xsl:call-template>		
				<xsl:text> </xsl:text>
			</div>

			<xsl:call-template name="form_addNotes" />

		</div>

	</xsl:template>
	
	
	<xsl:template match="*[@type = 'checkbox']">
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

		<div class="checkbox_cell">
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->

			<div class="box">
				<xsl:choose>
					<xsl:when test="node() != ''">
						<input type="checkbox" title="{@title}" name="{$item_id}[value]" id="{$item_id}" value="1" checked="checked" />
					</xsl:when>
					<xsl:otherwise>
						<input type="checkbox" title="{@title}" name="{$item_id}[value]" id="{$item_id}" value="1" />
					</xsl:otherwise>
				</xsl:choose>
			</div>
			<xsl:if test="@title != ''">
				<label class="form_label_checkbox"><xsl:value-of select="@title"/></label>
			</xsl:if>

			<xsl:call-template name="form_addNotes" />
			<xsl:call-template name="form_addHiddentType">
				<xsl:with-param name="item_id" select="$item_id" />
			</xsl:call-template>

		</div>

	</xsl:template>
	
	
</xsl:stylesheet>