<?php

namespace KrakenEvents\Compatibility\OpenCage\Geocoder;

use KrakenEvents\Compatibility\OpenCage\Geocoder\LegacyAbstractGeocoder;

class Geocoder extends LegacyAbstractGeocoder
{
    public function geocode($query, $optParams = [])
    {
        $url = self::URL . 'q=' . urlencode($query);
        
        if (is_array($optParams) && !empty($optParams)) {
            foreach ($optParams as $param => $paramValue) {
                $url .= '&'.$param.'=' . urlencode($paramValue);
            }
        }
        
        if (empty($this->key)) {
            throw new \Exception('Missing API key');
        }
        $url .= '&key=' . $this->key;

        $ret = json_decode($this->getJSON($url), true);
        return $ret;
    }
}
