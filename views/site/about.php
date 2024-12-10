<?php

$xmlStrings = "https://cledical.quatrys.fr/api/products/?filter[reference]=" . urlencode("D00269") . "&display=full&ws_key=9FY6ILG2C8L4I7KLYBMYIAJM7V6EIYCT";



function curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Vaisonet e-connecteur");
    $get = curl_exec($ch);
    curl_close($ch);

    return $get;
}

function xml2array($xml)
{
    if (\is_object($xml) and (get_class($xml) == 'SimpleXMLExtended')) {
        $attributes = $xml->attributes();
        foreach ($attributes as $k => $v) {
            if ($v) {
                $a[$k] = (string)$v;
            }
        }
        $x = $xml;
        $xml = get_object_vars($xml);
    }
    if (\is_array($xml)) {
        if (count($xml) === 0) {
            return (string)$x; // for CDATA
        }

        foreach ($xml as $key => $value) {
            $r[$key] = xml2array($value);
        }

        if (isset($a)) {
            $r['@attributes'] = $a; // Attributes
            if (count($x) === 0) {
                $r['@value'] = (string)$x;
            }
        }

        return $r;
    }
    return (string)$xml;
}

class SimpleXMLExtended extends SimpleXMLElement
{

    public function addCData($cdata_text)
    {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }

    /**
     * Create a child with CDATA value
     * @param string $name The name of the child element to add.
     * @param string $cdata_text The CDATA value of the child element.
     */
    public function addChildCData($name, $cdata_text)
    {
        $child = $this->addChild($name);
        $child->addCData($cdata_text);
        return $child;
    }
}

$test = xml2array(curl_get($xmlStrings));


print_r($test);
