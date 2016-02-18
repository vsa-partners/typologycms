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

	<xsl:output method="html" media-type="text/html"
		indent="yes"
		omit-xml-declaration="yes"
		encoding="UTF-8"/>

<!--		encoding="ISO-8859-1" -->

	<xsl:include href="../includes.xsl"/>

	<!-- Include all the required form item templates, if a new node type is added must add here too -->
	<xsl:include href="form_misc.xsl"/>
	<xsl:include href="form_node_section.xsl"/>
	<xsl:include href="form_node_date.xsl"/>
	<xsl:include href="form_node_check.xsl"/>
	<xsl:include href="form_node_pagelink.xsl"/>
	<xsl:include href="form_node_pageselect.xsl"/>
	<xsl:include href="form_node_itemlink.xsl"/>
	<xsl:include href="form_node_textarea.xsl"/>
	<xsl:include href="form_node_multilang_textarea.xsl"/>
	<xsl:include href="form_node_multilang_textfield.xsl"/>
	<xsl:include href="form_node_textfield.xsl"/>
	<xsl:include href="form_node_href.xsl"/>
	<xsl:include href="form_node_swf.xsl"/>
	<xsl:include href="form_node_file.xsl"/>
	<xsl:include href="form_node_geocode.xsl"/>
	<xsl:include href="form_node_edit_tab.xsl"/>
	<xsl:include href="form_node_itemlink.xsl"/>
	<xsl:include href="form_node_select.xsl"/>

	<xsl:template match="/">
						
			<xsl:for-each select="/data/dupe_xml">
			
				<xsl:apply-templates select="*">
					<xsl:with-param name="parent_id" select="/data/parent_id"/>
					<xsl:with-param name="parent_path" select="/data/parent_path"/>
					<xsl:with-param name="sortkey" select="/data/sortkey"/>
				</xsl:apply-templates>
	
			</xsl:for-each>

	</xsl:template>

	
</xsl:stylesheet>