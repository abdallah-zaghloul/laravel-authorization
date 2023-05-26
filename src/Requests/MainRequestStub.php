<?php

namespace ZaghloulSoft\LaravelAuthorization\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use ZaghloulSoft\LaravelAuthorization\Traits\Response;

class MainRequestStub extends FormRequest
{
    use Response;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $this->error($validator->errors()->first(),$this->statusCodeBadRequest);
    }
}
