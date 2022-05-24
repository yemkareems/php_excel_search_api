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

namespace App\Tests;


use App\Service\SpreadSheet;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ReadSpreadSheetTest extends TestCase
{
    private $readSheet;
    const FILENAME = "servers_filters.xlsx";
    protected function setUp(): void
    {
        parent::setUp();
        $this->readSheet = new SpreadSheet();
    }



    /**
     * Test by passing only 1 filter to the API.
     * The return from the API call will have 2 more
     * indices in the array. So the exact content is
     * the count(dataFetched) - 2.
     */
    public function testReadFileWith1Filter() {
        $absPathOfFile = __DIR__ . '/'. self::FILENAME;
        $params = [
            'storage' => null,
            'ram' => '4GB',
            'diskType' => null,
            'location' => null,
        ];
        $data = $this->readSheet->readFile($params, $absPathOfFile);
        print_r($data);
        print "\n";
        $expectedResult = [["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","AmsterdamAMS-01","€39.99"]];
        print_r($expectedResult);
        //exit;
        $this->assertEquals($data, $expectedResult);
    }

    /**
     * Test by passing only storage filter to the API.
     * The return from the API call will have 2 more
     * indices in the array. So the exact content is
     * the count(dataFetched) - 2.
     */
    public function testReadFileWithStorageFilter() {
        $absPathOfFile = __DIR__ . '/'. self::FILENAME;
        $params = [
            'storage' => '4TB',
            'ram' => null,
            'diskType' => null,
            'location' => null,
        ];
        $data = $this->readSheet->readFile($params, $absPathOfFile);
        $expectedResult = [["Dell R210Intel Xeon X3440","16GBDDR3","2x2TBSATA2","AmsterdamAMS-01","€49.99"],["Dell R210-IIIntel Xeon E3-1230v2","16GBDDR3","2x2TBSATA2","FrankfurtABC-01","€72.99"],["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","AmsterdamAMS-01","€39.99"]];
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * Test by passing only harddisk filter to the API.
     * The return from the API call will have 2 more
     * indices in the array. So the exact content is
     * the count(dataFetched) - 2.
     */
    public function testReadFileHarddiskFilter() {
        $absPathOfFile = __DIR__ . '/'. self::FILENAME;
        $params = [
            'storage' => null,
            'ram' => null,
            'diskType' => 'SSD',
            'location' => null,
        ];
        $data = $this->readSheet->readFile($params, $absPathOfFile);
        $expectedResult = [
            [
                '0' => 'RH2288v32x Intel Xeon E5-2650V4',
                '1' => '128GBDDR4',
                '2' => '4x480GBSSD',
                '3' => 'AmsterdamAMS-01',
                '4' => '€227.99',
            ],
            [
                '0' => 'Dell R730XD2x Intel Xeon E5-2667v4',
                '1' => '128GBDDR4',
                '2' => '2x120GBSSD',
                '3' => 'SingaporeSIN-01',
                '4' => '€364.99',
            ],
            [
                '0' => 'Dell R730XD2x Intel Xeon E5-2670v3',
                '1' => '128GBDDR4',
                '2' => '2x120GBSSD',
                '3' => 'AmsterdamAMS-01',
                '4' => '€364.99',
            ],
            [
                '0' => 'Dell R730XD2x Intel Xeon E5-2650v3',
                '1' => '128GBDDR4',
                '2' => '4x480GBSSD',
                '3' => 'AmsterdamAMS-01',
                '4' => '€279.99',
            ],
            [
                '0' => 'Dell R730XD2x Intel Xeon E5-2650v4',
                '1' => '128GBDDR4',
                '2' => '4x480GBSSD',
                '3' => 'AmsterdamAMS-01',
                '4' => '€286.99',
            ]
        ];
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * Test by passing only location filter to the API.
     * The return from the API call will have 2 more
     * indices in the array. So the exact content is
     * the count(dataFetched) - 2.
     */
    public function testReadFile3Filter() {
        $absPathOfFile = __DIR__ . '/'. self::FILENAME;
        $params = [
            'storage' => null,
            'ram' => null,
            'diskType' => null,
            'location' => 'AmsterdamAMS-01',
        ];
        $data = $this->readSheet->readFile($params, $absPathOfFile);
        $expectedResult = [
            [
                '0' => 'Dell R210Intel Xeon X3440',
                '1' => '16GBDDR3',
                '2' => '2x2TBSATA2',
                '3' => 'AmsterdamAMS-01',
                '4' => '€49.99',
            ],
            [
                '0' => 'HP DL180G62x Intel Xeon E5620',
                '1' => '32GBDDR3',
                '2' => '8x2TBSATA2',
                '3' => 'AmsterdamAMS-01',
                '4' => '€119.00',
            ],
            [
                '0' => 'HP DL380eG82x Intel Xeon E5-2420',
                '1' => '32GBDDR3',
                '2' => '8x2TBSATA2',
                '3' => 'AmsterdamAMS-01',
                '4' => '€131.99',
            ],
            [
                '0' => 'RH2288v32x Intel Xeon E5-2650V4',
                '1' => '128GBDDR4',
                '2' => '4x480GBSSD',
                '3' => 'AmsterdamAMS-01',
                '4' => '€227.99',
            ],
            [
                '0' => 'RH2288v32x Intel Xeon E5-2620v4',
                '1' => '64GBDDR4',
                '2' => '4x2TBSATA2',
                '3' => 'AmsterdamAMS-01',
                '4' => '€161.99',
            ],
            [
                '0' => 'HP DL380pG82x Intel Xeon E5-2650',
                '1' => '64GBDDR3',
                '2' => '8x2TBSATA2',
                '3' => 'AmsterdamAMS-01',
                '4' => '€179.99',
            ],
            [
                '0' => 'IBM X36302x Intel Xeon E5620',
                '1' => '32GBDDR3',
                '2' => '8x2TBSATA2',
                '3' => 'AmsterdamAMS-01',
                '4' => '€106.99',
            ],
            [
                '0' => 'HP DL120G7Intel G850',
                '1' => '4GBDDR3',
                '2' => '4x1TBSATA2',
                '3' => 'AmsterdamAMS-01',
                '4' => '€39.99',
            ],
            [
                '0' => 'Dell R730XD2x Intel Xeon E5-2670v3',
                '1' => '128GBDDR4',
                '2' => '2x120GBSSD',
                '3' => 'AmsterdamAMS-01',
                '4' => '€364.99',
            ],
            [
                '0' => 'Dell R730XD2x Intel Xeon E5-2650v3',
                '1' => '128GBDDR4',
                '2' => '4x480GBSSD',
                '3' => 'AmsterdamAMS-01',
                '4' => '€279.99',
            ],
            [
                '0' => 'Dell R730XD2x Intel Xeon E5-2650v4',
                '1' => '128GBDDR4',
                '2' => '4x480GBSSD',
                '3' => 'AmsterdamAMS-01',
                '4' => '€286.99',
            ]
        ];
        $this->assertEquals($expectedResult, $data);
    }


}
