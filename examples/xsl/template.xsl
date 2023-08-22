<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:localize="urn:carica:localize"
  exclude-result-prefixes="localize">

  <xsl:import href="../../src/localize.xsl"/>

  <xsl:param name="LOCALIZE_LANGUAGE">de</xsl:param>
  <xsl:param name="LOCALIZE_MESSAGES" select="document(localize:messages-file('de', './example'))"/>

  <xsl:template match="/">
    <div>
      <xsl:call-template name="localize:message">
        <xsl:with-param name="message">Example</xsl:with-param>
      </xsl:call-template>
    </div>
    <div>
      <xsl:call-template name="localize:message">
        <xsl:with-param name="id">example.one</xsl:with-param>
        <xsl:with-param name="message">Example</xsl:with-param>
      </xsl:call-template>
    </div>
    <div>
      <xsl:call-template name="localize:message">
        <xsl:with-param name="id">example.two</xsl:with-param>
        <xsl:with-param name="message">Example: {foo}</xsl:with-param>
        <xsl:with-param name="values" select="/values/*"/>
      </xsl:call-template>
    </div>
    <div>
      <xsl:call-template name="localize:message">
        <xsl:with-param name="id">example.three</xsl:with-param>
        <xsl:with-param name="message">Example: {foo}</xsl:with-param>
        <xsl:with-param name="values">
          <foo>PARAMETER</foo>
        </xsl:with-param>
      </xsl:call-template>
    </div>
    <div>
      <xsl:call-template name="localize:message">
        <xsl:with-param name="id">example.four</xsl:with-param>
        <xsl:with-param name="meaning">Category</xsl:with-param>
        <xsl:with-param name="message">Example 4</xsl:with-param>
      </xsl:call-template>
    </div>
    <div>
      <xsl:call-template name="localize:message">
        <xsl:with-param name="id">example.five</xsl:with-param>
        <xsl:with-param name="meaning">Category</xsl:with-param>
        <xsl:with-param name="message">
          Example
          <b>Five</b>
        </xsl:with-param>
        <xsl:with-param name="type">html</xsl:with-param>
      </xsl:call-template>
    </div>
  </xsl:template>

</xsl:stylesheet>
