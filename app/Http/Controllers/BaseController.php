<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GetStream\StreamChat\Client;

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

    public function upsertChatUser(mixed $user): void
    {
        $chat_client = new Client(config('app.stream_chat.key'), config('app.stream_chat.secret'));

        $chat_client->upsertUsers([
            [
                'id' => (string) $user->id,
                'name' => $user->name,
                'role' => 'user',
                'image' => $user->avatar['uri'],
            ],
        ]);
    }

    public function createChatToken($user_id): string
    {
        $chat_client = new Client(config('app.stream_chat.key'), config('app.stream_chat.secret'));
        $chat_token = $chat_client->createToken((string) $user_id);

        return $chat_token;
    }

    public function parseSearchDate($input)
    {
        try {
            Carbon::setLocale('en'); // Ensure proper month parsing
            $now = now()->setTimezone('Asia/Kuala_Lumpur');

            // Day and Month (e.g., "27 Jan")
            if (preg_match('/^\d{1,2} [a-zA-Z]+$/', $input)) {
                return Carbon::createFromFormat('d M Y', "{$input} ".$now->format('Y'), 'Asia/Kuala_Lumpur');
            }

            // Day and Year (e.g., "27 2024")
            if (preg_match('/^\d{1,2} \d{4}$/', $input)) {
                return Carbon::createFromFormat('d M Y', "{$input} ".$now->format('M'), 'Asia/Kuala_Lumpur');
            }

            // Month and Year (e.g., "Jan 2024")
            if (preg_match('/^[a-zA-Z]+ \d{4}$/', $input)) {
                return Carbon::createFromFormat('d M Y', "01 {$input}", 'Asia/Kuala_Lumpur');
            }

            // Just Day (e.g., "27")
            if (is_numeric($input)) {
                return Carbon::createFromFormat('d M Y', "{$input} ".$now->format('M Y'), 'Asia/Kuala_Lumpur');
            }

            // Just Month (e.g., "Jan")
            if (preg_match('/^[a-zA-Z]+$/', $input)) {
                return Carbon::createFromFormat('d M Y', "01 {$input} ".$now->format('Y'), 'Asia/Kuala_Lumpur');
            }

            // Just Year (e.g., "2024")
            if (preg_match('/^\d{4}$/', $input)) {
                return Carbon::createFromFormat('Y-m-d', "{$input}-01-01", 'Asia/Kuala_Lumpur');
            }

            // Default: Parse the input directly
            return Carbon::parse($input, 'Asia/Kuala_Lumpur');
        } catch (\Exception $e) {
            return null;
        }
    }
}
