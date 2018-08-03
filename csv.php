<?php

/**
 * CSV generator
 *
 * This file has class to generate CSV
 *
 * @category Saral
 * @package Saral_CSV
 * @version		0.1
 * @since		0.1
 */

/**
 * CSV class
 *
 * This class is used to generate CSV and can be saved or downloaded
 *
 * @category Saral
 * @package Saral_CSV
 * @version Release: 0.1
 * @since 28.Aug.2011
 * @author Sailesh Jaiswal (cya@sailesh.in)
 */
class Saral_CSV
{

    /**
     *
     * use to generate and download csv
     *
     * @param array $data
     * @param array $header
     * @param string $name
     */
    public function download($data, $header = array(), $name = 'report')
    {

        // adding header to data
        if (count($header) != 0) {
            array_unshift($data, $header);
        }

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$name}.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $outstream = fopen("php://output", 'w');

        function __outputCSV(&$vals, $key, $filehandler)
        {
            fputcsv($filehandler, $vals, ',', '"');
        }

        array_walk($data, '__outputCSV', $outstream);
        fclose($outstream);
    }

    /**
     *
     * used to generate & save CSV
     *
     * @param array $data
     * @param array $header
     * @param string $name
     */
    public function saveCSV($data, $header = array(), $name = 'report')
    {

        // adding header to data
        if (count($header) != 0) {
            array_unshift($data, $header);
        }

        $outstream = fopen($name . '.csv', 'w');

        function __outputCSV(&$vals, $key, $filehandler)
        {
            fputcsv($filehandler, $vals, ',', '"');
        }

        array_walk($data, '__outputCSV', $outstream);
        fclose($outstream);
    }
}
?>
