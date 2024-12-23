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

    /**
     * Get time difference in specified format
     *
     * @param  \Carbon\Carbon  $time1
     * @param  \Carbon\Carbon|null  $time2
     */
    public function formatTimeDiff($time1, $time2 = null): string
    {
        $time2 = $time2 ?: now();

        $diff_in_days = $time1->diffInDays($time2);

        if ($diff_in_days < 1) {
            return $time1->format('h:i A');
        } elseif ($diff_in_days < 2) {
            return 'Yesterday';
        } elseif ($diff_in_days < 7) {
            return $time1->format('l');
        } else {
            return $time1->format('d/m/Y');
        }
    }
}
