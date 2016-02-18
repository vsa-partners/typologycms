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


	<xsl:template match="*[@type = 'pagelink']">
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
			<xsl:if test="@page_id != ''">
				<xsl:call-template name="loadPageXML">
					<xsl:with-param name="id" select="@page_id" />
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
			
				<div class="field_buttons">

					<div>
						<xsl:attribute name="style">
							<xsl:if test="not(exslt:node-set($item_details)/data/node/title != '')">display: none; </xsl:if>
							<xsl:text>padding: 5px 5px 0 0; float: left;</xsl:text>
						</xsl:attribute>
						<a href="#" onClick="itemPicker.clear('{$item_id}'); return false;"><img src="{$asset_path}img/mini_icons/cross.gif" width="10" height="10"/></a>
					</div>

					<button type="button" class="button button_outline_small" onClick="itemPicker.open({$js_params}); return false;"><span>PICK</span></button>

				</div>

				<span class="field_text">
					<img src="{$asset_path}img/mini_icons/document.gif" width="10" height="10"/>
					<xsl:text> </xsl:text>
					
					<span id="{$item_id}_display">					
						<xsl:choose>
							<xsl:when test="exslt:node-set($item_details)/data/node/title != ''">
								<a href="{exslt:node-set($item_details)/data/node/page_id}"><xsl:value-of select="exslt:node-set($item_details)/data/node/title" /></a>
								<div style="font-size: 9px; margin: 4px 0 0 11px; width: 250px; line-height: 10px;"><xsl:value-of select="exslt:node-set($item_details)/data/node/path"/><xsl:text> </xsl:text></div>
							</xsl:when>
							<xsl:otherwise><xsl:text>( No Item Selected )</xsl:text></xsl:otherwise>
						</xsl:choose>
					</span>
				</span>
			
				<input type="hidden" title="{@title}" name="{$item_id}[value]" id="{$item_id}[value]" value="{exslt:node-set($item_details)/data/node/title}" />
				<input type="hidden" readonly="readonly" name="{$item_id}[page_id]" id="{$item_id}" value="{@page_id}" />
				<input type="hidden" readonly="readonly" name="{$item_id}[page_path]" id="{$item_id}[page_path]" value="{exslt:node-set($item_details)/data/node/path}" />
			
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