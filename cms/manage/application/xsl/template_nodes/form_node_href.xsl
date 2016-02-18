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


	<xsl:template match="*[@type = 'href']">
		<xsl:param name="parent_id" />
		<xsl:param name="parent_path" />
		<xsl:param name="sortkey" select="@sortkey" />

		<xsl:variable name="targets">
			<target value="_self">Same Window</target>
			<target value="_blank">New Window</target>
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

		<xsl:value-of select="$nl"/>

		<div class="form_row">
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->

			<label>
				<xsl:value-of select="@title"/>
				<span class="sublabel">Title</span>
			</label>

			<xsl:call-template name="form_addMultiControl">
				<xsl:with-param name="item_id" select="$item_id" />
				<xsl:with-param name="item_path" select="$item_path" />
				<xsl:with-param name="parent_path" select="$parent_path" />
				<xsl:with-param name="parent_id" select="$parent_id" />
			</xsl:call-template>

			<div class="field">
				<input type="text" class="transform_xmlattrib">
					<xsl:attribute name="title"><xsl:value-of select="@title" /></xsl:attribute>
					<xsl:attribute name="name"><xsl:value-of select="$item_id" />[link_title]</xsl:attribute>
					<xsl:attribute name="id"><xsl:value-of select="$item_id" /></xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="@link_title" /></xsl:attribute>
				</input>	
			</div>
			<div class="note">Title can NOT contain &amp; characters.</div>

			<div class="sub_row">
				<label><span class="sublabel">Url</span></label>
				<div class="sub_field">
					<input type="text">
						<xsl:attribute name="name"><xsl:value-of select="$item_id" />[value]</xsl:attribute>
						<xsl:attribute name="id"><xsl:value-of select="$item_id" /></xsl:attribute>
						<xsl:attribute name="value">
							<xsl:choose>
								<xsl:when test="node() != ''"><xsl:value-of select="node()"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="@unknown" /></xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
					</input>
				</div>
			</div>
			
			<div class="sub_row">
				<label><span class="sublabel">Target</span></label>
				<div class="sub_field">
					<select>
						<xsl:attribute name="name"><xsl:value-of select="$item_id" />[target]</xsl:attribute>
						<xsl:attribute name="id"><xsl:value-of select="$item_id" /></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="@target" /></xsl:attribute>
						
						<xsl:variable name="target" select="@target" />
						
						<xsl:for-each select="exslt:node-set($targets)/target">
							<option value="{@value}">
								<xsl:if test="$target = @value">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
							
								<xsl:value-of select="node()"/>
							</option>
						
						</xsl:for-each>
						
					</select>
				</div>
			</div>	
			

			<xsl:call-template name="form_addNotes" />
			<xsl:call-template name="form_addHiddentType">
				<xsl:with-param name="item_id" select="$item_id" />
			</xsl:call-template>
		</div>

	</xsl:template>
	
</xsl:stylesheet>