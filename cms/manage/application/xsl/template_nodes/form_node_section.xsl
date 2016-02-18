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


	<xsl:template match="*[@type = 'section']">
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

		<div>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@toggle = 'yes'">form_section form_section_toggle form_section_closed</xsl:when>
					<xsl:when test="@border = 'none'">form_section form_section_borderless</xsl:when>
					<xsl:otherwise>form_section</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>			
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->

			<xsl:if test="not(@border = 'none')">
                <div class="form_section_header">
                    <xsl:choose>
                        <xsl:when test="@toggle = 'yes'"><a href="#" onclick="TNDR.Form.Actions.toggleSection(this); return false;" class="arrow"><xsl:value-of select="@title"/></a></xsl:when>
                        <xsl:otherwise><xsl:value-of select="@title"/></xsl:otherwise>
                    </xsl:choose>
                </div>
            </xsl:if>


			<xsl:call-template name="form_addMultiControl">
				<xsl:with-param name="item_id" select="$item_id" />
				<xsl:with-param name="item_path" select="$item_path" />
				<xsl:with-param name="parent_path" select="$parent_path" />
				<xsl:with-param name="parent_id" select="$parent_id" />
				<xsl:with-param name="type" select="'section'" />
			</xsl:call-template>

			<div class="section_content">
				<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_content')" /></xsl:attribute> <!-- Add node id, used for sorting -->
			
				<xsl:call-template name="editForm_addChildren">
					<xsl:with-param name="parent_id" select="$item_id"/>
					<xsl:with-param name="parent_path" select="$item_path"/>
				</xsl:call-template>		
			
			</div>
		</div>
	</xsl:template>
	
	
</xsl:stylesheet>