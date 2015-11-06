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
            list($flowName, $table) = explode('-', $flowNameTable);
            $locations = explode('_', $flowName);
            foreach ($locations as $location) {
                if (!array_key_exists($location, $supportedLocations)) {
                    $supportedLocations[$location]['id'] = $location;
                    $supportedLocations[$location]['name'] = $location;
                }
            }
        }

        return $supportedLocations;
    }
}
