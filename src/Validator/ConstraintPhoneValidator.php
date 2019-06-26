<?php

namespace Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConstraintPhoneValidator extends ConstraintValidator
{
    public $secret_key = '03ffd78488a31972377746a24ee5fc97';
    public $site_url = 'http://apilayer.net/api/validate';
    public $country_code = 'BY';
    public $format = 1;

    public function validate($value, Constraint $constraint)
    {
        if (!$this->numverifyValidate($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }

    private function numverifyValidate(string $phone) :bool
    {
        $handle = curl_init();

        $postData = [
            'access_key' => $this->secret_key,
            'number' => $phone,
            'country_code' => $this->country_code,
            'format' => $this->format,
        ];

        curl_setopt_array($handle, [
            CURLOPT_URL => $this->site_url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $data = curl_exec($handle);

        curl_close($handle);

        $result = json_decode($data, true);

        return $result['success'];
    }
}