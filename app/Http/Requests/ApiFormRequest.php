<?php

namespace App\Http\Requests;

use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;


class ApiFormRequest extends FormRequest
{
    use ApiResponseTrait;

    protected function failedValidation(Validator $validator):JsonResponse
    {
        return throw new HttpResponseException(
            $this->error('Unsuccessfully',array($validator->getMessageBag()),400));
    }
}
