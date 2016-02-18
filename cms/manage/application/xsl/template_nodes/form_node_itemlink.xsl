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


	<xsl:template match="*[@type = 'itemlink']">
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
		
		<xsl:variable name="item_details">
			<xsl:if test="@value != ''">
				<xsl:call-template name="loadPageXML">
					<xsl:with-param name="id" select="@value" />
				</xsl:call-template>
			</xsl:if>
		</xsl:variable>
		
		<xsl:variable name="js_params">
			<xsl:text>{destination: '</xsl:text>
			<xsl:value-of select="$item_id"/>
			<xsl:text>', module: '</xsl:text>
			<xsl:choose>
				<xsl:when test="@link_module != ''"><xsl:value-of select="@link_module"/></xsl:when>
				<xsl:otherwise>page</xsl:otherwise>
			</xsl:choose>
			<xsl:text>', sType: '</xsl:text>
			<xsl:value-of select="@link_type"/>

			<!--
			<xsl:text>', submitForm: 'editForm'</xsl:text>
			-->

			<xsl:text>'</xsl:text>
			<xsl:text>}</xsl:text>
		</xsl:variable>

		<xsl:value-of select="$nl"/>

		<div class="form_row">
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->

			<xsl:call-template name="form_addTitle"/>

			<div class="field">
				
			
				<div class="button"><button type="button" class="button button_outline_small" onClick="itemPicker.open({$js_params}); return false;"><span>PICK</span></button></div>
				<div class="right button_clear">
					<xsl:attribute name="style">
						<xsl:if test="not(exslt:node-set($item_details)/data/node/title != '')">display: none; </xsl:if>
						<xsl:text>padding: 5px 5px 0 0;</xsl:text>
					</xsl:attribute>
					<a href="#" onClick="itemPicker.clear('{$item_id}'); return false;"><img src="{$asset_path}img/mini_icons/cross.gif" width="10" height="10"/></a>
				</div>

			
				<span class="field_text">
					<img src="{$asset_path}img/mini_icons/document.gif" width="10" height="10"/>
					<xsl:text> </xsl:text>
					
					<span id="{$item_id}_display">					
						<xsl:choose>
							<xsl:when test="exslt:node-set($item_details)/data/node/title != ''"><a href="{exslt:node-set($item_details)/data/node/ixPage}"><xsl:value-of select="exslt:node-set($item_details)/data/node/title" /></a></xsl:when>
							<xsl:otherwise><xsl:text>( No Item Selected )</xsl:text></xsl:otherwise>
						</xsl:choose>
					</span>
				</span>
			
				<input type="hidden" title="{@title}" name="{$item_id}[value]" id="{$item_id}">
					<xsl:attribute name="value">
						<xsl:choose>
							<xsl:when test="node() != ''"><xsl:value-of select="node()"/></xsl:when>
							<xsl:when test="@value != ''"><xsl:value-of select="@value"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="@unknown" /></xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
				</input>
			
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