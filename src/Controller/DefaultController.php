<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SpreadSheet;
class DefaultController
{

    public function index(Request $request){

        $storageSearchCriteria = $request->get('storage');
        $ramSearchCriteria = $request->get('ram');
        $hardDiskSearchCriteria = $request->get('diskType');
        $locationSearchCriteria = $request->get('location');

        $searchParams = [
            'storage' => $storageSearchCriteria,
            'ram' => $ramSearchCriteria,
            'diskType' => $hardDiskSearchCriteria,
            'location' => $locationSearchCriteria,
        ];
        $filePath = getcwd().'/../third_party/file.xlsx';

        $spreadSheet = new SpreadSheet();
        $data = $spreadSheet->readFile($searchParams, $filePath);
        return new JsonResponse($data);
    }
}