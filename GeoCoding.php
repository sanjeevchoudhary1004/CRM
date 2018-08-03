<?php

/**
 * GeoCoding class
 *
 * This class is used to generate geo coding
 *
 * @category Saral
 * @version Release: 0.1
 * @since 28.Aug.2011
 */
class GeoCoding
{

    private $forward_geocoding_url = 'http://maps.googleapis.com/maps/api/geocode/json?sensor=true&address=';

    private $reverse_geocoding_url = 'http://maps.googleapis.com/maps/api/geocode/json?sensor=true&latlng=';

    private $time_zone_url = 'https://maps.googleapis.com/maps/api/timezone/json?location=';

    function getAddress($lat, $lng)
    {
        $lat = trim($lat);
        $lng = trim($lng);

        // $this->logInfo()
        // file_put_contents("/tmp/log_2014-11-29.log", $this->reverse_geocoding_url . $lat . ',' . $lng, FILE_APPEND);
        // Make the HTTP request
        $data = @file_get_contents($this->reverse_geocoding_url . $lat . ',' . $lng);

        // file_put_contents("/tmp/log_2014-11-29.log", $data, FILE_APPEND);
        // Parse the json response
        $json_data = json_decode($data, true);

        $address = array();
        if ($this->checkStatus($json_data)) {

            $address1 = '';

            $address_component = $json_data["results"][0]['address_components'];
            $address['country'] = $this->getLongNameByType('country', $address_component, true);
            $address['state'] = $this->getLongNameByType('administrative_area_level_1', $address_component);
            $address['city'] = ($this->getLongNameByType('locality', $address_component) == '') ? $address['state'] : $this->getLongNameByType('locality', $address_component);
            $address['zip'] = $this->getLongNameByType('postal_code', $address_component);
            $address1 = $this->getLongNameByType('street_number', $address_component);
            $address1 .= ((trim($address1) == '') ? '' : ', ') . $this->getLongNameByType('route', $address_component);
            $address1 .= ((trim($address1) == '') ? '' : ', ') . $this->getLongNameByType('neighborhood', $address_component);
            $address['street'] = $this->getLongNameByType('street_number', $address_component);
            $address['address1'] = $address1;
            $address['address'] = $json_data['results'][0]['formatted_address'];
        } else {
            throw new Exception('Invalid location');
        }
        return $address;
    }

    /*
     * Check if the json data from Google Geo is valid
     */
    function checkStatus($json_data)
    {
        if ($json_data["status"] == "OK")
            return true;
        return false;
    }

    /*
     * Searching in Google Geo json, return the long name given the type.
     * (If short_name is true, return short name)
     */
    function getLongNameByType($type, $array, $short_name = false)
    {
        foreach ($array as $value) {
            if (in_array($type, $value["types"])) {
                if ($short_name)
                    return $value["short_name"];
                return $value["long_name"];
            }
        }
    }

    /**
     * This function will get the time zone details from the google
     */
    function getTimeZone($location, $time_stamp)
    {
        $data = @file_get_contents($this->time_zone_url . $location . '&timestamp=' . $time_stamp);

        // Parse the json response
        $json_data = json_decode($data, true);

        return $json_data;
    }
}