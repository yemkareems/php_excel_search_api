<?php
/**
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


use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;

class SpreadSheet
{

    /**
     * Read the spreadsheet file and return the json data
     *
     * @param $searchParams
     * @param $dataSource
     * @return array
     * @throws UnsupportedTypeException
     * @throws IOException
     * @throws ReaderNotOpenedException
     */
    public function searchDataSource($searchParams, $dataSource): array {
        $filesList = array_values(array_diff(scandir($dataSource), array('.', '..')));
        $ret = [];
        $searchKeysCount = 0;
        foreach ($searchParams as $key => $searchValue) {
            if(isset($searchValue) && $searchValue != '') {
                $searchKeysCount++;
            }
        }
        $values = [];
        foreach ($filesList as $file) {
            $reader = ReaderEntityFactory::createReaderFromFile($dataSource . $file);
            $reader->open($dataSource . $file);
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $values[] = $row->toArray();
                }
            }
            $reader->close();
        }
        foreach ($values as $va) {
            $ram = $va[1]; $storage = $va[2]; $location = $va[3];
            preg_match ('/^(\\d+GB).*/', $ram, $matches);
            $includeInResult = 0;
            //ram filter
            if(isset($searchParams['ram']) && isset($matches[1])) {
                if(in_array($matches[1], explode(",",$searchParams['ram']))) {
                    $includeInResult++;
                }
            }
            //location filter
            if(isset($searchParams['location']) && strstr($location, $searchParams['location'])) {
                $includeInResult++;
            }
            preg_match("/(.*)(TB|GB)(.*)/", $storage, $minMatches);
            if($minMatches) {
                //disk type filter
                if(isset($searchParams['diskType']) && strstr($minMatches[3], $searchParams['diskType'])) {
                    $includeInResult++;
                }
                //storage filter
                if(isset($searchParams['storage'])) {
                    $storageFilter = $this->filterStorage($minMatches, $searchParams['storage']);
                    if ($storageFilter) {
                        $includeInResult++;
                    }
                }
            }
            if($includeInResult == $searchKeysCount) {
                $ret[] = [$va[0], $va[1], $va[2], $va[3], $va[4]];
            }
        }

        return $ret;
    }

    /**
     * Check if storage falls in the given range
     * @param array $matches
     * @param string $filterValue
     * @return bool
     */
    private function filterStorage(array $matches, string $filterValue): bool {

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
            return $this->getStorageRangeResult($computedStorage, $matches[2], $minVal, $maxVal);
        }

        return false;
    }

    /**
     * Compute the storage value
     * @param string $storageVal
     * @return int
     */
    private function computeStorage(string $storageVal): int {
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
    private function getStorageRangeResult(int $computedStorage, string $compStorageType, ? string $minVal, ? string $maxVal): bool {
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
            } else {
                $convertedMinGB = $minMatches[1];
            }
        }
        if (isset($maxMatches[1]) && isset($maxMatches[2])) {
            if ($maxMatches[2] === 'TB') {
                $convertedMaxGB = 1024 * $maxMatches[1];
            } else {
                $convertedMaxGB = $maxMatches[1];
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
            if ($result && $computedStorage <= $convertedMaxGB) {
                $result = true;
            } else {
                $result = false;
            }
        }

        return $result;
    }

}