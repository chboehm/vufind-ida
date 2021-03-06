<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="/">
        <root>
            <xsl:apply-templates select="//root">
            </xsl:apply-templates>
        </root>
    </xsl:template>

    <xsl:template match="item">
        <xsl:variable name="id" select="@id" />
        <xsl:variable name="isCollection" select="@isCollection" />
        <xsl:variable name="hasChildren" select="@hasChildren" />
        <xsl:variable name="hierarchyState">
            <xsl:choose>
                <xsl:when test="$hasChildren = 'true'">closed</xsl:when>
                <xsl:otherwise></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="baseModule">
            <xsl:choose>
                <xsl:when test="true or $context = 'Record'">
                    <xsl:choose>
                        <xsl:when test="false and $isCollection = 'true'">YYY</xsl:when>
                        <xsl:otherwise>Record</xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise>Collection</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <item state="{$hierarchyState}">
            <content>
                <hasChildren value="{$hasChildren}" />
                <name class="JSTreeID"><xsl:value-of select="$id"/></name>
                <name href="{$baseURL}/{$baseModule}/{$id}/HierarchyTree?hierarchy={$collectionID}&amp;recordID={$id}" title="{$titleText}">
                    <xsl:value-of select="./content/name" />
                </name>
            </content>
            <xsl:apply-templates select="item"/>
        </item>
    </xsl:template>

</xsl:stylesheet>
