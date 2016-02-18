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


	<xsl:template match="*[@type = 'file']">
		<xsl:param name="parent_id" />
		<xsl:param name="parent_path" />
		<xsl:param name="sortkey" select="@sortkey" />

		<xsl:variable name="POST">
			<xsl:call-template name="callPhpFunction">
				<xsl:with-param name="function" select="'getPostParams'" />
			</xsl:call-template>
		</xsl:variable>

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

		<xsl:variable name="file_details">
			<xsl:choose>
				<xsl:when test="@file_id != ''">
					<xsl:call-template name="loadFileItem">
						<xsl:with-param name="id" select="@file_id" />
					</xsl:call-template>
				</xsl:when>
				<xsl:when test="exslt:node-set($POST)/data/file_id != ''">
					<xsl:call-template name="loadFileItem">
						<xsl:with-param name="id" select="exslt:node-set($POST)/data/file_id" />
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise> </xsl:otherwise>			
			</xsl:choose>
		</xsl:variable>
		
<!--
<xsl:call-template name="displayNodeset">
	<xsl:with-param name="title" select="'file_details'" />
	<xsl:with-param name="nodes" select="$file_details"/>
</xsl:call-template>
-->

		<xsl:variable name="js_params">
			<xsl:text>{module: 'file</xsl:text>
			<xsl:text>', current: '</xsl:text>
			<xsl:value-of select="node()"/>
			<xsl:text>', destination: '</xsl:text>
			<xsl:value-of select="$item_id"/>
			<xsl:text>'</xsl:text>
			<xsl:if test="(@parent_id != '') or (exslt:node-set($file_details)/data/parent_id != '')">
				<xsl:text>, parent_id: '</xsl:text>
						<xsl:choose>
							<xsl:when test="exslt:node-set($file_details)/data/parent_id != ''"><xsl:value-of select="exslt:node-set($file_details)/data/parent_id"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="@parent_id"/></xsl:otherwise>
						</xsl:choose>
				<xsl:text>'</xsl:text>
			</xsl:if>
			<xsl:if test="@multi != ''">
				<xsl:text>, multi: 'yes'</xsl:text>
			</xsl:if>
			<xsl:text>}</xsl:text>
		</xsl:variable>
	
		<xsl:value-of select="$nl"/>

		<div class="form_row">
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->
			
			<xsl:call-template name="form_addTitle"/>

			<div class="field">

				<div class="field_buttons">

					<div class="file_field_buttons">
						<xsl:attribute name="style">
							<xsl:if test="not(exslt:node-set($file_details)/data/title != '')">display: none; </xsl:if>
							<xsl:text>padding: 5px 5px 0 0; float: left;</xsl:text>
						</xsl:attribute>	
						<a href="{exslt:node-set($CI_VARS)/data/admin_path}/file/edit/{exslt:node-set($file_details)/data/file_id}" style="margin-right: 3px;"><img src="{$asset_path}img/mini_icons/pencil.gif" width="10" height="10"/></a>
						<a href="#" class="button_clear" onClick="itemPicker.clear('{$item_id}', ['file_path', 'file_title']); return false;"><img src="{$asset_path}img/mini_icons/cross.gif" width="10" height="10"/></a>
					</div>

					<button type="button" class="button button_outline_small" onClick="itemPicker.open({$js_params}); return false;"><span>PICK</span></button>
				</div>




				<div class="field_text">
					<img src="{$asset_path}img/mini_icons/image.gif" width="10" height="10"/>
					<xsl:text> </xsl:text>

					<xsl:choose>
						<xsl:when test="(node() != '' ) and not(exslt:node-set($file_details)/data/file_id)"><span id="{$item_id}_display">( Invalid or deleted image )</span></xsl:when>
						<xsl:when test="exslt:node-set($file_details)/data/title != ''">
							<a href="#" onclick="TNDR.Modal.showImage('{exslt:node-set($file_details)/data/manage_path}'); return false;" id="{$item_id}_display">
								<xsl:value-of select="exslt:node-set($file_details)/data/title"/>
								<xsl:if test="(exslt:node-set($file_details)/data/manage_path != '') and (exslt:node-set($file_details)/data/is_image = 1)">
									<div class="preview_image" style="margin-top: 5px;"><img src="{exslt:node-set($file_details)/data/manage_path}?w=80"/></div>
								</xsl:if>
							</a>
						</xsl:when>
						<xsl:otherwise><span id="{$item_id}_display">( No file selected )</span></xsl:otherwise>
					</xsl:choose>


				</div>
				
				<!--
				<input type="text" readonly="readonly" name="{$item_id}[height]" id="{$item_id}[height]" value="{@height}" />
				<input type="text" readonly="readonly" name="{$item_id}[width]" id="{$item_id}[width]" value="{@width}" />
				-->
				
				<input type="hidden" readonly="readonly" name="{$item_id}[file_path]" id="{$item_id}[file_path]" value="{exslt:node-set($file_details)/data/view_path}" />
				<input type="hidden" readonly="readonly" name="{$item_id}[file_title]" id="{$item_id}[file_title]" value="{exslt:node-set($file_details)/data/title}" />
				<input type="hidden" readonly="readonly" name="{$item_id}[file_id]" id="{$item_id}">
					<xsl:attribute name="value">
						<xsl:choose>
							<xsl:when test="not(exslt:node-set($file_details)/data/file_id)"><xsl:value-of select="@unknown"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="exslt:node-set($file_details)/data/file_id" /></xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
				</input>
				<input type="hidden" readonly="readonly" name="{$item_id}[value]" value="" />
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