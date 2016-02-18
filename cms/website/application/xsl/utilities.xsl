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


	<xsl:variable name="nl"><xsl:text>
</xsl:text></xsl:variable>



	<xsl:template name="nl2br">
			<xsl:param name="contents" />
			
			<xsl:choose>
					<xsl:when test="contains($contents, '&#10;')">
						<xsl:value-of select="substring-before($contents, '&#10;')" disable-output-escaping="yes" />
						<br />
						<xsl:call-template name="nl2br">
							<xsl:with-param name="contents" select="substring-after($contents, '&#10;')" />
						</xsl:call-template>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$contents" disable-output-escaping="yes" />
					</xsl:otherwise>
			</xsl:choose>
	</xsl:template>

	
	<xsl:template name="explode">
			<xsl:param name="string" />
			<xsl:param name="delim" select="','" />
			<xsl:param name="node" select="'node'" />
			
			<xsl:choose>
				<xsl:when test="not(string-length($string) &gt; 0)">
					<!-- Nothing -->
				</xsl:when>
				<xsl:when test="not(contains($string, $delim))">
					<xsl:element name="{$node}">
						<xsl:value-of select="$string"/>
					</xsl:element>
				</xsl:when>
				<xsl:otherwise>
					<xsl:variable name="opt" select="substring-before($string, $delim)"/>
					<xsl:variable name="end" select="substring-after($string, $delim)"/>
	
					<xsl:element name="{$node}">
						<xsl:value-of select="$opt"/>
					</xsl:element>
	
					<xsl:call-template name="explode">
						<xsl:with-param name="string" select="$end" />
						<xsl:with-param name="delim" select="$delim" />
						<xsl:with-param name="node" select="$node" />
					</xsl:call-template>
	
				</xsl:otherwise>
			</xsl:choose>
		
	</xsl:template>

	
	<xsl:template name="displayNodesetXml">
			<xsl:param name="nodes" select="." />
			<xsl:param name="cdata" select="'FALSE'" />
			
			<xsl:for-each select="exslt:node-set($nodes)/*">
			
	
				<xsl:element name="{name()}">
				
					<xsl:for-each select="@*">
						<xsl:attribute name="{name()}">
							<xsl:value-of select="."/>
						</xsl:attribute>
					</xsl:for-each>
		
					<xsl:choose>	
						<xsl:when test="count(*) &gt; 0">
		
							<!-- Get Children -->
							<xsl:call-template name="displayNodesetXml">
								<xsl:with-param name="cdata" select="$cdata" />
							</xsl:call-template>
							
						</xsl:when>
						<xsl:when test="string-length(node())">
		
							<xsl:if test="$cdata = 'TRUE'">
								<xsl:text disable-output-escaping="yes">&lt;![CDATA[</xsl:text>
							</xsl:if>
		
							<xsl:value-of select="node()" disable-output-escaping="yes" />
		
							<xsl:if test="$cdata = 'TRUE'">
								<xsl:text disable-output-escaping="yes">]]&gt;</xsl:text>
							</xsl:if>
		
						</xsl:when>
						<xsl:otherwise>
							<!-- Nothing -->
						</xsl:otherwise>
					</xsl:choose>
		
				</xsl:element>
	
			</xsl:for-each>
		
	</xsl:template>
		
	
	<xsl:template name="displayNodeset">
			<xsl:param name="nodes" select="." />
			<xsl:param name="depth" select="0" />
			<xsl:param name="indent" select="'    '" />
			<xsl:param name="title"/>
			<xsl:param name="cdata" select="'FALSE'" />
			
			<xsl:if test="$depth = 0">
				<xsl:text disable-output-escaping="yes">
					&lt;div style="border: 2px solid #666; clear: both; font-size: 9px; line-height: 11px; background-color: #fff; color: #666; padding: 10px 20px;"&gt;
					&lt;pre style="margin: 0;"&gt;
				</xsl:text>
			</xsl:if>

			<xsl:if test="$title != ''">
				<strong style="color: #222;"><xsl:value-of select="$title"/></strong>
			</xsl:if>
	
			<xsl:for-each select="exslt:node-set($nodes)/*">
			
				<!-- Indent -->
				<xsl:call-template name="repeat-string">
					<xsl:with-param name="cnt" select="$depth" />
					<xsl:with-param name="str" select="$indent" />
				</xsl:call-template>
				
				<xsl:text>&lt;</xsl:text>
				<xsl:value-of select="name()" />
	
				<xsl:for-each select="@*">
					<xsl:text> </xsl:text>
					<xsl:value-of select="name()" />
					<xsl:text>="</xsl:text>
					<xsl:value-of select="."/>
					<xsl:text>"</xsl:text>
				</xsl:for-each>
	
				<xsl:choose>	
					<xsl:when test="count(*) &gt; 0">
	
						<xsl:text>&gt;</xsl:text>
						<xsl:value-of select="$nl"/>
						
						<!-- Get Children -->
						<xsl:call-template name="displayNodeset">
							<xsl:with-param name="depth" select="$depth + 1" />
							<xsl:with-param name="cdata" select="$cdata" />
						</xsl:call-template>
						
						<!-- Indent -->
						<xsl:call-template name="repeat-string">
							<xsl:with-param name="cnt" select="$depth" />
							<xsl:with-param name="str" select="$indent" />
						</xsl:call-template>
	
						<!-- Close Node -->
						<xsl:text>&lt;/</xsl:text>
						<xsl:value-of select="name()" />
						<xsl:text>&gt;</xsl:text>
	
					</xsl:when>
					<xsl:when test="string-length(node())">
	
						<!-- Has Node Value -->
						<xsl:text>&gt;</xsl:text>
	
						<xsl:if test="$cdata = 'TRUE'">
							<xsl:text>&lt;![CDATA[</xsl:text>
						</xsl:if>
	
						<xsl:value-of select="node()" />
	
						<xsl:if test="$cdata = 'TRUE'">
							<xsl:text>]]&gt;</xsl:text>
						</xsl:if>
	
	
						<!-- Close Node -->
						<xsl:text>&lt;/</xsl:text>
						<xsl:value-of select="name()" />
						<xsl:text>&gt;</xsl:text>
	
					</xsl:when>
					<xsl:otherwise>
						<!-- Nothing, Just close it. -->
						<xsl:text> /&gt;</xsl:text>				
					</xsl:otherwise>
				</xsl:choose>
	
				<xsl:value-of select="$nl"/>
	
			</xsl:for-each>
			
			<xsl:if test="$depth = 0">
				<xsl:text disable-output-escaping="yes">
					&lt;/pre&gt;
					&lt;/div&gt;
				</xsl:text>
			</xsl:if>			
		
	</xsl:template>
	
	
	<xsl:template name="repeat-string">
			<!-- http://aspn.activestate.com/ASPN/Cookbook/XSLT/Recipe/148987 -->
			<xsl:param name="str"/><!-- The string to repeat -->
			<xsl:param name="cnt"/><!-- The number of times to repeat the string -->
			<xsl:param name="pfx"/><!-- The prefix to add to the string -->

			<xsl:choose>
				<xsl:when test="$cnt = 0"><xsl:value-of select="$pfx"/></xsl:when>
				<xsl:when test="$cnt mod 2 = 1">
					<xsl:call-template name="repeat-string">
						<xsl:with-param name="str" select="concat($str,$str)"/>
						<xsl:with-param name="cnt" select="($cnt - 1) div 2"/>
						<xsl:with-param name="pfx" select="concat($pfx,$str)"/>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="repeat-string">
						<xsl:with-param name="str" select="concat($str,$str)"/>
						<xsl:with-param name="cnt" select="$cnt div 2"/>
						<xsl:with-param name="pfx" select="$pfx"/>
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>
	
		
	<xsl:template name="string-replace">
			<xsl:param name="value" select="''" />
			<xsl:param name="find" select="''" />
			<xsl:param name="replace" select="''" />
	
			<xsl:variable name="first">
				<xsl:choose>
					<xsl:when test="contains($value,$find)">
						<xsl:value-of select="substring-before($value,$find)"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$value"/>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<xsl:variable name="middle">
				<xsl:if test="contains($value,$find)">
					<xsl:value-of select="$replace"/>
				</xsl:if>
			</xsl:variable>
			<xsl:variable name="last">
				<xsl:if test="contains($value,$find)">
					<xsl:choose>
						<xsl:when test="contains(substring-after($value,$find),$find)">
							<xsl:call-template name="string-replace">
								<xsl:with-param name="value" select="substring-after($value,$find)" />
								<xsl:with-param name="find" select="$find" />
								<xsl:with-param name="replace" select="$replace" />
							</xsl:call-template>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="substring-after($value,$find)" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:if>
			</xsl:variable>
	
			<xsl:value-of disable-output-escaping="yes" select="concat($first,$middle,$last)" />
	</xsl:template>	

	
	<xsl:template name="escape-quotes">
			<xsl:param name="text"/>
			<xsl:param name="quote-char"><xsl:text>'</xsl:text></xsl:param>
			<xsl:param name="escaped-quote"><xsl:text>\'</xsl:text></xsl:param>
	
			<xsl:choose>
				<xsl:when test="contains($text,$quote-char)">
					<xsl:variable name="pre" select="substring-before($text,$quote-char)"/>
					<xsl:variable name="post">
						<xsl:call-template name="escape-quotes">
							<xsl:with-param name="text" select="substring-after($text,concat($pre,$quote-char))"/>
						</xsl:call-template>
					</xsl:variable>
					<xsl:value-of select="concat($pre,$escaped-quote,$post)"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$text"/>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>


</xsl:stylesheet>