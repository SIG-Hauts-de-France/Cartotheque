<?xml version="1.0" encoding="UTF-8"?>
<MD_Metadata xmlns="http://www.isotc211.org/2005/gmd" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gml="http://www.opengis.net/gml" xmlns:xlink="http://www.w3.org/1999/xlink">
    <fileIdentifier>
        <gco:CharacterString>{$uuid}</gco:CharacterString>
            </fileIdentifier>
            <language>
                <gco:CharacterString>{$language_code}</gco:CharacterString>
            </language>
            <hierarchyLevel>
                <MD_ScopeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_ScopeCode" codeListValue="dataset"/>
            </hierarchyLevel>
            <hierarchyLevelName>
                <gco:CharacterString>dataset</gco:CharacterString>
            </hierarchyLevelName>
            <contact>
                <CI_ResponsibleParty>
                    <individualName>
                        <gco:CharacterString>{$author_name}</gco:CharacterString>
                    </individualName>
                    <organisationName>
                        <gco:CharacterString>{$author_name}</gco:CharacterString>
                    </organisationName>
                    <contactInfo>
                        <CI_Contact>
                            <address>
                                <CI_Address>
                                    <deliveryPoint>
                                        <gco:CharacterString>5130 State Office Building</gco:CharacterString>
                                    </deliveryPoint>
                                    <city>
                                        <gco:CharacterString>Salt Lake City</gco:CharacterString>
                                    </city>
                                    <postalCode>
                                        <gco:CharacterString>84114</gco:CharacterString>
                                    </postalCode>
                                    <country>
                                        <gco:CharacterString>USA</gco:CharacterString>
                                    </country>
                                    <electronicMailAddress>
                                        <gco:CharacterString>{$author_email}</gco:CharacterString>
                                    </electronicMailAddress>
                                </CI_Address>
                            </address>
                        </CI_Contact>
                    </contactInfo>
                    <role>
                        <CI_RoleCode codeList="http://www.isotc211.org/2005/resources/codeList.xml#CI_RoleCode" codeListValue="pointOfContact"/>
                    </role>
                </CI_ResponsibleParty>
            </contact>
    <!-- Date the metadata was created -->
            <dateStamp>
                <gco:Date>{$creation_date}</gco:Date>
            </dateStamp>
            <metadataStandardName>
                <gco:CharacterString>ISO 19115:2003/19139</gco:CharacterString>
            </metadataStandardName>
            <metadataStandardVersion>
                <gco:CharacterString>1.0</gco:CharacterString>
            </metadataStandardVersion>
    <!-- REFERENCE SYSTEM INFORMATION -->
            <referenceSystemInfo>
                <MD_ReferenceSystem>
                    <referenceSystemIdentifier>
                        <RS_Identifier>
                            <code>
                                <gco:CharacterString>{$map_number}</gco:CharacterString>
                            </code>
                            <codeSpace>
                                <gco:CharacterString>EPSG</gco:CharacterString>
                            </codeSpace>
                        </RS_Identifier>
                    </referenceSystemIdentifier>
                </MD_ReferenceSystem>
            </referenceSystemInfo>
    <!-- END REFERENCE SYSTEM INFORMATION -->
    <!-- IDENTIFICATION INFORMATION -->
            <identificationInfo>
                <MD_DataIdentification>
                    <citation>
                        <CI_Citation>
                            <title>
                                <gco:CharacterString>{$map_title}</gco:CharacterString>
                            </title>
                            <date>
                                <CI_Date>
                                    <date>
                                        <gco:Date>{$creation_date}</gco:Date>
                                    </date>
                                    <dateType>
                                        <CI_DateTypeCode codeList="http://www.isotc211.org/2005/resources/codeList.xml#CI_DateTypeCode" codeListValue="publication"/>
                                    </dateType>
                                </CI_Date>
                            </date>
                            <edition>
                                <gco:CharacterString>None</gco:CharacterString>
                            </edition>
                            <identifier>
                                <MD_Identifier>
                                    <code>
                                        <gco:CharacterString>{$uuid}</gco:CharacterString>
                                    </code>
                                </MD_Identifier>
                            </identifier>
                            <series>
                                <CI_Series>
                                    <name>
                                        <gco:CharacterString>State of Utah SGID</gco:CharacterString>
                                    </name>
                                    <issueIdentification>
                                        <gco:CharacterString>None</gco:CharacterString>
                                    </issueIdentification>
                                </CI_Series>
                            </series>
                            <!-- not mapped
                            <otherCitationDetails>
                                <gco:CharacterString>Digital data with no scale</gco:CharacterString>
                            </otherCitationDetails>
                             -->
                        </CI_Citation>
                    </citation>
                    <abstract>
                        <gco:CharacterString>{$map_abstract}</gco:CharacterString>
                    </abstract>
                    <purpose>
                        <gco:CharacterString> (TODO) </gco:CharacterString>
                    </purpose>
                    <status>
                        <MD_ProgressCode codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_ProgressCode" codeListValue="completed"/>
                    </status>
                    <pointOfContact>
                        <CI_ResponsibleParty>
                            <individualName>
                                <gco:CharacterString>{$author_name}</gco:CharacterString>
                            </individualName>
                            <organisationName>
                                <gco:CharacterString> (TODO) </gco:CharacterString>
                            </organisationName>
                            <positionName>
                                <gco:CharacterString> (TODO) </gco:CharacterString>
                            </positionName>
                            <contactInfo>
                                <CI_Contact>
                                    <phone>
                                        <CI_Telephone>
                                            <voice>
                                                <gco:CharacterString>(801) 537-9201</gco:CharacterString>
                                            </voice>
                                            <facsimile>
                                                <gco:CharacterString>(801) 538-3317</gco:CharacterString>
                                            </facsimile>
                                        </CI_Telephone>
                                    </phone>
                                    <address>
                                        <CI_Address>
                                            <deliveryPoint>
                                                <gco:CharacterString>5130 State Office Building</gco:CharacterString>
                                            </deliveryPoint>
                                            <city>
                                                <gco:CharacterString>Salt Lake City</gco:CharacterString>
                                            </city>
                                            <administrativeArea>
                                                <gco:CharacterString>UT</gco:CharacterString>
                                            </administrativeArea>
                                            <postalCode>
                                                <gco:CharacterString>84114</gco:CharacterString>
                                            </postalCode>
                                            <country>
                                                <gco:CharacterString>USA</gco:CharacterString>
                                            </country>
                                            <electronicMailAddress>
                                                <gco:CharacterString>{$author_email}</gco:CharacterString>
                                            </electronicMailAddress>
                                        </CI_Address>
                                    </address>
                                </CI_Contact>
                            </contactInfo>
                            <role>
                                <CI_RoleCode codeList="http://www.isotc211.org/2005/resources/codeList.xml#CI_RoleCode" codeListValue="pointOfContact"/>
                            </role>
                        </CI_ResponsibleParty>
                    </pointOfContact>
            <!-- ***** KEYWORDS ***** -->
            <!-- ***** DISCIPLINE ***** -->
[$keywords]
                    <descriptiveKeywords>
                        <MD_Keywords>
                            <keyword>
                                <gco:CharacterString>{$value}</gco:CharacterString>
                            </keyword>
                            <type>
                                <MD_KeywordTypeCode codeList="http://metadata.dgiwg.org/codelistRegistry?MD_KeywordTypeCode" codeListValue="theme"/>
                            </type>
                        </MD_Keywords>
                    </descriptiveKeywords>
[/$keywords]
                    <spatialRepresentationType>
                        <MD_SpatialRepresentationTypeCode codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_SpatialRepresentationTypeCode" codeListValue="vector"/>
                    </spatialRepresentationType>
                    <language>
                        <gco:CharacterString>{language_code}</gco:CharacterString>
                    </language>
                    <topicCategory>
                        <MD_TopicCategoryCode>geoscientificInformation</MD_TopicCategoryCode>
                    </topicCategory>
            <!-- Method one for indicating data coverage area -->
            <!-- Use EX_GeographicBoundingBoxType to describe -->
            <!-- the data coverage area using approximate coordinates -->
            <!-- THIS METHOD IS MANDATORY IF THE HIERARCHY  -->
            <!-- LEVEL OF THIS METADATA IS "DATASET" -->
                    <extent>
                        <EX_Extent>
                            <geographicElement>
                                <EX_GeographicBoundingBox>
                                    <westBoundLongitude>
                                        <gco:Decimal>{$bbox.west}</gco:Decimal>
                                    </westBoundLongitude>
                                    <eastBoundLongitude>
                                        <gco:Decimal>{$bbox.east}</gco:Decimal>
                                    </eastBoundLongitude>
                                    <southBoundLatitude>
                                        <gco:Decimal>{$bbox.south}</gco:Decimal>
                                    </southBoundLatitude>
                                    <northBoundLatitude>
                                        <gco:Decimal>{$bbox.north}</gco:Decimal>
                                    </northBoundLatitude>
                                </EX_GeographicBoundingBox>
                            </geographicElement>
                        </EX_Extent>
                    </extent>
                    <extent>
                        <EX_Extent>
                            <temporalElement>
                                <EX_TemporalExtent>
                                    <extent>
                                    <!--
                                        <gml:TimePeriod gml:id="ID00001">
										TODO
                                         -->
                                        <gml:TimePeriod>
                                            <gml:beginPosition>2000-01-01</gml:beginPosition>
                                            <gml:endPosition>2007-05-07</gml:endPosition>
                                        </gml:TimePeriod>
                                    </extent>
                                </EX_TemporalExtent>
                            </temporalElement>
                        </EX_Extent>
                    </extent>
                    <extent>
                        <EX_Extent>
                            <geographicElement>
                                <EX_GeographicDescription>
                                    <geographicIdentifier>
                                        <RS_Identifier>
                                            <code>
                                                <gco:CharacterString>Utah</gco:CharacterString>
                                            </code>
                                        </RS_Identifier>
                                    </geographicIdentifier>
                                </EX_GeographicDescription>
                            </geographicElement>
                        </EX_Extent>
                    </extent>
                    <supplementalInformation>
                        <gco:CharacterString>Procedures_Used: ( TODO )</gco:CharacterString>
                    </supplementalInformation>
                </MD_DataIdentification>
            </identificationInfo>
    <!-- DATA QUALITY AND LINEAGE -->
            <dataQualityInfo>
                <DQ_DataQuality>
                    <scope>
                        <DQ_Scope>
                            <level>
                                <MD_ScopeCode codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_ScopeCode" codeListValue="dataset"/>
                            </level>
                        </DQ_Scope>
                    </scope>
                    <lineage>
                        <LI_Lineage>
                            <source>
                                <LI_Source>
                                    <scaleDenominator>
                                        <MD_RepresentativeFraction>
                                            <denominator>
                                                <gco:Integer>{$map_resolution}</gco:Integer>
                                            </denominator>
                                        </MD_RepresentativeFraction>
                                    </scaleDenominator>
                                    <sourceStep>
                                        <LI_ProcessStep>
                                            <description>
                                                <gco:CharacterString> (TODO) </gco:CharacterString>
                                            </description>
                                        </LI_ProcessStep>
                                    </sourceStep>
                                </LI_Source>
                            </source>
                        </LI_Lineage>
                    </lineage>
                </DQ_DataQuality>
            </dataQualityInfo>
    <!-- END OF DATA QUALITY & LINEAGE -->
        </MD_Metadata>
