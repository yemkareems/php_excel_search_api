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
        $searchResults = [];
        $searchKeysCount = 0;
        foreach ($searchParams as $key => $searchValue) {
            if(isset($searchValue) && $searchValue != '' && $key != 'storageTo') {
                $searchKeysCount++;
            }
            if($key == 'storageTo' && is_null($searchParams['storageFrom']) && !is_null($searchParams['storageTo'])) {
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
            $includeInResult = 0;
            //ram filter
            $includeInResult += $this->isRamMatching($ram, $searchParams);
            //location filter
            $includeInResult += $this->isLocationMatching($location, $searchParams);
            preg_match("/(.*)(TB|GB)(.*)/", $storage, $minMatches);
            if($minMatches) {
                //disk type filter
                $includeInResult += $this->isDiskTypeMatching($minMatches, $searchParams);
                //storage filter
                if(isset($searchParams['storageFrom']) || isset($searchParams['storageTo'])) {
                    $includeInResult += $this->isStorageMatching($minMatches, $searchParams['storageFrom'], $searchParams['storageTo']);
                }
            }
            if($includeInResult == $searchKeysCount) {
                $searchResults[] = [$va[0], $va[1], $va[2], $va[3], $va[4]];
            }
        }

        return $searchResults;
    }

    /**
     * @param string $ram
     * @param array $searchParams
     * @return int
     */
    private function isRamMatching(string $ram, array $searchParams): int {
        preg_match ('/^(\\d+GB).*/', $ram, $matches);
        return isset($searchParams['ram']) && isset($matches[1]) && in_array($matches[1], $searchParams['ram']) ? 1 : 0;
    }

    /**
     * @param string $location
     * @param array $searchParams
     * @return int
     */
    private function isLocationMatching(string $location, array $searchParams): int {
        return isset($searchParams['location']) && strstr($location, $searchParams['location']) ? 1 : 0;
    }

    /**
     * @param array $minMatches
     * @param array $searchParams
     * @return int
     */
    private function isDiskTypeMatching(array $minMatches, array $searchParams): int
    {
        return isset($searchParams['diskType']) && strstr($minMatches[3], $searchParams['diskType']) ? 1 : 0;
    }

    /**
     * Check if storage falls in the given range
     * @param array $matches
     * @param string|null $minVal
     * @param string|null $maxVal
     * @return int
     */
    private function isStorageMatching(array $matches, ? string $minVal, ? string $maxVal): int {

        $minVal = is_null($minVal) ? '0GB' : $minVal;
        $computedStorage = $this->computeStorage($matches[1]);
        return $this->getStorageRangeResult($computedStorage, $matches[2], $minVal, $maxVal);

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
     * @return int
     */
    private function getStorageRangeResult(int $computedStorage, string $compStorageType, ? string $minVal, ? string $maxVal): int {
        $result = false;
        preg_match("/^(\d+)(GB|TB)/", $minVal, $minMatches);
        preg_match("/^(\d+)(GB|TB)/", $maxVal, $maxMatches);
        /**
         * Convert the computed storage to GB
         */
        $computedStorage = $compStorageType === 'TB' ? $computedStorage * 1024: $computedStorage;

        /**
         * Convert TB to GB
         */
        $convertedMaxGB = $maxMatches[1] ?? 0;
        $convertedMinGB = isset($minMatches[1]) && isset($minMatches[2]) && $minMatches[2] === 'TB' ? 1024 * $minMatches[1]: $minMatches[1];
        if(isset($maxMatches[1]) && isset($maxMatches[2])) {
            $convertedMaxGB = $maxMatches[2] === 'TB' ? 1024 * $maxMatches[1]: $maxMatches[1];
        }

        /**
         * If the min range is in GB
         */
        $result = $computedStorage >= $convertedMinGB;

        /**
         * if the max range is in GB
         */
        if($convertedMaxGB === 0) {
            if($result && (isset($maxMatches[1])) && $computedStorage <= $maxMatches[1]) {
                $result = true;
            }
        } else {
            if($result && $computedStorage <= $convertedMaxGB) {
                $result = true;
            } else {
                $result = false;
            }
        }

        return $result == true ? 1 : 0;
    }
}