<?php

/**
 * INSTALL PEAR and package request2  http://pear.php.net/package/HTTP_Request2/
 */
//$path = '/usr/local/pear/share/pear';
//set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once 'HTTP/Request2.php';

/**
 * cswClient allows to request a OGC CSW 2.0.2 - ISO API service
 * Modifié par dpiquet (dpiquet@teicee.com)
 * Client adapté pour Geosource 3.0
 * Support de l'API REST de Geosource
 *
 * @package csw
 * @author lagarde pierre
 * @copyright BRGM
 * @name cswClient
 * @version 1.0.0
 */
class cswGeoClient {
    private $_cswAddress;
    private $_authentAddress;
    private $_cswLogin;
    private $_cswPassword;
    private $_bAuthent;
    private $_sessionID;

   
    private $_response;

    /**
     *
     * @param String $cswAddress address of the CSW URL
     * @param String $cswLogin login of the user to CSW-T
     * @param String $cswPassword  password of the user to CSW-T
     * @param String $authentAddress address of the login/logout address
     */
    
    function  __construct($cswAddress,$cswLogin=null,$cswPassword=null,$authentAddress=null) {
        $this->_cswAddress=$cswAddress;
        $this->_bAuthent=false;
        if (isset($cswLogin)) {
            $this->_cswLogin=$cswLogin;
            $this->_cswPassword=$cswPassword;
            $this->_authentAddress=$authentAddress;
            $this->_bAuthent=true;
        }
    }

    /**
     *
     * @return bool Request success / error
     */
    private function _callHTTPCSW($request) {

        try {
            $resp= $request->send();
            //if (200 == $resp->getStatus()) {
            if (200 == $resp->getStatus()) {
              $this->_response = $resp->getBody();
              $cookies = $resp->getCookies();
              foreach ($cookies as $cook) {                  
                  if ($cook['name']=='JSESSIONID') $this->_sessionID = $cook['value'];
              }
              return true;
            } else {
                $this->_response = $resp->getStatus() . ' ' .$resp->getReasonPhrase();
                return false;
            }
        } catch (HTTP_Request2_Exception $e) {
                $this->_response = 'Error: ' . $e->getMessage();
                return false;
        }

    }

	/**
	 * Geosource 3 always redirect client after login
	 * @return bool Request success / error
	 */
	private function _callHTTPAuth($request) {
		try {
			$resp = $request->send();
			if (301 == $resp->getStatus() or 302 == $resp->getStatus()) {
				//TODO: check for failed login
				$cookies = $resp->getCookies();
				foreach ($cookies as $cook) {
					if ($cook['name']=='JSESSIONID') $this->_sessionID = $cook['value'];
				}
				return true;
			}
			else {
				$this->_response = $resp->getStatus() . ' ' .$resp->getReasonPhrase();
				return false;
			}
		} catch (HTTP_REQUEST2_Exception $e) {
			$this->_response = 'Error: ' . $e->getMessage();
		}
	}

    /**
     *
     * @return bool authentication success or error
     */
    private function _authentication($request) {
        //only available for Geosource and Geonetwork
        //start by logout
        if ($this->_bAuthent) {
            //$req = new HTTP_Request2($this->_authentAddress.'/xml.user.logout', HTTP_Request2::METHOD_POST);
            $req = new HTTP_Request2($this->_authentAddress.'/j_spring_security_logout', HTTP_Request2::METHOD_POST);

            //if ($this->_callHTTPCSW($req)) {
            if ($this->_callHTTPAuth($req)) {
                //success so next step
                //start to login
                //$req = new HTTP_Request2( $this->_authentAddress.'/xml.user.login');
                $req = new HTTP_Request2( $this->_authentAddress.'/j_spring_security_check');
                $req->setMethod(HTTP_Request2::METHOD_POST)
                        ->setHeader("'Content-type': 'application/x-www-form-urlencoded', 'Accept': 'text/plain'")
                        ->addPostParameter('username', $this->_cswLogin)
                        ->addPostParameter('password', $this->_cswPassword);
                if ($this->_callHTTPAuth($req)) {
                    $request->addCookie('JSESSIONID', $this->_sessionID);
                    return true;
                }
            }
            return false;
        }
        return true;
    }

	/**
	 * JSON Request to Geosource
	 *
	 * @param string relative URI for the request
	 * @param request
	 * @return string JSON or false
	 */
	private function _jsonRequest($request) {
		return;
	}
	
	/**
	 * Load Theme Thesaurus from Geosource
	 *
	 */
	public function getGeoThemeThesaurus() {
		$request = new HTTP_Request2($this->_authentAddress.'/srv/fre/q');
		$request->setMethod(HTTP_Request2::METHOD_GET);
		$url = $request->getUrl();
		
		$url->setQueryVariables(array(
			'summaryOnly' => 'false',
			'thesaurusIdentifier' => 'external.theme.inspire-theme',
			'_content_type' => 'json',
		));
		
		//auth needed
		$this->_authentication($request);
		
		if($this->_callHTTPCSW($request)) {
			$thesaurus = json_decode($this->_response, true);
			return $thesaurus;
		}
		
		return false;
	}
	
	/**
	 * Recupérer l'id Geosource a partir de l'uuid
	 * TODO
	 */
	public function getGeoidFromUuid($uuid) {
		$request = new HTTP_Request2($this->_authentAddress.'/srv/eng/q');
		$request->setMethod(HTTP_Request2::METHOD_GET);
		$url = $request->getUrl();
		
		$url->setQueryVariables(array(
			'_content_type' => 'json',
			'fast' => 'index',
			'uuid' => $uuid,
		));
		
		// Auth needed
		$this->_authentication($request);
		
		if($this->_callHTTPCSW($request)) {
			$record = json_decode($this->_response, true);
			return $record['metadata']['geonet:info']['id'];
		}
		
		return false;
	}
	
	/**
	 * Assigner une catégorie sur une ressource Geosource
	 *
	 * @param int Geosource resource identifier
	 * @param array of categories to assign
	 * @return bool
	 */
	public function addMapCategory($geoid, $categories = array()) {
		$request = new HTTP_Request2($this->_authentAddress.'/srv/eng/md.category.update');
		$request->setMethod(HTTP_Request2::METHOD_GET);
		$url = $request->getUrl();
		
		$urlVars = array(
			'id' => $geoid,
		);
		
		foreach ($categories as $cat) {
			switch($cat) {
				case 'map':
					$urlVars['_1'] = 'on';
					break;
				case 'interactive':
					$urlVars['_3'] = 'on';
					break;
			}
		}
		
		$url->setQueryVariables($urlVars);
		
		// auth needed
		$this->_authentication($request);
		
		return $this->_callHTTPCSW($request);
	}
	
	public function publish($geoid) {
		$request = new HTTP_Request2($this->_authentAddress.'/srv/eng/md.publish');
		$request->setMethod(HTTP_Request2::METHOD_GET);
		$url = $request->getUrl();
		
		$urlVars = array(
			'ids' => $geoid,
		);
		
		$url->setQueryVariables($urlVars);
		
		// auth needed
		$this->_authentication($request);
		
		return $this->_callHTTPCSW($request);
	}
	
	public function unpublish($geoid) {
		$request = new HTTP_Request2($this->_authentAddress.'/srv/eng/md.unpublish');
		$request->setMethod(HTTP_Request2::METHOD_GET);
		$url = $request->getUrl();
		
		$urlVars = array(
			'ids' => $geoid,
		);
		
		$url->setQueryVariables($urlVars);
		
		// auth needed
		$this->_authentication($request);
		
		return $this->_callHTTPCSW($request);
	}
	
	/**
	 * retrieve csw repository capabilities
	 * @return XML content
	 */
	public function getCapabilities() {
		$getCapRequest = new HTTP_Request2($this->_cswAddress);
		$getCapRequest->setMethod(HTTP_Request2::METHOD_POST)
			->setHeader('Content-type: text/xml; charset=utf-8')
			->setBody('<?xml version="1.0"?>'.
					'<csw:GetCapabilities xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" service="CSW">'.
						'<ows:AcceptVersions xmlns:ows="http://www.opengis.net/ows">'.
							'<ows:Version>2.0.2</ows:Version>'.
						'</ows:AcceptVersions>'.
						'<ows:AcceptFormats xmlns:ows="http://www.opengis.net/ows">'.
							'<ows:OutputFormat>application/xml</ows:OutputFormat>'.
						'</ows:AcceptFormats>'.
					'</csw:GetCapabilities>')
			;
		$this->_authentication($getCapRequest);
		if($this->_callHTTPCSW($getCapRequest)) {
			return $this->_response;
		}
		else {
			throw new Exception($this->_response);
		}
	}

    /**
     * retrieve a specific metadata with UUID in GeoNetwork / Geosource
     * @param String $id of the metadata
     * @return XML content
     */
    public function getRecordById($id) {
        $getRecodByIDRequest = new HTTP_Request2($this->_cswAddress);
        $getRecodByIDRequest->setMethod(HTTP_Request2::METHOD_POST)
              ->setHeader('Content-type: text/xml; charset=utf-8')
              ->setBody("<?xml version='1.0'?>".
                           "<csw:GetRecordById xmlns:csw='http://www.opengis.net/cat/csw/2.0.2' service='CSW' version='2.0.2' outputSchema='http://www.isotc211.org/2005/gmd'>".
                           "<csw:ElementSetName>full</csw:ElementSetName>".
                           "<csw:Id>".$id."</csw:Id>".
                           "</csw:GetRecordById>");
            //authentication if needed
        if (!$this->_authentication($getRecodByIDRequest)) throw new Exception($this->_response);
        if ($this->_callHTTPCSW($getRecodByIDRequest)) {
                $getRecodByIDRequest=null;
                return $this->_response;
        }
        else {
            $getRecodByIDRequest=null;
            throw new Exception($this->_response);
        }
        
    }
    
        /**
     * retrieve a specific metadata with UUID in GeoNetwork / Geosource
     * @param String $id of the metadata
     * @return XML content
     */
    public function getRecordsWithBBOX($propertyName, $literal, $xmin, $ymin, $xmax, $ymax, $returnable, $nb_records) {
        $getRecodsRequest = new HTTP_Request2($this->_cswAddress);
        $getRecodsRequest->setMethod(HTTP_Request2::METHOD_POST)
          ->setHeader('Content-type: text/xml; charset=utf-8')
		      ->setBody("<?xml version='1.0'?>".
                           "<csw:GetRecords xmlns:csw='http://www.opengis.net/cat/csw/2.0.2' service='CSW' resultType='results' maxRecords='" . $nb_records . "' version='2.0.2'>".
                           "<csw:Query typeNames='csw:Record'>".
                           "<csw:ElementSetName typeNames='csw:Record'>" . $returnable . "</csw:ElementSetName>".
                           "<csw:Constraint version='1.1.0'>".
                           "<Filter xmlns='http://www.opengis.net/ogc' xmlns:gml='http://www.opengis.net/gml'>".
                           "<And>".
                           "<PropertyIsLike wildCard='%' singleChar='_' escape='\\'>".
                           "<PropertyName>" . $propertyName . "</PropertyName>".
                           "<Literal>" . $literal . "</Literal>".
                           "</PropertyIsLike>".
                           "<Intersects><PropertyName>BoundingBox</PropertyName>".
                           "<Envelope><lowerCorner>" . $xmax . " " . $ymin ."</lowerCorner>".
                           "<upperCorner>" . $xmin . " " . $ymax . "</upperCorner>".
                           "</Envelope></Intersects>".
                           "</And>".
                           "</Filter></csw:Constraint></csw:Query></csw:GetRecords>");
            //authentication if needed
            //
			
        if (!$this->_authentication($getRecodsRequest)) throw new Exception($this->_response);
        if ($this->_callHTTPCSW($getRecodsRequest)) {
                $getRecodsRequest=null;
                return $this->_response;
        }
        else {
            $getRecodsRequest=null;
            throw new Exception($this->_response);
        }
    }
    
    public function getRecords($propertyName, $literal, $returnable, $nb_records) {
        $getRecodsRequest = new HTTP_Request2($this->_cswAddress);
        $getRecodsRequest->setMethod(HTTP_Request2::METHOD_POST)
          ->setHeader('Content-type: text/xml; charset=utf-8')
		      ->setBody("<?xml version='1.0'?>".
                           "<csw:GetRecords xmlns:csw='http://www.opengis.net/cat/csw/2.0.2' service='CSW' resultType='results' maxRecords='" . $nb_records . "' version='2.0.2'>".
                           "<csw:Query typeNames='csw:Record'>".
                           "<csw:ElementSetName typeNames='csw:Record'>" . $returnable . "</csw:ElementSetName>".
                           "<csw:Constraint version='1.1.0'>".
                           "<Filter xmlns='http://www.opengis.net/ogc' xmlns:gml='http://www.opengis.net/gml'>".
                           "<PropertyIsLike wildCard='%' singleChar='_' escape='\\'>".
                           "<PropertyName>" . $propertyName . "</PropertyName>".
                           "<Literal>" . $literal . "</Literal>".
                           "</PropertyIsLike>".
                           "</Filter></csw:Constraint></csw:Query></csw:GetRecords>");
            //authentication if needed
            //
        if (!$this->_authentication($getRecodsRequest)) throw new Exception($this->_response);
        if ($this->_callHTTPCSW($getRecodsRequest)) {
                $getRecodsRequest=null;
                return $this->_response;
        }
        else {
            $getRecodsRequest=null;
            throw new Exception($this->_response);
        }
    }
    
    public function getRecordsByTopic($propertyName, $topic, $query, $xmin, $ymin, $xmax, $ymax, $returnable, $nb_records) {
        $getRecodsRequest = new HTTP_Request2($this->_cswAddress);
        $getRecodsRequest->setMethod(HTTP_Request2::METHOD_POST)
          ->setHeader('Content-type: text/xml; charset=utf-8')
		      ->setBody("<?xml version='1.0'?>".
                           "<csw:GetRecords xmlns:csw='http://www.opengis.net/cat/csw/2.0.2' service='CSW' resultType='results' maxRecords='" . $nb_records . "' version='2.0.2'>".
                           "<csw:Query typeNames='csw:Record'>".
                           "<csw:ElementSetName typeNames='csw:Record'>" . $returnable . "</csw:ElementSetName>".
                           "<csw:Constraint version='1.1.0'>".
                           "<Filter xmlns='http://www.opengis.net/ogc' xmlns:gml='http://www.opengis.net/gml'>".
                           "<And>".
                           "<PropertyIsEqualTo>".
                           "<PropertyName>" . $propertyName . "</PropertyName>".
                           "<Literal>" . $topic . "</Literal>".
                           "</PropertyIsEqualTo>".
                           "<PropertyIsLike wildCard='%' singleChar='_' escape='\\'>".
                           "<PropertyName>AnyText</PropertyName>".
                           "<Literal>" . $query . "</Literal>".
                           "</PropertyIsLike>".
						   "</And>".  
                           "<Intersects><PropertyName>BoundingBox</PropertyName>".
                           "<Envelope><lowerCorner>" . $xmax . " " . $ymin ."</lowerCorner>".
                           "<upperCorner>" . $xmin . " " . $ymax . "</upperCorner>".
                           "</Envelope></Intersects>".
						   
                           "</Filter></csw:Constraint></csw:Query></csw:GetRecords>");
            //authentication if needed
            //
        if (!$this->_authentication($getRecodsRequest)) throw new Exception($this->_response);
        if ($this->_callHTTPCSW($getRecodsRequest)) {
                $getRecodsRequest=null;
                return $this->_response;
        }
        else {
            $getRecodsRequest=null;
            throw new Exception($this->_response);
        }
	}

	/**
	 * Get records modified since date
	 *
	 * @param DateTime 
	 * @return string 
	 *
	 * TODO: multiple requetes au lieu d'une grosse (maxRecords)
	 */
	public function getRecordsModifiedSince(DateTime $since, $startRecord = 1, $qtty = 10000) {
		
		$now = new DateTime('now');
		
		$getRecodsRequest = new HTTP_Request2($this->_cswAddress);
		$getRecodsRequest->setMethod(HTTP_Request2::METHOD_POST)
			->setHeader('Content-type: text/xml; charset=utf-8')
			->setBody('<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?> '.
				'<csw:GetRecords xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" '.
				'xmlns:ogc="http://www.opengis.net/ogc" service="CSW" version="2.0.2" '.
				'resultType="results" startPosition="'.$startRecord.'" maxRecords="'.$qtty.'" '.
				'outputFormat="application/xml" '.
				'outputSchema="http://www.opengis.net/cat/csw/2.0.2" '.
				'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
				'xsi:schemaLocation="http://www.opengis.net/cat/csw/2.0.2 '.
				'http://schemas.opengis.net/csw/2.0.2/CSW-discovery.xsd"> '.
 				'<csw:Query typeNames="csw:Record"> '.
				'	<csw:ElementSetName>brief</csw:ElementSetName> '.
				'	  <csw:Constraint version="1.1.0"> '.
				'		 <ogc:Filter> '.
				'		   <ogc:PropertyIsBetween> '.
				'			 <ogc:PropertyName>dc:modified</ogc:PropertyName> '.
				'			 <ogc:LowerBoundary> '.
				'			   <ogc:Literal>'.$since->format('Y-m-d').'</ogc:Literal> '.
				'			 </ogc:LowerBoundary> '.
				'			 <ogc:UpperBoundary> '.
				'			   <ogc:Literal>'.$now->format('Y-m-d').'</ogc:Literal> '.
				'			 </ogc:UpperBoundary> '.
				'		   </ogc:PropertyIsBetween> '.
				'		 </ogc:Filter> '.
				'	   </csw:Constraint> '.
				'	 </csw:Query> '.
				'</csw:GetRecords>');
        //authentication if needed
        //
        if (!$this->_authentication($getRecodsRequest)) throw new Exception($this->_response);
        if ($this->_callHTTPCSW($getRecodsRequest)) {
                $getRecodsRequest=null;
                return $this->_response;
        }
        else {
            $getRecodsRequest=null;
            throw new Exception($this->_response);
        }
	}

/*
  http://localhost:8080/geonetwork/srv/en/csw?request=GetRecords
  http://geocatalog.bibl.ulaval.ca/geonetwork/srv/fr/csw?request=GetRecords&service=CSW&version=2.0.2
  &namespace=xmlns%28csw%3Dhttp%3A%2F%2Fwww.opengis.net%2Fcat%2Fcsw%2F2.0.2%29%2Cxmlns%28gmd%3Dhttp%3A%2F%2Fwww.isotc211.org%2F2005%2Fgmd%29
  &constraint=AnyText+like+%25batiment%25
  &constraintLanguage=CQL_TEXT
  &constraint_language_version=1.1.0
  &typeNames=csw%3ARecord
  http://geocatalog.bibl.ulaval.ca/geonetwork/srv/fr/csw?request=GetRecords&service=CSW&version=2.0.2&namespace=xmlns(csw=http://www.opengis.net/cat/csw/2.0.2),xmlns(gmd=http://www.isotc211.org/2005/gmd)&constraint=AnyText like %africa%&constraintLanguage=CQL_TEXT&constraint_language_version=1.1.0&typeNames=csw:Record
*/
    
    
    /**
     *
     * @return Number of metadata in the csw server
     */
    public function getCountRecords() {
        $getCountRecordsRequest = new HTTP_Request2($this->_cswAddress);
        $getCountRecordsRequest->setMethod(HTTP_Request2::METHOD_POST)
                      ->setHeader('Content-type: text/xml; charset=utf-8')
		      ->setBody("<?xml version='1.0'?>".
                                "<csw:GetRecords xmlns:csw='http://www.opengis.net/cat/csw/2.0.2' service='CSW' version='2.0.2' resultType='hits'>".
                                "<csw:Query typeNames='csw:Record'>".
                                "<csw:Constraint version='1.1.0'>".
                                "    <Filter xmlns='http://www.opengis.net/ogc' xmlns:gml='http://www.opengis.net/gml'/>".
                                "</csw:Constraint>".
                                "</csw:Query>".
                                "</csw:GetRecords>");
            //authentication if needed
        if (!$this->_authentication($getCountRecordsRequest)) throw new Exception($this->_response);
        if ($this->_callHTTPCSW($getCountRecordsRequest)) {
                $docXml= new DOMDocument();
                if ($docXml->loadXML($this->_response)) {                    
                    $xp = new DOMXPath($docXml);
                    $xpathString="//@numberOfRecordsMatched";
                    $nodes = $xp->query($xpathString);
                    if ($nodes->length==1)
                        return $nodes->item(0)->textContent;
                    else
                        return 0;
                }
                else {
                    throw new Exception($this->_response);
                }
                
        }
        else
            throw new Exception($this->_response);

    }
    /**
	 * Insert a new metadata in the csw server
	 * TODO: better xml header removal
	 *
     * @param DOMDocument $xmlISO19139 content to add
     * @return number of insered metadata
     */
    public function insertMetadata($xmlISO19139) {
        $insertMetadataRequest = new HTTP_Request2($this->_cswAddress);
        $insertMetadataRequest->setMethod(HTTP_Request2::METHOD_POST)
                      ->setHeader('Content-type: text/xml; charset=utf-8')
		      ->setBody("<?xml version='1.0'?>".
                           "<csw:Transaction service='CSW' version='2.0.2' xmlns:csw='http://www.opengis.net/cat/csw/2.0.2' xmlns:ogc='http://www.opengis.net/ogc' xmlns:apiso='http://www.opengis.net/cat/csw/apiso/1.0'>".
                           "<csw:Insert>".str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$xmlISO19139->saveXML()).
		           "</csw:Insert>".
                           "</csw:Transaction>");
        //authentication is needed !!
        if (!$this->_authentication($insertMetadataRequest)) throw new Exception("authentication mandatory");
        if ($this->_callHTTPCSW($insertMetadataRequest)) {
                $docXml= new DOMDocument();
                if ($docXml->loadXML($this->_response)) {
                    $xp = new DOMXPath($docXml);
                    $xpathString="//csw:BriefRecord";
                    $nodes = $xp->query($xpathString);
					if ($nodes->length > 0) {
						$uuids = array();
						foreach($nodes as $node) {
							$uuids[] = trim(preg_replace('/\s+/', '', $node->textContent));
						}
						return $uuids;
					}
                    else
                        return false;
                }
                else {
                    throw new Exception($this->_response);
                }
        }
        else
            throw new Exception($this->_response);


    }

    /**
     * update a  metadata in the csw server
     * @param DOMDocument $xmlISO19139 content to add
     * @return number of updated metadata
     */
    public function updateMetadata($xmlISO19139) {
        
        //first, find the uuid of the metadata !
        
        $nFI=$xmlISO19139->getElementsByTagName('fileIdentifier');
        if ($nFI->length==1) {               
                $uuid = $nFI->item(0)->childNodes->item(1)->nodeValue;
        }
        else
            throw new Exception("No fileIdentifier found");
       
        $updateMetadataRequest = new HTTP_Request2($this->_cswAddress);
        $updateMetadataRequest->setMethod(HTTP_Request2::METHOD_POST)
                      ->setHeader('Content-type: text/xml; charset=utf-8')
		      ->setBody("<?xml version='1.0'?>".
                           "<csw:Transaction service='CSW' version='2.0.2' xmlns:csw='http://www.opengis.net/cat/csw/2.0.2' xmlns:ogc='http://www.opengis.net/ogc' xmlns:apiso='http://www.opengis.net/cat/csw/apiso/1.0'>".
                           "<csw:Update>".str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$xmlISO19139->saveXML()).
                           "<csw:Constraint version='1.0.0'>".
                           "<Filter xmlns='http://www.opengis.net/ogc' xmlns:gml='http://www.opengis.net/gml'>".
                           "<PropertyIsLike wildCard='%' singleChar='_' escapeChar='\'>".
                           "    <PropertyName>apiso:identifier</PropertyName>".
                           "    <Literal>".$uuid."</Literal>".
                           "</PropertyIsLike>".
                           "</Filter>".
                           "</csw:Constraint>".
		           "</csw:Update>".
                           "</csw:Transaction>");
        //authentication is needed !!
        
        if (!$this->_authentication($updateMetadataRequest)) throw new Exception("authentication mandatory");
        
        if ($this->_callHTTPCSW($updateMetadataRequest)) {
                $docXml= new DOMDocument();
               
                if ($docXml->loadXML($this->_response)) {
                    $xp = new DOMXPath($docXml);
                    $xpathString="//csw:totalUpdated";
                    $nodes = $xp->query($xpathString);
                    if ($nodes->length==1)
                        return $nodes->item(0)->textContent;
                    else
                        return 0;
                }
                else {
                    throw new Exception($this->_response);
                }
        }
        else
            throw new Exception($this->_response);


    }

	/**
     * update full record in the csw server
     * @param DOMDocument $xmlISO19139 content to add
     * @return number of updated metadata
     */
    public function updateFullRecord($xmlISO19139) {
        
        //first, find the uuid of the metadata !
        
        $nFI=$xmlISO19139->getElementsByTagName('fileIdentifier');
        if ($nFI->length==1) {               
                $uuid = $nFI->item(0)->childNodes->item(1)->nodeValue;
        }
        else
            throw new Exception("No fileIdentifier found");
       
        $updateMetadataRequest = new HTTP_Request2($this->_cswAddress);
        $updateMetadataRequest->setMethod(HTTP_Request2::METHOD_POST)
			->setHeader('Content-type: text/xml; charset=utf-8')
			->setBody("<?xml version='1.0'?>".
					"<csw:Transaction service='CSW' version='2.0.2' xmlns:csw='http://www.opengis.net/cat/csw/2.0.2' xmlns:ogc='http://www.opengis.net/ogc' xmlns:apiso='http://www.opengis.net/cat/csw/apiso/1.0'>".
					"	<csw:Update>".
							str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$xmlISO19139->saveXML()).
					"	</csw:Update>".
					"</csw:Transaction>");
        //authentication is needed !!
        
        if (!$this->_authentication($updateMetadataRequest)) throw new Exception("authentication mandatory");
        
        if ($this->_callHTTPCSW($updateMetadataRequest)) {
                $docXml= new DOMDocument();
               
                if ($docXml->loadXML($this->_response)) {
                    $xp = new DOMXPath($docXml);
                    $xpathString="//csw:totalUpdated";
                    $nodes = $xp->query($xpathString);
                    if ($nodes->length==1)
                        return $nodes->item(0)->textContent;
                    else
                        return 0;
                }
                else {
                    throw new Exception($this->_response);
                }
        }
        else
            throw new Exception($this->_response);


    }



    /**
     * deleted a  metadata in the csw server
     * @param DOMDocument $xmlISO19139 content to add
     * @return number of deleted metadata
     */
    public function deleteMetadata($xmlISO19139) {
       //first, find the uuid of the metadata !

        $nFI=$xmlISO19139->getElementsByTagName('fileIdentifier');
        if ($nFI->length==1) {
                $uuid = $nFI->item(0)->childNodes->item(1)->nodeValue;
                return $this->deleteMetadataFromUuid($uuid);
        }
        else
            throw new Exception("No fileIdentifier found");

    }

    /**
     * delete a  metadata in the csw server
     * @param String $uuid id of the metadata
     * @return number of deleted metadata
     */
    public function deleteMetadataFromUuid($uuid) {

       
        $deleteMetadataRequest = new HTTP_Request2($this->_cswAddress);
        $deleteMetadataRequest->setMethod(HTTP_Request2::METHOD_POST)
                      ->setHeader('Content-type: text/xml; charset=utf-8')
		      ->setBody("<?xml version='1.0'?>".
                           "<csw:Transaction service='CSW' version='2.0.2' xmlns:csw='http://www.opengis.net/cat/csw/2.0.2' xmlns:ogc='http://www.opengis.net/ogc' xmlns:apiso='http://www.opengis.net/cat/csw/apiso/1.0'>".
                           "<csw:Delete>".
                           "<csw:Constraint version='1.0.0'>".
                           "<Filter xmlns='http://www.opengis.net/ogc' xmlns:gml='http://www.opengis.net/gml'>".
                           "<PropertyIsLike wildCard='%' singleChar='_' escapeChar='\'>".
                           "    <PropertyName>apiso:identifier</PropertyName>".
                           "    <Literal>".$uuid."</Literal>".
                           "</PropertyIsLike>".
                           "</Filter>".
                           "</csw:Constraint>".
		           "</csw:Delete>".
                           "</csw:Transaction>");
        //authentication is needed !!

        if (!$this->_authentication($deleteMetadataRequest)) throw new Exception("authentication mandatory");

        if ($this->_callHTTPCSW($deleteMetadataRequest)) {
                $docXml= new DOMDocument();
                if ($docXml->loadXML($this->_response)) {
                    $xp = new DOMXPath($docXml);
                    $xpathString="//csw:totalDeleted";
                    $nodes = $xp->query($xpathString);
                    if ($nodes->length==1)
                        return $nodes->item(0)->textContent;
                    else
                        return 0;
                }
                else {
                    throw new Exception($this->_response);
                }
        }
        else
            throw new Exception($this->_response);


    }

}

//TEST
function testCswGeoClientClass() {

echo "allo";

    $cswClient = new cswGeoClient('http://geosourcedemo.brgm.fr/geosourcedemo/srv/fr/csw', 'editeur', 'editeur', 'http://geosourcedemo.brgm.fr/geosourcedemo/srv/fr');
try {

    

    $res=$cswClient->getRecordById('5a258fa6-103f-427f-88ab-28036db17c11');

    $xml=new DOMDocument();
    //cswLog::logInfo($res);
    $xml->loadXML($res);
    $md_Medatadata=$xml->getElementsByTagName('MD_Metadata')->item(0);
    $xmldoc=new DOMDocument();
    $xmldoc->loadXML($xml->saveXML($md_Medatadata));
    $cswClient->updateMetadata($xmldoc);
    $cswClient->deleteMetadata($xmldoc);


}
catch (Exception $e) {
     echo $e->getMessage();
}
}

?>
