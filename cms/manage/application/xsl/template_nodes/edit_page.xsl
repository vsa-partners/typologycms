<?xml version="1.0" encoding="utf-8" ?>
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
		encoding="utf-8" 
		/>

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
	<xsl:include href="form_node_textfield.xsl"/>
	<xsl:include href="form_node_multilang_textarea.xsl"/>
	<xsl:include href="form_node_multilang_textfield.xsl"/>
	<xsl:include href="form_node_href.xsl"/>
	<xsl:include href="form_node_swf.xsl"/>
	<xsl:include href="form_node_file.xsl"/>
	<xsl:include href="form_node_geocode.xsl"/>
	<xsl:include href="form_node_edit_tab.xsl"/>
	<xsl:include href="form_node_itemlink.xsl"/>
	<xsl:include href="form_node_select.xsl"/>

	<xsl:variable name="form_base_id" select="'fields[content][data]'" />
	<xsl:variable name="form_base_path" select="'data'" />


	<xsl:template match="/">

			<xsl:call-template name="drawEditPageForm" />



			<!--			
			<xsl:call-template name="displayNodeset">
				<xsl:with-param name="title" select="'$ADMIN_CONF'" />
				<xsl:with-param name="nodes" select="$ADMIN_CONF" />
			</xsl:call-template>
			<xsl:call-template name="displayNodeset">
				<xsl:with-param name="title" select="'Page Nodes'" />
			</xsl:call-template>
			-->


			
	</xsl:template>

	<xsl:template name="drawEditPageForm">

			<xsl:choose>
				<xsl:when test="not(/data/fields/template_id &gt; 0)">
					<div id="tab_content" class="tab_content" style="height: 158px;">
						( This page does not have a template )
						<input type="hidden" name="fields[content]" value="" />
					</div>
				</xsl:when>
				<xsl:when test="count(/data/fields/edit_xml/data/node()) &lt; 1">
					<!-- Whoops, no edit_xml -->
					<div style="height: 200px;">
						( This page does not contain any editable content )
						<input type="hidden" name="fields[content]" value="" />
					</div>
				</xsl:when>
				<xsl:when test="count(/data/fields/edit_xml/data/node()[@type = 'edit_tab']) &lt; 1">

					<xsl:for-each select="/data/fields/edit_xml/data">
	
						<xsl:call-template name="editForm_addChildren">
							<xsl:with-param name="parent_id" select="$form_base_id"/>
							<xsl:with-param name="parent_path" select="$form_base_path"/>
						</xsl:call-template>

						<xsl:value-of select="$nl"/>
					
					</xsl:for-each>

				</xsl:when>
				<xsl:otherwise>

					<!-- Yes edit tabs -->

					<div class="tab_set">
				
						<div class="tab_nav clearfix">
							<ul id="content_tabs">
								<xsl:for-each select="/data/fields/edit_xml/data/node()[@type = 'edit_tab']">
									<li><a href="#tab_content_{name()}"><span><xsl:value-of select="@title"/></span></a></li>
								</xsl:for-each>
							</ul>
						</div>

						<xsl:for-each select="/data/fields/edit_xml/data">

							<xsl:call-template name="editForm_addChildren">
								<xsl:with-param name="parent_id" select="$form_base_id"/>
								<xsl:with-param name="parent_path" select="$form_base_path"/>
							</xsl:call-template>

							<xsl:value-of select="$nl"/>

						</xsl:for-each>
					
					</div>

					<script language="javascript">
						new Control.Tabs('content_tabs', {
							afterChange: function(new_container){  

								refreshCLE();

                                document.observe("dom:loaded", function() {
                                    $(new_container).select('TEXTAREA').each(function(item) {
                                        TNDR.Form.UI.textarea.onChange(item);
                                    });
                                });

							}
						});
					</script>

				</xsl:otherwise>
			</xsl:choose>

				
			<!-- Module Page - Calendar -->
			<!--
			<xsl:if test="/data/fields/type = 'page_calendar'">
				<div id="tab_events" class="tab_content hide_edit_buttons">
					<iframe width="100%" height="450" frameborder="0">
						<xsl:attribute name="src">
							<xsl:value-of select="exslt:node-set($CI_VARS)/data/admin_path"/>
							<xsl:text>page_calendar/edit/</xsl:text>
							<xsl:value-of select="/data/fields/page_id"/>
							<xsl:text>/events</xsl:text>
						</xsl:attribute>
						<xsl:text> </xsl:text>
					</iframe>
				</div>
			</xsl:if>
			-->
			
			

	</xsl:template>
	
</xsl:stylesheet>