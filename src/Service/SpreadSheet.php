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

    /**
     * Read the spreadsheet file and return the json data
     *
     * @param $searchParams
     * @param $file
     * @return array
     */
    public function readFile($searchParams, $file) {
        $ret = [];
        $searchKeysCount = 0;
        foreach ($searchParams as $key => $searchValue) {
            if(isset($searchValue) && $searchValue != '') {
                $searchKeysCount++;
            }
        }
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
            $includeInResult = 0;
            if(isset($searchParams['ram'][0]) && isset($matches[1])) {
                if(in_array($matches[1],explode(",",$searchParams['ram'][0]))) {
                    $includeInResult++;
                }
            }
            //location filter
            if(isset($searchParams['location']) && strstr($location, $searchParams['location'])) {
                $includeInResult++;
            }
            //storage filter
            preg_match("/(.*)(TB|GB)(.*)/", $storage, $minMatches);
            if($minMatches) {
                if(isset($searchParams['diskType']) && strstr($minMatches[3], $searchParams['diskType'])) {
                    $includeInResult++;
                }
                if(isset($searchParams['storage']) && strstr($minMatches[0], $searchParams['storage'])) {
                    $includeInResult++;
                }
            }
            if($includeInResult == $searchKeysCount) {
                $ret[] = [$va[0], $va[1], $va[2], $va[3], $va[4]];
            }
        }
        $reader->close();

        return $ret;
    }
}