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

namespace App\Tests;


use App\Service\SpreadSheet;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use PHPUnit\Framework\TestCase;

class ReadSpreadSheetTest extends TestCase
{
    private $readSheet;
    const TEST_DATA_SOURCE = "dataSource/";
    protected function setUp(): void
    {
        parent::setUp();
        $this->readSheet = new SpreadSheet();
    }


    /**
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     * @throws IOException
     */
    public function testReadFileWithRamFilter() {
        $absPathOfFile = __DIR__ . '/'. self::TEST_DATA_SOURCE;
        $params = [
            'storageFrom' => null,
            'storageTo' => null,
            'ram' => ['4GB', '64GB'],
            'diskType' => null,
            'location' => null,
        ];
        $data = $this->readSheet->searchDataSource($params, $absPathOfFile);
        $expectedResult = [["RH2288v32x Intel Xeon E5-2620v4","64GBDDR4","4x2TBSATA2","AmsterdamAMS-01","€161.99"],["HP DL380pG82x Intel Xeon E5-2650","64GBDDR3","8x2TBSATA2","AmsterdamAMS-01","€179.99"],["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","AmsterdamAMS-01","€39.99"]];
        $this->assertEquals($data, $expectedResult);
    }

    /**
     * @throws ReaderNotOpenedException
     * @throws UnsupportedTypeException
     * @throws IOException
     */
    public function testReadFileWithStorageFilter() {
        $absPathOfFile = __DIR__ . '/'. self::TEST_DATA_SOURCE;
        $params = [
            'storageFrom' => '1TB',
            'storageTo' => '4TB',
            'ram' => null,
            'diskType' => null,
            'location' => null,
        ];
        $data = $this->readSheet->searchDataSource($params, $absPathOfFile);
        $expectedResult = [["Dell R210Intel Xeon X3440","16GBDDR3","2x2TBSATA2","AmsterdamAMS-01","€49.99"],["RH2288v32x Intel Xeon E5-2650V4","128GBDDR4","4x480GBSSD","AmsterdamAMS-01","€227.99"],["Dell R210-IIIntel Xeon E3-1230v2","16GBDDR3","2x2TBSATA2","FrankfurtABC-01","€72.99"],["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","AmsterdamAMS-01","€39.99"],["Dell R730XD2x Intel Xeon E5-2650v3","128GBDDR4","4x480GBSSD","AmsterdamAMS-01","€279.99"],["Dell R730XD2x Intel Xeon E5-2650v4","128GBDDR4","4x480GBSSD","AmsterdamAMS-01","€286.99"]];
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     * @throws IOException
     */
    public function testReadFileHarddiskFilter() {
        $absPathOfFile = __DIR__ . '/'. self::TEST_DATA_SOURCE;
        $params = [
            'storageFrom' => null,
            'storageTo' => null,
            'ram' => null,
            'diskType' => 'SSD',
            'location' => null,
        ];
        $data = $this->readSheet->searchDataSource($params, $absPathOfFile);
        $expectedResult = [["RH2288v32x Intel Xeon E5-2650V4","128GBDDR4","4x480GBSSD","AmsterdamAMS-01","€227.99"],["Dell R730XD2x Intel Xeon E5-2667v4","128GBDDR4","2x120GBSSD","SingaporeSIN-01","€364.99"],["Dell R730XD2x Intel Xeon E5-2670v3","128GBDDR4","2x120GBSSD","AmsterdamAMS-01","€364.99"],["Dell R730XD2x Intel Xeon E5-2650v3","128GBDDR4","4x480GBSSD","AmsterdamAMS-01","€279.99"],["Dell R730XD2x Intel Xeon E5-2650v4","128GBDDR4","4x480GBSSD","AmsterdamAMS-01","€286.99"]];
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     * @throws IOException
     */
    public function testReadFileWithLocationFilter() {
        $absPathOfFile = __DIR__ . '/'. self::TEST_DATA_SOURCE;
        $params = [
            'storageFrom' => null,
            'storageTo' => null,
            'ram' => null,
            'diskType' => null,
            'location' => 'AmsterdamAMS-01',
        ];
        $data = $this->readSheet->searchDataSource($params, $absPathOfFile);
        $expectedResult = [["Dell R210Intel Xeon X3440","16GBDDR3","2x2TBSATA2","AmsterdamAMS-01","€49.99"],["HP DL180G62x Intel Xeon E5620","32GBDDR3","8x2TBSATA2","AmsterdamAMS-01","€119.00"],["HP DL380eG82x Intel Xeon E5-2420","32GBDDR3","8x2TBSATA2","AmsterdamAMS-01","€131.99"],["RH2288v32x Intel Xeon E5-2650V4","128GBDDR4","4x480GBSSD","AmsterdamAMS-01","€227.99"],["RH2288v32x Intel Xeon E5-2620v4","64GBDDR4","4x2TBSATA2","AmsterdamAMS-01","€161.99"],["HP DL380pG82x Intel Xeon E5-2650","64GBDDR3","8x2TBSATA2","AmsterdamAMS-01","€179.99"],["IBM X36302x Intel Xeon E5620","32GBDDR3","8x2TBSATA2","AmsterdamAMS-01","€106.99"],["HP DL120G7Intel G850","4GBDDR3","4x1TBSATA2","AmsterdamAMS-01","€39.99"],["Dell R730XD2x Intel Xeon E5-2670v3","128GBDDR4","2x120GBSSD","AmsterdamAMS-01","€364.99"],["Dell R730XD2x Intel Xeon E5-2650v3","128GBDDR4","4x480GBSSD","AmsterdamAMS-01","€279.99"],["Dell R730XD2x Intel Xeon E5-2650v4","128GBDDR4","4x480GBSSD","AmsterdamAMS-01","€286.99"]];
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @throws ReaderNotOpenedException
     * @throws UnsupportedTypeException
     * @throws IOException
     */
    public function testReadFileWithAllFilter()
    {
        $absPathOfFile = __DIR__ . '/' . self::TEST_DATA_SOURCE;
        $params = [
            'storageFrom' => '100GB',
            'storageTo' => '1TB',
            'ram' => ['128GB', '64GB'],
            'diskType' => 'SSD',
            'location' => 'SingaporeSIN-01',
        ];
        $data = $this->readSheet->searchDataSource($params, $absPathOfFile);
        $expected = [["Dell R730XD2x Intel Xeon E5-2667v4","128GBDDR4","2x120GBSSD","SingaporeSIN-01","€364.99"]];
        $this->assertEquals($expected, $data);
    }

    }
