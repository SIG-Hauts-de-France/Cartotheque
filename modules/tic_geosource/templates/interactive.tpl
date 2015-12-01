<?xml version="1.0" encoding="UTF-8"?>
<gmd:MD_Metadata xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:gml="http://www.opengis.net/gml" xmlns:geonet="http://www.fao.org/geonetwork">
?$uuid
  <gmd:fileIdentifier>
    <gco:CharacterString>{$uuid}</gco:CharacterString>
  </gmd:fileIdentifier>
$uuid?
  <gmd:language>
    <gmd:LanguageCode codeList="http://www.loc.gov/standards/iso639-2/" codeListValue="fre" />
  </gmd:language>
  <gmd:characterSet>
    <gmd:MD_CharacterSetCode codeListValue="utf8" codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_CharacterSetCode" />
  </gmd:characterSet>
  <gmd:hierarchyLevel>
    <gmd:MD_ScopeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_ScopeCode" codeListValue="dataset" />
  </gmd:hierarchyLevel>
  <gmd:contact>
    <gmd:CI_ResponsibleParty>
      <gmd:organisationName>
        <gco:CharacterString>{$author_name}</gco:CharacterString>
      </gmd:organisationName>
      <gmd:contactInfo>
        <gmd:CI_Contact>
          <gmd:phone>
            <gmd:CI_Telephone />
          </gmd:phone>
          <gmd:address>
            <gmd:CI_Address>
              <gmd:electronicMailAddress>
                <gco:CharacterString>{$author_email}</gco:CharacterString>
              </gmd:electronicMailAddress>
            </gmd:CI_Address>
          </gmd:address>
        </gmd:CI_Contact>
      </gmd:contactInfo>
      <gmd:role>
        <gmd:CI_RoleCode codeListValue="pointOfContact" codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#CI_RoleCode" />
      </gmd:role>
    </gmd:CI_ResponsibleParty>
  </gmd:contact>
  <gmd:dateStamp>
    <gco:DateTime>{$creation_date}</gco:DateTime>
  </gmd:dateStamp>
  <gmd:metadataStandardName>
    <gco:CharacterString>ISO 19115:2003/19139</gco:CharacterString>
  </gmd:metadataStandardName>
  <gmd:metadataStandardVersion>
    <gco:CharacterString>1.0</gco:CharacterString>
  </gmd:metadataStandardVersion>
  <gmd:referenceSystemInfo>
    <gmd:MD_ReferenceSystem>
      <gmd:referenceSystemIdentifier>
        <gmd:RS_Identifier>
          <gmd:code>
            <gco:CharacterString>Lambert 93</gco:CharacterString>
          </gmd:code>
        </gmd:RS_Identifier>
      </gmd:referenceSystemIdentifier>
    </gmd:MD_ReferenceSystem>
  </gmd:referenceSystemInfo>
  <gmd:identificationInfo>
    <gmd:MD_DataIdentification>
      <gmd:citation>
        <gmd:CI_Citation>
          <gmd:title>
            <gco:CharacterString>{$map_title}</gco:CharacterString>
          </gmd:title>
          <gmd:date>
            <gmd:CI_Date>
              <gmd:date>
                <gco:Date>{$creation_date}</gco:Date>
              </gmd:date>
              <gmd:dateType>
                <gmd:CI_DateTypeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#CI_DateTypeCode" codeListValue="publication" />
              </gmd:dateType>
            </gmd:CI_Date>
          </gmd:date>
          <gmd:presentationForm>
            <gmd:CI_PresentationFormCode codeListValue="mapDigital" codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#CI_PresentationFormCode" />
          </gmd:presentationForm>
        </gmd:CI_Citation>
      </gmd:citation>
      <gmd:abstract>
        <gco:CharacterString>{$map_abstract}</gco:CharacterString>
      </gmd:abstract>
      <gmd:credit gco:nilReason="missing">
        <gco:CharacterString />
      </gmd:credit>
      <gmd:pointOfContact>
        <gmd:CI_ResponsibleParty>
          <gmd:organisationName>
            <gco:CharacterString>{$author_name}</gco:CharacterString>
          </gmd:organisationName>
          <gmd:contactInfo>
            <gmd:CI_Contact>
              <gmd:phone>
                <gmd:CI_Telephone />
              </gmd:phone>
              <gmd:address>
                <gmd:CI_Address>
                  <gmd:electronicMailAddress>
                    <gco:CharacterString>{$author_email}</gco:CharacterString>
                  </gmd:electronicMailAddress>
                </gmd:CI_Address>
              </gmd:address>
            </gmd:CI_Contact>
          </gmd:contactInfo>
          <gmd:role>
            <gmd:CI_RoleCode codeListValue="pointOfContact" codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#CI_RoleCode" />
          </gmd:role>
        </gmd:CI_ResponsibleParty>
      </gmd:pointOfContact>
[$thumbnails]
         <gmd:graphicOverview>
            <gmd:MD_BrowseGraphic>
               <gmd:fileName>
                  <gco:CharacterString>{$url}</gco:CharacterString>
               </gmd:fileName>
               <gmd:fileDescription>
                  <gco:CharacterString>{$description}</gco:CharacterString>
               </gmd:fileDescription>
               <gmd:fileType>
                  <gco:CharacterString>{$filetype}</gco:CharacterString>
               </gmd:fileType>
            </gmd:MD_BrowseGraphic>
         </gmd:graphicOverview>
[/$thumbnails]
      <gmd:descriptiveKeywords>
        <gmd:MD_Keywords>
[$keywords]
          <gmd:keyword>
            <gco:CharacterString>{$value}</gco:CharacterString>
          </gmd:keyword>
[/$keywords]
          <gmd:type>
            <gmd:MD_KeywordTypeCode codeListValue="theme" codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_KeywordTypeCode" />
          </gmd:type>
        </gmd:MD_Keywords>
      </gmd:descriptiveKeywords>
      <gmd:descriptiveKeywords xmlns:gn="http://www.fao.org/geonetwork"
                                  xmlns:gmx="http://www.isotc211.org/2005/gmx"
                                  xmlns:srv="http://www.isotc211.org/2005/srv"
                                  xmlns:xlink="http://www.w3.org/1999/xlink">
            <gmd:MD_Keywords>
[$themes]
               <gmd:keyword>
                  <gco:CharacterString>{$value}</gco:CharacterString>
               </gmd:keyword>
[/$themes]
               <gmd:type>
                  <gmd:MD_KeywordTypeCode codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_KeywordTypeCode"
                                          codeListValue="theme"/>
               </gmd:type>
               <gmd:thesaurusName>
                  <gmd:CI_Citation>
                     <gmd:title>
                        <gco:CharacterString>GEMET - INSPIRE themes, version 1.0</gco:CharacterString>
                     </gmd:title>
                     <gmd:date>
                        <gmd:CI_Date>
                           <gmd:date>
                              <gco:Date>2008-06-01</gco:Date>
                           </gmd:date>
                           <gmd:dateType>
                              <gmd:CI_DateTypeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_DateTypeCode"
                                                   codeListValue="publication"/>
                           </gmd:dateType>
                        </gmd:CI_Date>
                     </gmd:date>
                     <gmd:identifier>
                        <gmd:MD_Identifier>
                           <gmd:code>
                              <gmx:Anchor xlink:href="http://localhost:8080/geosource/srv/fre/thesaurus.download?ref=external.theme.inspire-theme">geonetwork.thesaurus.external.theme.inspire-theme</gmx:Anchor>
                           </gmd:code>
                        </gmd:MD_Identifier>
                     </gmd:identifier>
                  </gmd:CI_Citation>
               </gmd:thesaurusName>
            </gmd:MD_Keywords>
      </gmd:descriptiveKeywords>
      <gmd:language>
        <gmd:LanguageCode codeList="http://www.loc.gov/standards/iso639-2/" codeListValue="fre" />
      </gmd:language>
      <gmd:topicCategory>
        <gmd:MD_TopicCategoryCode>{$category}</gmd:MD_TopicCategoryCode>
      </gmd:topicCategory>
      <gmd:extent>
        <gmd:EX_Extent>
          <gmd:geographicElement>
            <gmd:EX_GeographicBoundingBox>
              <gmd:westBoundLongitude>
                <gco:Decimal>{$bbox.west}</gco:Decimal>
              </gmd:westBoundLongitude>
              <gmd:eastBoundLongitude>
                <gco:Decimal>{$bbox.east}</gco:Decimal>
              </gmd:eastBoundLongitude>
              <gmd:southBoundLatitude>
                <gco:Decimal>{$bbox.south}</gco:Decimal>
              </gmd:southBoundLatitude>
              <gmd:northBoundLatitude>
                <gco:Decimal>{$bbox.north}</gco:Decimal>
              </gmd:northBoundLatitude>
            </gmd:EX_GeographicBoundingBox>
          </gmd:geographicElement>
          <gmd:temporalElement>
            <gmd:EX_TemporalExtent>
              <gmd:extent>
                <gml:TimeInstant gml:id="d17414e182a1052958">
                  <gml:timePosition />
                </gml:TimeInstant>
              </gmd:extent>
            </gmd:EX_TemporalExtent>
          </gmd:temporalElement>
        </gmd:EX_Extent>
      </gmd:extent>
    </gmd:MD_DataIdentification>
  </gmd:identificationInfo>
  <gmd:distributionInfo>
    <gmd:MD_Distribution>
      <gmd:distributionFormat>
        <gmd:MD_Format>
          <gmd:name>
            <gco:CharacterString>PDF</gco:CharacterString>
          </gmd:name>
          <gmd:version>
            <gco:CharacterString>1.7</gco:CharacterString>
          </gmd:version>
        </gmd:MD_Format>
      </gmd:distributionFormat>
      <gmd:transferOptions>
        <gmd:MD_DigitalTransferOptions>
          <gmd:onLine>
            <gmd:CI_OnlineResource>
              <gmd:linkage>
                <gmd:URL>{$url_carte}</gmd:URL>
              </gmd:linkage>
              <gmd:protocol>
                <gco:CharacterString>OGC:WMS</gco:CharacterString>
              </gmd:protocol>
              <gmd:name>
                <gco:CharacterString>{$url_carte}</gco:CharacterString>
              </gmd:name>
              <gmd:description gco:nilReason="missing">
                <gco:CharacterString />
              </gmd:description>
            </gmd:CI_OnlineResource>
          </gmd:onLine>
[$files]
          <gmd:onLine>
            <gmd:CI_OnlineResource>
              <gmd:linkage>
                <gmd:URL>{$url}</gmd:URL>
              </gmd:linkage>
              <gmd:protocol>
                <gco:CharacterString>WWW:DOWNLOAD-1.0-http--download</gco:CharacterString>
              </gmd:protocol>
              <gmd:name>
                <gmx:MimeFileType xmlns:gmx="http://www.isotc211.org/2005/gmx" type="" />
              </gmd:name>
              <gmd:description>
                <gco:CharacterString>{$name}</gco:CharacterString>
              </gmd:description>
            </gmd:CI_OnlineResource>
          </gmd:onLine>
[/$files]
        </gmd:MD_DigitalTransferOptions>
      </gmd:transferOptions>
    </gmd:MD_Distribution>
  </gmd:distributionInfo>
  <gmd:dataQualityInfo>
    <gmd:DQ_DataQuality>
      <gmd:scope>
        <gmd:DQ_Scope />
      </gmd:scope>
      <gmd:report>
        <gmd:DQ_DomainConsistency>
          <gmd:result xmlns:gn="http://www.fao.org/geonetwork" xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:srv="http://www.isotc211.org/2005/srv" xmlns:xlink="http://www.w3.org/1999/xlink">
            <gmd:DQ_ConformanceResult>
              <gmd:specification>
                <gmd:CI_Citation>
                  <gmd:title>
                    <gco:CharacterString>{$map_title}</gco:CharacterString>
                  </gmd:title>
                  <gmd:date>
                    <gmd:CI_Date>
                      <gmd:date>
                        <gco:DateTime>{$creation_date}</gco:DateTime>
                      </gmd:date>
                      <gmd:dateType>
                        <gmd:CI_DateTypeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#CI_DateTypeCode" codeListValue="publication" />
                      </gmd:dateType>
                    </gmd:CI_Date>
                  </gmd:date>
                </gmd:CI_Citation>
              </gmd:specification>
              <gmd:explanation gco:nilReason="missing">
                <gco:CharacterString />
              </gmd:explanation>
            </gmd:DQ_ConformanceResult>
          </gmd:result>
        </gmd:DQ_DomainConsistency>
      </gmd:report>
      <gmd:lineage>
        <gmd:LI_Lineage>
          <gmd:statement>
            <gco:CharacterString>{$source_description} {$source_url} {$source_date}</gco:CharacterString>
          </gmd:statement>
        </gmd:LI_Lineage>
      </gmd:lineage>
    </gmd:DQ_DataQuality>
  </gmd:dataQualityInfo>
</gmd:MD_Metadata>
