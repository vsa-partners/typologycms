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


	<xsl:template match="*[@type = 'pageselect']">
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

		<xsl:variable name="value">
			<xsl:choose>
				<xsl:when test="node() != ''"><xsl:value-of select="node()"/></xsl:when>
				<xsl:when test="@value != ''"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:variable name="item_details">
			<xsl:choose>
				<xsl:when test="@parent_id != ''">
					<xsl:variable name="_parent_id">
						<xsl:choose>
							<xsl:when test="@parent_id = 'self'"><xsl:value-of select="$PAGE/page_id"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="@parent_id"/></xsl:otherwise>
						</xsl:choose>
					</xsl:variable>
					<xsl:call-template name="callPhpFunction">
						<xsl:with-param name="function" select="'loadPageXML'" />
						<xsl:with-param name="params">
							<PARAMS>
								<cache>TRUE</cache>
								<sort>title</sort>
								<format>xml</format>
								<parent_id><xsl:value-of select="$_parent_id"/></parent_id>
								<auto_join>FALSE</auto_join>
							</PARAMS>
						</xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:when test="@template_id != ''">
					<xsl:call-template name="callPhpFunction">
						<xsl:with-param name="function" select="'loadPageXML'" />
						<xsl:with-param name="params">
							<PARAMS>
								<cache>TRUE</cache>
								<sort>title</sort>
								<format>xml</format>
								<template_id><xsl:value-of select="@template_id"/></template_id>
								<auto_join>FALSE</auto_join>
							</PARAMS>
						</xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise> </xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:value-of select="$nl"/>

		<div class="form_row">
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->

			<xsl:call-template name="form_addTitle"/>
			<div class="field">
				<xsl:choose>
					<xsl:when test="count(exslt:node-set($item_details)/data/node)">
						
						<select title="{@title}" name="{$item_id}[value]" id="{$item_id}">
						
						<option value=""> -- Select -- </option>
						
						<xsl:for-each select="exslt:node-set($item_details)/data/node">
							<option value="{page_id}">
							<xsl:if test="page_id = $value">
								<xsl:attribute name="selected">true</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="title"/>
							</option>						
						</xsl:for-each>
						</select>
					
					</xsl:when>
					<xsl:otherwise>Error loading items</xsl:otherwise>
				</xsl:choose>

<!--
<xsl:call-template name="displayNodeset">
	<xsl:with-param name="title" select="'$item_details'" />
	<xsl:with-param name="nodes" select="$item_details" />
</xsl:call-template>
-->

			</div>
			
			<!--
			<div class="debug">(<xsl:value-of select="$item_id"/>)</div>
			-->

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