<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:localize="urn:carica:localize"
  xmlns:xliff="urn:oasis:names:tc:xliff:document:1.2"
  xmlns:exsl="http://exslt.org/common"
  xmlns:php="http://php.net/xsl"
  xmlns:func="http://exslt.org/functions"
  exclude-result-prefixes="localize"
  extension-element-prefixes="exsl func php">

  <xsl:variable name="LOCALIZE_MESSAGES"/>
  <xsl:variable name="LOCALIZE_LANGUAGE">en</xsl:variable>
  <xsl:variable name="LOCALIZE_SOURCE_LANGUAGE">en</xsl:variable>

  <xsl:variable name="CARICA_LOCALIZE_CALLBACK" select="'Carica\Localize\XSL\Callbacks::handleFunctionCall'"/>
  <xsl:variable name="CARICA_LOCALIZE_MODULE_MESSAGES" select="'Messages'"/>

  <func:function name="localize:messages-file">
    <xsl:param name="language"/>
    <xsl:param name="name">messages</xsl:param>
    <xsl:param name="extension">xlf</xsl:param>
    <xsl:param name="source-language" select="$LOCALIZE_SOURCE_LANGUAGE"/>
    <xsl:variable name="file">
      <xsl:value-of select="$name"/>
      <xsl:if test="$language != $source-language">
        <xsl:text>.</xsl:text>
        <xsl:value-of select="$language"/>
      </xsl:if>
      <xsl:text>.</xsl:text>
      <xsl:value-of select="$extension"/>
    </xsl:variable>
    <func:result select="$file"/>
  </func:function>

  <func:function
    name="localize:serializeNodes">
    <xsl:param name="nodes"/>
    <xsl:param name="type">plaintext</xsl:param>
    <xsl:variable name="result">
      <xsl:choose>
        <xsl:when test="$type = 'html' or $type = 'xhtml'">
          <xsl:value-of select="php:function($CARICA_LOCALIZE_CALLBACK, $CARICA_LOCALIZE_MODULE_MESSAGES, 'serializeNodes', exsl:node-set($nodes), string($type))"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="php:function($CARICA_LOCALIZE_CALLBACK, $CARICA_LOCALIZE_MODULE_MESSAGES, 'serializeNodes', exsl:node-set($nodes), string($type))"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <func:result select="$result"/>
  </func:function>

  <xsl:template name="localize:message">
    <xsl:param name="message"/>
    <xsl:param name="meaning" select="''"/>
    <xsl:param
      name="id"
      select="php:function($CARICA_LOCALIZE_CALLBACK, $CARICA_LOCALIZE_MODULE_MESSAGES, 'generateId', string($message), string($meaning))"/>
    <xsl:param name="description" select="''"/>
    <xsl:param name="locale" select="$LOCALIZE_LANGUAGE"/>
    <xsl:param name="values" select="false()"/>
    <xsl:param name="type" select="'plaintext'"/>

    <xsl:variable name="targetMessage">
      <xsl:choose>
        <xsl:when test="$LOCALIZE_MESSAGES">
          <xsl:variable
            name="translation"
            select="$LOCALIZE_MESSAGES/xliff:xliff//xliff:trans-unit[@id = $id]">
          </xsl:variable>
          <xsl:choose>
            <xsl:when test="$translation and $translation/xliff:target">
              <xsl:value-of select="$translation/xliff:target"/>
            </xsl:when>
            <xsl:when test="$translation and $translation/xliff:source">
              <xsl:copy-of select="$translation/xliff:source"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="localize:serializeNodes($message)"/>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="localize:serializeNodes($message)"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>

    <xsl:variable
      name="formattedMessage"
      select="php:function($CARICA_LOCALIZE_CALLBACK, $CARICA_LOCALIZE_MODULE_MESSAGES, 'formatMessage', string($locale), string($targetMessage), exsl:node-set($values), string($type))"/>

    <xsl:choose>
      <xsl:when test="$type = 'html' or $type = 'xhtml'">
        <xsl:copy-of select="$formattedMessage"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$formattedMessage"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

</xsl:stylesheet>
