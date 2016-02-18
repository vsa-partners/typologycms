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


	<xsl:template match="*[@type = 'geocode']">
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


		<div class="form_row">
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->

			<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false"><xsl:text> </xsl:text></script>
			
			<div class="row">
				<xsl:call-template name="form_addTitle"/>
				<div class="field">
					<div class="field_buttons">
						<div class="loading" style="display: none;"><img src="/cms/manage/assets/img/processing.gif" /></div>
						<button type="button" class="button button_outline_small" onClick="TNDR.Form.Validation.geoCode('{$item_id}'); return false;"><span><xsl:text disable-output-escaping="yes">LOCATE</xsl:text></span></button>
						<button type="button" class="button button_outline_small" onClick="TNDR.Form.Validation.geoCodeClear('{$item_id}'); return false;"><span><xsl:text disable-output-escaping="yes">CLEAR</xsl:text></span></button>
					</div>
					<input type="text" title="{@title}" name="{$item_id}[value]" id="{$item_id}" class="geocode">
						<xsl:attribute name="value">
							<xsl:choose>
								<xsl:when test="node() != ''"><xsl:value-of select="node()"/></xsl:when>
								<xsl:when test="@value != ''"><xsl:value-of select="@value"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="@unknown" /></xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
					</input>
				</div>

				<xsl:call-template name="form_addMultiControl">
					<xsl:with-param name="item_id" select="$item_id" />
					<xsl:with-param name="item_path" select="$item_path" />
					<xsl:with-param name="parent_path" select="$parent_path" />
					<xsl:with-param name="parent_id" select="$parent_id" />
				</xsl:call-template>

				
				<div class="note" id="{$item_id}[note]">
					<xsl:choose>
						<xsl:when test="(@lat != '') and (@long != '')">
							(<xsl:value-of select="@lat"/>,<xsl:value-of select="@long"/>)
						</xsl:when>
						<xsl:otherwise>
							<xsl:text> </xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</div>

				<input type="hidden" class="lat" name="{$item_id}[lat]" id="{$item_id}[lat]" value="{@lat}" />
				<input type="hidden" class="long" name="{$item_id}[long]" id="{$item_id}[long]" value="{@long}" />


			</div>

			<xsl:call-template name="form_addNotes" />
			<xsl:call-template name="form_addHiddentType">
				<xsl:with-param name="item_id" select="$item_id" />
			</xsl:call-template>

		</div>

	</xsl:template>

	<xsl:template match="*[@type = 'streetview']">
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

        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBy-zh_n_nyGG1__Gp8ffwnDDscGs8kdNQ&amp;sensor=false"><xsl:text> </xsl:text></script>

		<div class="form_row">
			<xsl:attribute name="path"><xsl:value-of select="$item_path" /></xsl:attribute> <!-- Add node name, used for duplication -->
			<xsl:attribute name="id"><xsl:value-of select="concat($item_id, '_node')" /></xsl:attribute> <!-- Add node id, used for duplication -->

			<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false"><xsl:text> </xsl:text></script>
			
			<div class="row">
				<xsl:call-template name="form_addTitle"/>
				<div class="field">
					<div class="field_buttons">
						<div class="loading" style="display: none;"><img src="/cms/manage/assets/img/processing.gif" /></div>
						<button type="button" class="button button_outline_small" onClick="TNDR.Form.Validation.streetView('{$item_id}'); return false;"><span><xsl:text disable-output-escaping="yes">LOAD STREETVIEW</xsl:text></span></button>
					</div>
					<input type="text" title="{@title}" name="{$item_id}[value]" id="{$item_id}" class="geocode">
						<xsl:attribute name="value">
							<xsl:choose>
								<xsl:when test="node() != ''"><xsl:value-of select="node()"/></xsl:when>
								<xsl:when test="@value != ''"><xsl:value-of select="@value"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="@unknown" /></xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
					</input>
				</div>

				<xsl:call-template name="form_addMultiControl">
					<xsl:with-param name="item_id" select="$item_id" />
					<xsl:with-param name="item_path" select="$item_path" />
					<xsl:with-param name="parent_path" select="$parent_path" />
					<xsl:with-param name="parent_id" select="$parent_id" />
				</xsl:call-template>
				
				<div class="note" id="{$item_id}[note]">
					<xsl:choose>
						<xsl:when test="(@lat != '') and (@long != '')">
                            <b>Lattitude: </b><xsl:value-of select="@lat"/> / <b>Longitude:</b><xsl:value-of select="@long"/> / <b>Heading: </b><xsl:value-of select="@heading"/> / <b> Pitch: </b><xsl:value-of select="@pitch"/> / <b>Zoom: </b><xsl:value-of select="@zoom"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text> </xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</div>

				<div class="map" id="{$item_id}[map]" style="display:none;"><xsl:text> </xsl:text></div>
				
				<input type="hidden" class="lat transform_streetview" name="{$item_id}[lat]" id="{$item_id}[lat]" value="{@lat}" />
				<input type="hidden" class="long" name="{$item_id}[long]" id="{$item_id}[long]" value="{@long}" />
				<input type="hidden" class="pitch" name="{$item_id}[pitch]" id="{$item_id}[pitch]" value="{@pitch}" />
				<input type="hidden" class="heading" name="{$item_id}[heading]" id="{$item_id}[heading]" value="{@heading}" />
				<input type="hidden" class="zoom" name="{$item_id}[zoom]" id="{$item_id}[zoom]" value="{@zoom}" />

			</div>

			<xsl:call-template name="form_addNotes" />
			<xsl:call-template name="form_addHiddentType">
				<xsl:with-param name="item_id" select="$item_id" />
			</xsl:call-template>

		</div>

	</xsl:template>
	
	
</xsl:stylesheet>