<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * XML helper functions.
 * Used by views/api/api_render, and views/rss.
 */
require_once BASEPATH.'helpers/xml_helper.php';

/** Strip named entities except for the 5 allowed in XML. Strip eg. &nbsp; &copy;.
 *  Preserve numeric entities which are allowed, eg. &#39; &#45;.
 */
function xml_safe($input) {
    if (!function_exists('mb_detect_encoding')) { echo "Help, we need 'mb_detect_encoding'!"; exit; }

	$xml_safe = array('&lt;', '&gt;', '&amp;', '&apos;', '&quot;');
    $placeholders = array('#LT#', '#GT#', '#AMP#', '#APOS#', '#QUOT#');

	// Everything should be UTF-8 already - in case it isn't, safely encode!
	$output = 'UTF-8'==mb_detect_encoding($input, 'UTF-8') ? $input : utf8_encode($input);

    $output = str_replace($xml_safe, $placeholders, $output);
    $output = html_entity_decode($output, ENT_NOQUOTES, 'UTF-8');
    $output = preg_replace('/&[^#]\w*?;/', '', $output);
    $output = str_replace($placeholders, $xml_safe, $output);

    return $output;
}

/** Strip unsafe attributes, eg. 'style' from RSS entry descriptions (Bug #183).
 *  See, http://validator.w3.org/feed/docs/warning/DangerousStyleAttr.html
 */
function xml_feed_html_safe($input) {
    return preg_replace('#style="[^"]+">#', 'style="cursor:auto"><!--NO CSS-->', $input);
}


class ArrayToXML {
    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @author djdykes, 8 August 2007
     * @author wwwzealdcom, 30 August 2009
     * @author The Open University.
     * @see  http://snipplr.com/view.php?codeview&id=3491
     * @note Uses SimpleXML.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function toXML($data, $rootNodeName = 'ResultSet', &$xml=null, $rootParams=null, $pretty=FALSE) {

        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if ( ini_get('zend.ze1_compatibility_mode') == 1 ) ini_set ( 'zend.ze1_compatibility_mode', 0 );
        if ( is_null( $xml ) ) $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8' standalone='yes'?><$rootNodeName $rootParams/>");#NDF, ("") does not work?

        // loop through the data passed in.
        foreach( $data as $key => $value ) {

            $numeric = false; #NDF, added.
            // no numeric keys in our xml please!
            if ( is_numeric( $key ) ) {
                $numeric = true;
                $key = $rootNodeName;
            }

            // delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);
            $key = preg_match('#^(status|total_)#', $key) ? $key : rtrim($key, 's'); #NDF, deal with some plurals. 0===strpos($key, 'total_')

            // if there is another array found recrusively call this function
            if ( is_array( $value )
              || is_object( $value )) {#NDF, Or object?
                $node = ArrayToXML::isAssoc( $value ) || $numeric ? $xml->addChild( $key ) : $xml;

                // recrusive call.
                if ( $numeric ) $key = 'anon';
                ArrayToXML::toXml( $value, $key, $node );
            } else {

                // add single node.
                $value = xml_safe($value);  #NDF.
                $xml->addChild( $key, $value );
            }
        }

        // pass back as XML
        if (! $pretty) {
            return $xml->asXML();
        }
    // if you want the XML to be formatted, use the below instead to return the XML
        $doc = new DOMDocument('1.0');
        $doc->preserveWhiteSpace = false;
        $doc->loadXML( $xml->asXML() );
        $doc->formatOutput = true;
        return $doc->saveXML();
    }


    /**
     * Convert an XML document to a multi dimensional array
     * Pass in an XML document (or SimpleXMLElement object) and this recrusively loops through and builds a representative array
     *
     * @param string $xml - XML document - can optionally be a SimpleXMLElement object
     * @return array ARRAY
     */
    public static function toArray( $xml ) {
        if ( is_string( $xml ) ) $xml = new SimpleXMLElement( $xml );
        $children = $xml->children();
        if ( !$children ) return (string) $xml;
        $arr = array();
        foreach ( $children as $key => $node ) {
            $node = ArrayToXML::toArray( $node );

            // support for 'anon' non-associative arrays
            if ( $key == 'anon' ) $key = count( $arr );

            // if the node is already set, put it into an array
            if ( isset( $arr[$key] ) ) {
                if ( !is_array( $arr[$key] ) || $arr[$key][0] == null ) $arr[$key] = array( $arr[$key] );
                $arr[$key][] = $node;
            } else {
                $arr[$key] = $node;
            }
        }
        return $arr;
    }

    // determine if a variable is an associative array
    public static function isAssoc( $array ) {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }
    
  
}
