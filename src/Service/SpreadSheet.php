<?php
/**
 * @author Sujith Haridasan <sujith.h@gmail.com>
 *
 * @copyright Copyright (c) 2020
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

namespace App\Service;


use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class SpreadSheet
{
    public function __construct()
    {

    }

    /**
     * Compute the storage value
     * @param string $storageVal
     * @return int
     */
    private function computeStorage($storageVal) {
        $computedValue = 1;
        $storageArray = explode("x", $storageVal);
        foreach ($storageArray as $value) {
            $computedValue = $computedValue * $value;
        }
        return $computedValue;
    }

    /**
     * Get the storage result by converting TB into GB for comparision
     * In this method the computedStorage, min and max values are converted to GB
     * for comparison. If all are in GB then no conversion is applied.
     *
     * @param int $computedStorage
     * @param string $compStorageType
     * @param null|string $minVal
     * @param null|string $maxVal
     * @return bool
     */
    private function getStorageRangeResult($computedStorage, $compStorageType, $minVal, $maxVal) {
        $result = false;
        preg_match("/^(\d+)(GB|TB)/", $minVal, $minMatches);
        preg_match("/^(\d+)(GB|TB)/", $maxVal, $maxMatches);

        /**
         * Convert the computed storage to GB
         */
        if ($compStorageType === 'TB') {
            $computedStorage = $computedStorage * 1024;
        }

        /**
         * Convert TB to GB
         */
        $convertedMinGB = 0;
        $convertedMaxGB = 0;
        if (isset($minMatches[1]) && isset($minMatches[2])) {
            if ($minMatches[2] === 'TB') {
                $convertedMinGB = 1024 * $minMatches[1];
            }
        }
        if (isset($maxMatches[1]) && isset($maxMatches[2])) {
            if ($maxMatches[2] === 'TB') {
                $convertedMaxGB = 1024 * $maxMatches[1];
            }
        }

        /**
         * If the min range is in GB
         */
        if ($convertedMinGB === 0) {
            if ((isset($minMatches[1])) && $computedStorage >= $minMatches[1]) {
                $result = true;
            }
        } else {
            if ($computedStorage >= $convertedMinGB) {
                $result = true;
            } else {
                $result = false;
            }
        }

        /**
         * if the max range is in GB
         */
        if ($convertedMaxGB === 0) {
            if ((isset($maxMatches[1])) && $computedStorage <= $maxMatches[1]) {
                $result = true;
            }
        } else {
            if ($computedStorage <= $convertedMaxGB) {
                $result = true;
            } else {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Validate the storage value matches with the filtervalue
     * @param string $matches
     * @param string $filterValue
     * @return bool
     */
    private function filterStorage($matches, $filterValue) {
        $filterValues = explode('-', $filterValue);
        $minVal = $filterValues[0];
        $maxVal = null;
        if (isset($filterValues[1])) {
            $maxVal  = $filterValues[1];
        }

        if (isset($matches[1]) && isset($matches[2])) {
            $computedStorage = $this->computeStorage($matches[1]);
            /**
             * Check if the storage falls under the range provided
             */
            if ($maxVal !== null) {
                return $this->getStorageRangeResult($computedStorage, $matches[2], $minVal, $maxVal);
            }
            $computedStorage .= $matches[2];
            return ($computedStorage === $filterValue);
        }
        return false;
    }

    /**
     * Validates the harddisk value matches with the filter value
     * @param string $matches
     * @param string $filterValue
     * @return bool
     */
    private function filterHarddisk($matches, $filterValue) {
        if (!isset($matches[3])) {
            return false;
        }
        return ($matches[3] === $filterValue);
    }

    /**
     * Validates the ram value matches with the filter value
     * @param string $ramProperties
     * @param string $filterValue
     * @return bool
     */
    private function filterByRAM($ramProperties, $filterValue) {
        $filterValues = explode(',', $filterValue);
        preg_match("/^(\\d+GB).*/", $ramProperties,$matches);
        if (!isset($matches[1])) {
            return false;
        }

        if (!isset($filterValues[1])) {
            return ($matches[1] === $filterValue);
        }

        if (isset($filterValues[1])) {
            return in_array($matches[1], $filterValues);
        }
    }

    /**
     * Populate the output which needs to be pushed to the filteredOutput data.
     * @param array $output
     * @param string $cellValue
     * @param string $columnName
     * @return array
     */
    private function populateOutput($output, $cellValue, $columnName) {

        if ($columnName === 'A') {
            $output['Model'] = $cellValue;
        }
        if ($columnName === 'B') {
            $output['RAM'] = $cellValue;
        }
        if ($columnName === 'C') {
            $output['HDD'] = $cellValue;
        }
        if ($columnName === 'D') {
            $output['Location'] = $cellValue;
        }
        if ($columnName === 'E') {
            $output['Price'] = $cellValue;
        }
        return $output;
    }

    /**
     * Read the spreadsheet file and return the json data
     *
     * @param $searchParams
     * @param $file
     * @return array
     */
    public function readFile($searchParams, $file) {
        $ret = [];
        $reader = ReaderEntityFactory::createReaderFromFile($file);
        $reader->open($file);
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $values[] = $row->toArray();
            }
        }
        foreach ($values as $va) {
            $ram = $va[1]; $storage = $va[2]; $location = $va[3];
            preg_match ('/^(\\d+GB).*/', $ram, $matches);
            $includeInResult = false;
            if(isset($searchParams['ram'][0]) && isset($matches[1])) {
                if(in_array($matches[1],explode(",",$searchParams['ram'][0]))) {
                    $includeInResult = true;
                }
            }
            //location filter
            if(isset($searchParams['location']) && strstr($location, $searchParams['location'])) {
                $includeInResult = true;
            }
            //storage filter
            preg_match("/(.*)(TB|GB)(.*)/", $storage, $minMatches);
            if($minMatches) {
                if(isset($searchParams['diskType']) && strstr($minMatches[3], $searchParams['diskType'])) {
                    $includeInResult = true;
                }
                if(isset($searchParams['storage']) && strstr($minMatches[0], $searchParams['storage'])) {
                    $includeInResult = true;
                }
            }
            if($includeInResult) {
                $ret[] = $va;
            }
        }
        $reader->close();

        return $ret;
    }
}