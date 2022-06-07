<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\SpreadSheet;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SearchController extends AbstractController
{

    public function searchAction(SpreadSheet $spreadSheetService, Request $request, ValidatorInterface $validator): JsonResponse
    {

        $searchParams = [
            'storage' => $request->get('storage'),
            'ram' => $request->get('ram'),
            'diskType' => $request->get('diskType'),
            'location' => $request->get('location'),
        ];

        $constraints = $this->getValidationConstraints();
        $violations = $validator->validate($searchParams, $constraints);
        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $dataSource = getcwd().'/../'.$this->getParameter('dataSource').'/';
        try {
            $data = $spreadSheetService->searchDataSource($searchParams, $dataSource);
            if($data) {
                return new JsonResponse(['success' => 'ok', 'data' => $data], Response::HTTP_OK);
            } else {
                return new JsonResponse(['error' => 'No data found for the search'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error reading the file'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getValidationConstraints(): Assert\Collection
    {
        return new Assert\Collection(array(
            'storage' => new Assert\Length(array('min' => 1, 'max' => 255)),
            'ram' => new Assert\Choice(['2GB', '4GB', '8GB', '12GB', '16GB', '24GB', '32GB', '48GB', '64GB', '96GB'], null, true),
            'diskType' => new Assert\Choice(['SAS', 'SATA', 'SSD']),
            'location' => new Assert\Choice(['AmsterdamAMS-01', 'DallasDAL-10', 'FrankfurtFRA-10', 'Hong KongHKG-10', 'San FranciscoSFO-12', 'SingaporeSIN-11', 'Washington D.C.WDC-01']),
        ));

    }
}
