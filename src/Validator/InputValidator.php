<?php

namespace App\Validator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class InputValidator
{
    public function validate(array $validateInput): array
    {
        $storageValues = ['0GB', '250GB', '500GB', '1TB', '2TB', '3TB', '4TB', '8TB', '12TB', '24TB', '48TB', '72TB', '100TB'];
        $constraints = new Assert\Collection(array(
            'storageFrom' => new Assert\Choice($storageValues),
            'storageTo' => new Assert\Choice($storageValues),
            'ram' => new Assert\Choice(['2GB', '4GB', '8GB', '12GB', '16GB', '24GB', '32GB', '48GB', '64GB', '96GB', '128GB'], null, true),
            'diskType' => new Assert\Choice(['SAS', 'SATA', 'SSD']),
            'location' => new Assert\Choice(['AmsterdamAMS-01', 'DallasDAL-10', 'FrankfurtFRA-10', 'Hong KongHKG-10', 'San FranciscoSFO-12', 'SingaporeSIN-11', 'Washington D.C.WDC-01']),
        ));
        $messages = [];

        $validator = Validation::createValidator();
        $violations = $validator->validate($validateInput, $constraints);
        if($violations->count() > 0){
            foreach ($violations as $violation) {
                $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
        }

        return $messages;
    }

}