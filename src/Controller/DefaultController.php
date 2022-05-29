<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\SpreadSheet;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class DefaultController extends AbstractController
{

    public function index(SpreadSheet $spreadSheetService, Request $request): JsonResponse
    {

        $searchParams = [
            'storage' => $request->get('storage'),
            'ram' => $request->get('ram'),
            'diskType' => $request->get('diskType'),
            'location' => $request->get('location'),
        ];

        $validator = Validation::createValidator();
        $constraint = new Assert\Collection(array(
            // the keys correspond to the keys in the input array
            'storage' => new Assert\Length(array('min' => 1, 'max' => 255)),
            'ram' => new Assert\Length(array('min' => 1, 'max' => 255)),
            'diskType' => new Assert\Length(array('min' => 1, 'max' => 255)),
            'location' => new Assert\Length(array('min' => 1, 'max' => 255)),
        ));
        $violations = $validator->validate($searchParams, $constraint);
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
}
