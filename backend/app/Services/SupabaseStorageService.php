<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    public function uploadDepositProof(UploadedFile $file, string $userId): ?string
    {
        $base = rtrim((string) config('services.supabase.url'), '/');
        $key = config('services.supabase.service_role_key');
        $bucket = (string) config('services.supabase.storage_bucket');

        if ($base === '' || ! is_string($key) || $key === '') {
            Log::warning('supabase: missing url or service role key');

            return null;
        }

        $path = 'deposits/'.$userId.'/'.Str::uuid().'.'.$file->getClientOriginalExtension();

        $client = new Client(['timeout' => 60]);
        $url = $base.'/storage/v1/object/'.$bucket.'/'.$path;

        try {
            $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$key,
                    'Content-Type' => $file->getMimeType() ?? 'application/octet-stream',
                    'x-upsert' => 'true',
                ],
                'body' => fopen($file->getRealPath(), 'r'),
            ]);

            return $base.'/storage/v1/object/public/'.$bucket.'/'.$path;
        } catch (\Throwable $e) {
            Log::error('supabase.upload: '.$e->getMessage());

            return null;
        }
    }

    public function publicUrlForPath(string $pathInBucket): string
    {
        $base = rtrim((string) config('services.supabase.url'), '/');
        $bucket = (string) config('services.supabase.storage_bucket');

        return $base.'/storage/v1/object/public/'.$bucket.'/'.ltrim($pathInBucket, '/');
    }
}
