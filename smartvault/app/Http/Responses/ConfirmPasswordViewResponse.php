<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Laravel\Fortify\Contracts\ConfirmPasswordViewResponse as ConfirmPasswordViewResponseContract;

class ConfirmPasswordViewResponse implements ConfirmPasswordViewResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return view('auth.confirm-password');
    }
}


