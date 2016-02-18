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


	<!-- ADD CHILDREN -->
	<xsl:template name="editForm_addChildren">
			<xsl:param name="parent_id" />
			<xsl:param name="parent_path" />
	
			<xsl:apply-templates select="*">
				<xsl:with-param name="parent_id" select="$parent_id"/>
				<xsl:with-param name="parent_path" select="$parent_path"/>
			</xsl:apply-templates>

	</xsl:template>



	<!-- GET ITEM ID -->
	<xsl:template name="editForm_getItemID">
			<xsl:param name="parent_id" />
			<xsl:param name="sortkey" select="'0'" />
	
			<xsl:value-of select="$parent_id"/>
			<xsl:value-of select="concat('[',name(),']')"/>
	
			<xsl:choose>
				<xsl:when test="$sortkey != ''"><xsl:value-of select="concat('[',$sortkey,']')"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="concat('[','0',']')"/></xsl:otherwise>
			</xsl:choose>

	</xsl:template>



	<!-- GET ITEM PATH -->
	<xsl:template name="editForm_getItemPath">
		<xsl:param name="parent_path" />

		<xsl:value-of select="$parent_path"/>
		<xsl:value-of select="concat('/',name())"/>

	</xsl:template>



	<!-- NODE TYPE -->
	<xsl:template name="form_addHiddentType">
			<xsl:param name="item_id" />
			
			<xsl:choose>
				<xsl:when test="$item_id = ''">
					ERROR: form_addHiddentType:: Missing or invalid item_id
				</xsl:when>
				<xsl:when test="./@type = ''">
					ERROR: form_addHiddentType:: Missing or invalid type
				</xsl:when>
				<xsl:otherwise>
					<input type="hidden" name="{$item_id}[type]" value="{./@type}" READONLY="TRUE"/>
				</xsl:otherwise>
			</xsl:choose>
	
	</xsl:template>



	<!-- NODE MAX CONTROLS -->
	<xsl:template name="form_addMultiControl">
			<xsl:param name="item_id" />
			<xsl:param name="item_path" />
			<xsl:param name="parent_id" />
			<xsl:param name="parent_path" />
			<xsl:param name="type" select="row" />
	
			<!--
			<xsl:value-of select="@title"/> <xsl:value-of select="@type"/> 
			-->
			
			<xsl:if test="@multi != ''">
	
				<xsl:variable name="js_params">
					<xsl:text>{</xsl:text>
					<xsl:text>item_id : '</xsl:text><xsl:value-of select="$item_id"/><xsl:text>'</xsl:text>
					<xsl:text>, item_path : '</xsl:text><xsl:value-of select="$item_path"/><xsl:text>'</xsl:text>
					<xsl:text>, parent_id : '</xsl:text><xsl:value-of select="$parent_id"/><xsl:text>'</xsl:text>
					<xsl:text>, parent_path : '</xsl:text><xsl:value-of select="$parent_path"/><xsl:text>'</xsl:text>
					<xsl:text>}</xsl:text>
				</xsl:variable>
	
				<div>
					<xsl:attribute name="class">
						<xsl:choose>
							<xsl:when test="$type = 'section'">form_section_buttons</xsl:when>
							<xsl:otherwise>row_buttons</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
					
					<div class="button_bar">
						<div class="buttons_inner">
							<span class="btn_dupe" multi_param="{$js_params}"><a class="button_add" href="#dupe" onClick="TNDR.Form.Actions.dupe({$js_params}); return false;">Add</a></span>
							<a class="button_remove" href="#remove" onClick="TNDR.Form.Actions.remove('{$item_id}'); return false;">Delete</a>
							<a class="button_sort" href="#sort" onClick="TNDR.Form.Actions.sort({$js_params}); return false;">Sort</a>
						</div>
					</div>
					
				</div>
			</xsl:if>
		
	</xsl:template>



	<!-- NODE NOTES -->
	<xsl:template name="form_addNotes">

			<xsl:if test="@notes != ''">
				<div class="note"><xsl:value-of select="@notes"/></div>
			</xsl:if>
			<xsl:if test="@note != ''">
				<div class="note"><xsl:value-of select="@note"/></div>
			</xsl:if>
		
	</xsl:template>


	<!-- NODE TITLE -->
	<xsl:template name="form_addTitle">

			<xsl:if test="@title != ''">
				<label><xsl:value-of select="@title"/></label>
			</xsl:if>
		
	</xsl:template>
	


	<!-- NOTE -->
	<xsl:template match="note">

			<div class="form_row form_note">
				<xsl:value-of select="node()"/>
			</div>
		
	</xsl:template>



	<!-- LINE -->
	<xsl:template match="line">

			<div class="form_line"><hr/></div>
		
	</xsl:template>



	<!-- SPACE -->
	<xsl:template match="space">

			<xsl:variable name="height">
				<xsl:choose>
					<xsl:when test="@height != ''"><xsl:value-of select="@height"/></xsl:when>
					<xsl:otherwise>15</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			
			<div class="form_row no_border" style="padding: 0; height: {$height}px;"><xsl:text> </xsl:text></div>
		
	</xsl:template>

	
</xsl:stylesheet>