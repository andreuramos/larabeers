<?php

namespace Larabeers\External\Images\Uploader;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Larabeers\Entities\Image;

class GoogleDriveImageUploader implements ImageUploader
{
    const PUBLIC_FILE_URL = 'https://drive.google.com/uc?id=';

    private $client;

    public function __construct()
    {
        $this->client = $this->getGoogleClient();
    }

    public function upload(string $image_path): Image
    {
        $google_drive_service = new Google_Service_Drive($this->authenticate($this->client));

        $file_metadata = new Google_Service_Drive_DriveFile([
            'name' => 'larabeers_' . uniqid()
        ]);
        $content = file_get_contents($image_path);
        $file = $google_drive_service->files->create($file_metadata, [
            'data' => $content,
            'mimeType' => mime_content_type($image_path),
            'uploadType' => 'media',
            'fields' => 'id'
        ]);

        $this->setPublicAccess($google_drive_service, $file);

        $public_url = self::PUBLIC_FILE_URL . $file->id;

        $image_path = new Image();
        $image_path->url = $public_url;

        return $image_path;
    }

    private function getGoogleClient(): Google_Client
    {
        $client = new \Google_Client();
        $client->setApplicationName('Larabeers');
        $client->setScopes(Google_Service_Drive::DRIVE_FILE);
        $client->setClientId(env('GOOGLE_API_APP_ID'));
        $client->setClientSecret(env('GOOGLE_API_SECRET'));
        $client->setAccessType('offline');
        $client->setRedirectUri(url('/dashboard/settings/google_auth_comeback'));

        return $client;
    }

    private function authenticate(Google_Client $client): Google_Client
    {
        $encrypted_token = Auth::user()->google_refresh_token; //TODO: decouple this from Auth
        if (!$encrypted_token) {
            throw new \Exception("Google account not connected");
        }
        $refresh_token = Crypt::decrypt($encrypted_token);
        $access_token = $client->fetchAccessTokenWithRefreshToken($refresh_token);

        $client->setAccessToken($access_token);

        return $client;
    }

    /**
     * @param Google_Service_Drive $google_drive_service
     * @param Google_Service_Drive_DriveFile $file
     */
    private function setPublicAccess(Google_Service_Drive $google_drive_service, Google_Service_Drive_DriveFile $file): void
    {
        $permission = new \Google_Service_Drive_Permission();
        $permission->role = "reader";
        $permission->type = "anyone";
        $google_drive_service->permissions->create($file->id, $permission);
    }
}
