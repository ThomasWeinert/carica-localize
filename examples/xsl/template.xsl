<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:i18n="urn:i18n"
  exclude-result-prefixes="i18n">

  <xsl:import href="i18n://Messages"/>

  <xsl:param name="I18N_LOCALE">de</xsl:param>
  <xsl:param name="I18N_MESSAGES" select="document(i18n:messages-file('de', './example'))"/>

  <xsl:template match="/">
    <div>
      <xsl:call-template name="i18n:message">
        <xsl:with-param name="id">example.one</xsl:with-param>
        <xsl:with-param name="message">Example</xsl:with-param>
      </xsl:call-template>
    </div>
    <div>
      <xsl:call-template name="i18n:message">
        <xsl:with-param name="id">example.two</xsl:with-param>
        <xsl:with-param name="message">Example: {foo}</xsl:with-param>
        <xsl:with-param name="values" select="/values/*"/>
      </xsl:call-template>
    </div>
    <div>
      <xsl:call-template name="i18n:message">
        <xsl:with-param name="id">example.three</xsl:with-param>
        <xsl:with-param name="message">Example: {foo}</xsl:with-param>
        <xsl:with-param name="values">
          <foo>PARAMETER</foo>
        </xsl:with-param>
      </xsl:call-template>
    </div>
  </xsl:template>

</xsl:stylesheet>
