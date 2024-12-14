<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    public function getBase64File($base64)
    {
        $file = preg_replace('/^data:[a-zA-Z0-9\/\+\-]+;base64,/', '', $base64);

        return $file;
    }

    public function getBase64FileExtension($base64)
    {
        $mime_type = mime_content_type($base64);
        $extension = explode('/', $mime_type)[1];

        return $extension;
    }

    public function getBase64FileName($base64)
    {
        $extension = $this->getBase64FileExtension($base64);

        $filename = now()->format('dmYHis').'.'.$extension;

        return $filename;
    }
}
