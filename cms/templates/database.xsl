<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exslt="http://exslt.org/common"
	xmlns:php="http://php.net/xsl"
	exclude-result-prefixes="xsl php exslt">

	<xsl:output method="html" media-type="text/html"
		indent="yes"
		omit-xml-declaration="yes"
		encoding="UTF-8" />

	<!-- -->
	<xsl:variable name="PAGE" select="/data"/>
	<xsl:variable name="PAGE_ID" select="/data/page_id"/>
	<xsl:variable name="CONTENT" select="/data/content/data"/>
	<xsl:include href="../website/application/xsl/includes.xsl"/>
	<xsl:include href="_includes.xsl"/>

	<xsl:param name="ISAJAX" />

	<!-- -->

	<xsl:template match="/">
        
        <xsl:choose>
            <xsl:when test="$ISAJAX">

                <!-- AJAX Search Request -->
                <xsl:call-template name="_display" />

            </xsl:when>
            <xsl:otherwise>
            
                <!-- Initial Page View -->
                
                <xsl:variable name="page_attribute_options">
                    <xsl:call-template name="getSectionAttributeOptions">
                        <xsl:with-param name="template_id" select="$PAGE/template_options/child_template/node" /> <!-- This is the template id of child pages, not the folder itself -->
                    </xsl:call-template>			
                </xsl:variable>
        
                <!--
                <xsl:call-template name="displayNodeset">
                    <xsl:with-param name="title" select="'page_attribute_options'" />
                    <xsl:with-param name="nodes" select="$page_attribute_options" />
                </xsl:call-template>
                -->
                
                <xsl:for-each select="exslt:node-set($page_attribute_options)/data/*">
                    <xsl:variable name="_group_key" select="key"/>
                    <ul>
                        <li>
                            <h6><xsl:value-of select="title"/></h6>
                            <ul>
                            <xsl:for-each select="options/*">
                                <li><a href="?attribute[{$_group_key}]={name()}"><xsl:value-of select="node()"/></a></li>
                            </xsl:for-each>     
                            </ul>
                        </li>                    
                    </ul>                
                </xsl:for-each>
                
                <div style="border: 10px solid red;">
                
                    <div id="search_display">

                        <div id="search_results">
                            <xsl:call-template name="_display" />
                        </div>
                        
                    </div><!-- #search_display -->
                    
                </div>


            </xsl:otherwise>
        </xsl:choose>
	

	</xsl:template>
	
	
	<xsl:template name="_display">
	
        <xsl:variable name="results">
            <xsl:call-template name="getPageByAttribute">
                <xsl:with-param name="key" select="'attribute'" />
                <xsl:with-param name="parent_id" select="$PAGE/page_id" />
            </xsl:call-template>			
        </xsl:variable>

        <xsl:call-template name="displayNodeset">
            <xsl:with-param name="title" select="'results'" />
            <xsl:with-param name="nodes" select="$results" />
        </xsl:call-template>
	
	
	</xsl:template>
	

</xsl:stylesheet>