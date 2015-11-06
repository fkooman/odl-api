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

    /**
     * Determine the flow by removing the locations with priority 0 and
     * sorting the non-zero locations by number. Lowest is highest priority.
     */
    public static function determineFlow(array $locationData)
    {
        $flow = array();
        foreach ($locationData as $key => $value) {
            if (0 >= (int) $value) {
                continue;
            }
            $flow[$value] = $key;
        }

        ksort($flow);
        if (0 === count($flow)) {
            return false;
        }

        return implode('_', $flow);
    }
}
