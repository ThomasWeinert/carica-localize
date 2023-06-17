<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:i18n="urn:i18n"
  xmlns:xliff="urn:oasis:names:tc:xliff:document:1.2"
  xmlns:exsl="http://exslt.org/common"
  xmlns:php="http://php.net/xsl"
  exclude-result-prefixes="i18n"
  extension-element-prefixes="exsl php">

  <xsl:variable name="I18N_MESSAGES"/>
  <xsl:variable name="I18N_LOCALE">en</xsl:variable>

  <xsl:variable name="I18N_CALLBACK" select="'I18N\Messages\XSL\Callbacks::handleFunctionCall'"/>
  <xsl:variable name="I18N_MODULE_MESSAGES" select="'Messages'"/>

  <xsl:template name="i18n:message">
    <xsl:param name="message"/>
    <xsl:param name="id"/>
    <xsl:param name="meaning" select="''"/>
    <xsl:param name="description" select="''"/>
    <xsl:param name="locale" select="$I18N_LOCALE"/>
    <xsl:param name="values"/>

    <xsl:variable name="targetMessage">
      <xsl:choose>
        <xsl:when test="$I18N_MESSAGES">
          <xsl:variable
            name="translation"
            select="$I18N_MESSAGES/xliff:xliff//xliff:trans-unit[@id = $id]">
          </xsl:variable>
          <xsl:choose>
            <xsl:when test="$translation and $translation/xliff:target">
              <xsl:value-of select="$translation/xliff:target"/>
            </xsl:when>
            <xsl:when test="$translation and $translation/xliff:source">
              <xsl:value-of select="$translation/xliff:source"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="$message"/>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$message"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>

    <xsl:variable
      name="formattedMessage"
      select="php:function($I18N_CALLBACK, $I18N_MODULE_MESSAGES, 'formatMessage', string($locale), string($targetMessage), exsl:node-set($values))"/>

    <xsl:value-of select="$formattedMessage"/>
  </xsl:template>

</xsl:stylesheet>
