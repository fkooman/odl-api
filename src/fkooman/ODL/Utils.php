<?php

namespace fkooman\ODL;

class Utils
{
    /**
     * Get a list of supported 'locations' from the dataDir.
     */
    public static function extractLocations($dataDir)
    {
        $supportedLocations = array();

        foreach (glob($dataDir.'/*.json') as $fileName) {
            $flowNameTable = basename($fileName, '.json');

            if (0 === strpos($flowNameTable, 'loop')) {
                continue;
            }
            if (0 === strpos($flowNameTable, 'delete')) {
                continue;
            }

            list($flowName, $table) = explode('-', $flowNameTable);
            $locations = explode('_', $flowName);
            foreach ($locations as $location) {
                if (!array_key_exists($location, $supportedLocations)) {
                    $function = self::locationToFunction($location);
                    $supportedLocations[$location]['id'] = $location;
                    $supportedLocations[$location]['name'] = $location;
                    $supportedLocations[$location]['function'] = $function;
                }
            }
        }

        return $supportedLocations;
    }

    public static function locationToFunction($location)
    {
        switch ($location) {
            case 'CloudSigma':
                return 'text';
            case 'Microsoft':
                return 'TBD';
            case 'Okeanos':
                return 'TBD';
            case 'SURFnet':
                return 'grayscale';
            case 'SURFsara':
                return 'flip';
            case 'perfSONAR':
                return 'mirror';
            default:
                return '?';
        }
    }
}
