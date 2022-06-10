<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\SpreadSheet;
use App\Validator\InputValidator;

class SearchController extends AbstractController
{

    /**
     * @param SpreadSheet $spreadSheetService
     * @param Request $request
     * @param InputValidator $validator
     * @return JsonResponse
     */
    public function searchAction(SpreadSheet $spreadSheetService, Request $request, InputValidator $validator): JsonResponse
    {

        $searchParams = [
            'storageFrom' => $request->get('storageFrom'),
            'storageTo' => $request->get('storageTo'),
            'ram' => $request->get('ram'),
            'diskType' => $request->get('diskType'),
            'location' => $request->get('location'),
        ];

        $errorMessages = $validator->validate($searchParams);

        if (count($errorMessages)) {
            return new JsonResponse(["error" => $errorMessages], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $dataSource = getcwd() . '/../' . $this->getParameter('dataSource') . '/';
        try {
            $searchResult = $spreadSheetService->searchDataSource($searchParams, $dataSource);
            $returnSearchResponse = new JsonResponse(['success' => 'ok', 'searchCount' => count($searchResult), 'searchResult' => $searchResult], Response::HTTP_OK);
            if (0 === count($searchResult)) {
                $returnSearchResponse = new JsonResponse(['error' => 'No data found for the search'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            $returnSearchResponse = new JsonResponse(['error' => 'Error reading the file'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

       return $returnSearchResponse;
    }

}
