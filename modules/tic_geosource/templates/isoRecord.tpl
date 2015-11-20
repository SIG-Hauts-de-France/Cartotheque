<?xml version="1.0" encoding="UTF-8"?>
<gmd:MD_Metadata xmlns:gmd="http://www.isotc211.org/2005/gmd"
                 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                 xmlns:gml="http://www.opengis.net/gml"
                 xmlns:gts="http://www.isotc211.org/2005/gts"
                 xmlns:gco="http://www.isotc211.org/2005/gco"
                 xmlns:geonet="http://www.fao.org/geonetwork"
				 xsi:schemaLocation="http://www.isotc211.org/2005/gmd http://www.isotc211.org/2005/gmd/gmd.xsd http://www.isotc211.org/2005/srv http://schemas.opengis.net/iso/19139/20060504/srv/srv.xsd">
?$uuid
   <gmd:fileIdentifier>
      <gco:CharacterString>{$uuid}</gco:CharacterString>
   </gmd:fileIdentifier>
$uuid?
   <gmd:language gco:nilReason="missing">
      <gco:CharacterString/>
   </gmd:language>
   <gmd:characterSet>
      <gmd:MD_CharacterSetCode codeListValue="utf8"
                               codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_CharacterSetCode"/>
   </gmd:characterSet>
   <gmd:hierarchyLevel>
     <gmd:MD_ScopeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_ScopeCode" codeListValue="dataset" />
   </gmd:hierarchyLevel>
   <gmd:contact>
      <gmd:CI_ResponsibleParty>
         <gmd:individualName>
            <gco:CharacterString>{$author_name}</gco:CharacterString>
         </gmd:individualName>
         <gmd:organisationName gco:nilReason="missing">
            <gco:CharacterString />
         </gmd:organisationName>
         <gmd:positionName gco:nilReason="missing">
            <gco:CharacterString />
         </gmd:positionName>
         <gmd:contactInfo>
            <gmd:CI_Contact>
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
            <gmd:CI_RoleCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#CI_RoleCode"
                             codeListValue="pointOfContact"/>
         </gmd:role>
      </gmd:CI_ResponsibleParty>
  </gmd:contact>
   <gmd:dateStamp>
      <gco:DateTime>2015-11-20T09:57:57</gco:DateTime>
   </gmd:dateStamp>
   <gmd:metadataStandardName>
      <gco:CharacterString>ISO 19115:2003/19139</gco:CharacterString>
   </gmd:metadataStandardName>
   <gmd:metadataStandardVersion>
      <gco:CharacterString>1.0</gco:CharacterString>
   </gmd:metadataStandardVersion>
   <gmd:spatialRepresentationInfo>
      <gmd:MD_VectorSpatialRepresentation>
         <gmd:topologyLevel>
            <gmd:MD_TopologyLevelCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_TopologyLevelCode"
                                      codeListValue="abstract"/>
         </gmd:topologyLevel>
         <gmd:geometricObjects>
            <gmd:MD_GeometricObjects>
               <gmd:geometricObjectType>
                  <gmd:MD_GeometricObjectTypeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_GeometricObjectTypeCode"
                                                  codeListValue="complex"/>
               </gmd:geometricObjectType>
            </gmd:MD_GeometricObjects>
         </gmd:geometricObjects>
      </gmd:MD_VectorSpatialRepresentation>
   </gmd:spatialRepresentationInfo>
   <gmd:referenceSystemInfo>
      <gmd:MD_ReferenceSystem>
         <gmd:referenceSystemIdentifier>
            <gmd:RS_Identifier>
               <gmd:code>
                  <gco:CharacterString>${map_number}</gco:CharacterString>
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
                        <gmd:CI_DateTypeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#CI_DateTypeCode"
                                             codeListValue="publication"/>
                     </gmd:dateType>
                  </gmd:CI_Date>
               </gmd:date>
               <gmd:edition>
                  <gco:CharacterString>First</gco:CharacterString>
               </gmd:edition>
               <gmd:presentationForm>
                  <gmd:CI_PresentationFormCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#CI_PresentationFormCode"
                                               codeListValue="mapDigital"/>
               </gmd:presentationForm>
            </gmd:CI_Citation>
         </gmd:citation>
         <gmd:abstract>
            <gco:CharacterString>{$map_abstract}</gco:CharacterString>
         </gmd:abstract>
         <gmd:purpose gco:nilReason="missing">
            <gco:CharacterString/>
         </gmd:purpose>
         <gmd:status>
            <gmd:MD_ProgressCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_ProgressCode"
                                 codeListValue="completed"/>
         </gmd:status>
         <gmd:pointOfContact>
            <gmd:CI_ResponsibleParty>
               <gmd:individualName>
                  <gco:CharacterString>{$author_name}</gco:CharacterString>
               </gmd:individualName>
               <gmd:organisationName gco:nilReason="missing">
                  <gco:CharacterString />
               </gmd:organisationName>
               <gmd:positionName gco:nilReason="missing">
                  <gco:CharacterString />
               </gmd:positionName>
               <gmd:contactInfo>
                  <gmd:CI_Contact>
                     <gmd:address>
                        <gmd:CI_Address>
                           <gmd:deliveryPoint gco:nilReason="missing">
                              <gco:CharacterString />
                           </gmd:deliveryPoint>
                           <gmd:city gco:nilReason="missing">
                              <gco:CharacterString />
                           </gmd:city>
                           <gmd:administrativeArea gco:nilReason="missing">
                              <gco:CharacterString />
                           </gmd:administrativeArea>
                           <gmd:postalCode gco:nilReason="missing">
                              <gco:CharacterString />
                           </gmd:postalCode>
                           <gmd:country gco:nilReason="missing">
                              <gco:CharacterString />
                           </gmd:country>
                           <gmd:electronicMailAddress>
                              <gco:CharacterString>{$author_email}</gco:CharacterString>
                           </gmd:electronicMailAddress>
                        </gmd:CI_Address>
                     </gmd:address>
                  </gmd:CI_Contact>
               </gmd:contactInfo>
               <gmd:role>
                  <gmd:CI_RoleCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#CI_RoleCode"
                                   codeListValue="pointOfContact"/>
               </gmd:role>
            </gmd:CI_ResponsibleParty>
         </gmd:pointOfContact>
         <gmd:resourceMaintenance>
            <gmd:MD_MaintenanceInformation>
               <gmd:maintenanceAndUpdateFrequency>
                  <gmd:MD_MaintenanceFrequencyCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_MaintenanceFrequencyCode"
                                                   codeListValue="asNeeded"/>
               </gmd:maintenanceAndUpdateFrequency>
            </gmd:MD_MaintenanceInformation>
         </gmd:resourceMaintenance>
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
                  <gmd:MD_KeywordTypeCode codeList="./resources/codeList.xml#MD_KeywordTypeCode" codeListValue="theme"/>
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
         <gmd:descriptiveKeywords>
            <gmd:MD_Keywords>
               <gmd:keyword>
                  <gco:CharacterString/>
               </gmd:keyword>
               <gmd:type>
                  <gmd:MD_KeywordTypeCode codeList="" codeListValue=""/>
               </gmd:type>
            </gmd:MD_Keywords>
         </gmd:descriptiveKeywords>
         <gmd:resourceConstraints>
            <gmd:MD_LegalConstraints>
               <gmd:accessConstraints>
                  <gmd:MD_RestrictionCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_RestrictionCode"
                                          codeListValue="copyright"/>
               </gmd:accessConstraints>
               <gmd:useConstraints>
                  <gmd:MD_RestrictionCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_RestrictionCode"
                                          codeListValue="copyright"/>
               </gmd:useConstraints>
               <gmd:otherConstraints gco:nilReason="missing">
                  <gco:CharacterString/>
               </gmd:otherConstraints>
            </gmd:MD_LegalConstraints>
         </gmd:resourceConstraints>
         <gmd:spatialRepresentationType>
            <gmd:MD_SpatialRepresentationTypeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_SpatialRepresentationTypeCode"
                                                  codeListValue="vector"/>
         </gmd:spatialRepresentationType>
         <gmd:spatialResolution>
            <gmd:MD_Resolution>
               <gmd:equivalentScale>
                  <gmd:MD_RepresentativeFraction>
                     <gmd:denominator>
                        <gco:Integer>{$map_resolution}</gco:Integer>
                     </gmd:denominator>
                  </gmd:MD_RepresentativeFraction>
               </gmd:equivalentScale>
            </gmd:MD_Resolution>
         </gmd:spatialResolution>
         <gmd:language gco:nilReason="missing">
            <gco:CharacterString/>
         </gmd:language>
         <gmd:characterSet>
            <gmd:MD_CharacterSetCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_CharacterSetCode"
                                     codeListValue="utf8"/>
         </gmd:characterSet>
         <gmd:topicCategory>
            <gmd:MD_TopicCategoryCode>{$category}</gmd:MD_TopicCategoryCode>
         </gmd:topicCategory>
         <gmd:extent>
            <gmd:EX_Extent>
               <gmd:temporalElement>
                  <gmd:EX_TemporalExtent>
                     <gmd:extent>
                        <gml:TimePeriod gml:id="d21893e270a1052958"/>
                     </gmd:extent>
                  </gmd:EX_TemporalExtent>
               </gmd:temporalElement>
            </gmd:EX_Extent>
         </gmd:extent>
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
            </gmd:EX_Extent>
         </gmd:extent>
         <gmd:supplementalInformation>
            <gco:CharacterString>The maps are included in the SOIL and TERRAIN Database for Northern and Central EURASIA CD-ROM , which contains also the Soil map of North and Central Eurasia, reports and databases. Copies of this CD-Rom can be ordered from: Sales and Marketing Group FAO, Viale delle Terme di Caracalla, 00153 Rome, or by email to Publications-sales@fao.org. The terms and definitions used in the Physiographic database are based on the procedures manual for ?Global and National Soils and Terrain Digital Databases (SOTER)?, prepared by UNEP, ISSS, ISRIC and FAO and published by FAO as World Soil Resources Report #74 Rev1 (1995). Refinements were made in China as part of the preparation of a physiographic map for Asia, work carried out by G. van Lynden for FAO as part of the ASSOD project.</gco:CharacterString>
         </gmd:supplementalInformation>
      </gmd:MD_DataIdentification>
   </gmd:identificationInfo>
   <gmd:distributionInfo>
      <gmd:MD_Distribution>
         <gmd:transferOptions>
            <gmd:MD_DigitalTransferOptions>
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
                        <gmx:MimeFileType xmlns:gmx="http://www.isotc211.org/2005/gmx" type="{$filetype}">{$name}</gmx:MimeFileType>
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
            <gmd:DQ_Scope>
               <gmd:level>
                  <gmd:MD_ScopeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_ScopeCode"
                                    codeListValue="dataset"/>
               </gmd:level>
            </gmd:DQ_Scope>
         </gmd:scope>
         <gmd:report>
            <gmd:DQ_DomainConsistency>
               <gmd:result xmlns:gn="http://www.fao.org/geonetwork"
                           xmlns:gmx="http://www.isotc211.org/2005/gmx"
                           xmlns:srv="http://www.isotc211.org/2005/srv"
                           xmlns:xlink="http://www.w3.org/1999/xlink">
                  <gmd:DQ_ConformanceResult>
                     <gmd:specification>
                        <gmd:CI_Citation>
                           <gmd:title gco:nilReason="missing">
                              <gco:CharacterString/>
                           </gmd:title>
                           <gmd:date>
                              <gmd:CI_Date>
                                 <gmd:date>
                                    <gco:Date/>
                                 </gmd:date>
                                 <gmd:dateType>
                                    <gmd:CI_DateTypeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#CI_DateTypeCode"
                                                         codeListValue="publication"/>
                                 </gmd:dateType>
                              </gmd:CI_Date>
                           </gmd:date>
                        </gmd:CI_Citation>
                     </gmd:specification>
                     <gmd:explanation gco:nilReason="missing">
                        <gco:CharacterString/>
                     </gmd:explanation>
                  </gmd:DQ_ConformanceResult>
               </gmd:result>
            </gmd:DQ_DomainConsistency>
         </gmd:report>
         <gmd:lineage>
            <gmd:LI_Lineage>
               <gmd:statement>
                  <gco:CharacterString>{$map_url}</gco:CharacterString>
               </gmd:statement>
            </gmd:LI_Lineage>
         </gmd:lineage>
      </gmd:DQ_DataQuality>
  </gmd:dataQualityInfo>
</gmd:MD_Metadata>
