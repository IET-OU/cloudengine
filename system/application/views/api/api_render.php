<?php
/**
 * Render the API response as JSON(-P) or XML.
 *
 * Hmm, 'total_results v. 'opensearch:totalResults'.
 */

if (!isset($format)) {
    $format = $this->uri->file_extension() ? $this->uri->file_extension() : 'json';
}
$debug = $this->config->item('x_api_debug');
switch ($format):
case 'xml':
    // Note, $this is the Loader instance.
    $this->helper('xml');

    @header("Content-Type: application/xml; charset=UTF-8");
    $xmlns = 'xmlns="http://cloudworks.ac.uk/2010/api"';
    //No namespace etc. for errors.
    $attr = isset($response['code']) ? null : "$xmlns xml:base=\"".base_url().'"';
    echo ArrayToXML::toXml($response, 'rsp', $xml=null, $attr, $debug);
    break;

case 'json': # Is JSON the default? (For errors re. format.)
default:
    $json = json_encode($response);

    if ($debug) {
        // Debug - readability.
        $json = str_replace(
          array(',"',   ',{',  ':['),
          array(",\n\"",",\n  {",":[\n  " ), #PHP_EOL
          $json);
        // Prevent most browsers prompting to save file!
        header("Content-Type: text/javascript; charset=UTF-8");
        @header("Content-Disposition: inline; filename=cloudworks"
          .str_replace('/', '-', $this->uri->uri_string()).".txt");
    } else {
        // Production.
        @header("Content-Type: application/json; charset=UTF-8");
    }

    // Optionally add a JSON-P callback function.
    if (isset($json_callback) && $json_callback) {
        $json = "$json_callback($json)";
    }
    echo $json;
    
    break;
endswitch;

exit;
