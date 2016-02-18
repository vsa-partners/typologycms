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


	<xsl:template match="*[@type = 'swf']">
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


		<xsl:variable name="js_params">
			<xsl:text>{browser: '</xsl:text>
			<xsl:text>disk</xsl:text>
			<xsl:text>', destination: '</xsl:text>
			<xsl:value-of select="$item_id"/>
			<xsl:text>'</xsl:text>
			<xsl:if test="@path != ''">
				<xsl:text>, path: '</xsl:text>
				<xsl:value-of select="@path"/>
				<xsl:text>'</xsl:text>		
			</xsl:if>
			<xsl:text>}</xsl:text>
		</xsl:variable>




		<div class="form_row">
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->
			
			<div class="row">
				<xsl:call-template name="form_addTitle"/>
				<div class="field">
					<div class="button"><button type="button" class="button button_outline_small" onClick="fileBrowser.open({$js_params}); return false;"><span>PICK</span></button></div>

					<input type="text" title="{@title}" name="{$item_id}[value]" id="{$item_id}">
						<xsl:attribute name="value">
							<xsl:choose>
								<xsl:when test="node() != ''"><xsl:value-of select="node()"/></xsl:when>
								<xsl:when test="@value != ''"><xsl:value-of select="@value"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="@unknown" /></xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
					</input>
				</div>
			</div>
			
			<div class="row">
				<div class="subtitle">Width:</div>
				<div class="field">
					<input type="text" name="{$item_id}[width]" id="{$item_id}[width]" value="{@width}" />
				</div>
			</div>
			
			<div class="row">
				<div class="subtitle">Height:</div>
				<div class="field">
					<input type="text" name="{$item_id}[height]" id="{$item_id}[height]" value="{@height}" />
				</div>
			</div>

			<xsl:call-template name="form_addMultiControl">
				<xsl:with-param name="item_id" select="$item_id" />
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