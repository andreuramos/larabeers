<?php

namespace Larabeers\External\Images\Uploader;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Larabeers\Entities\Image;
use Symfony\Component\HttpFoundation\Cookie;

class GoogleDriveImageUploader implements ImageUploader
{
    const UPLOAD_URI = "/upload/drive/v3/files";
    const UPLOAD_TYPE = "uploadType=media";

    // Client ID:   299417665438-c8ec3gi99j01k1nk6gmcpqolp7grenhb.apps.googleusercontent.com
    // Client Secret:   VA0JhzA1ZrV5oF-pZTs6gk4D
    // API key:  AIzaSyAZWtJrhYfhFM3Jz5sJRPm1xUnCGgKcu-4

    private string $access_token;
    private Google_Service_Drive $google_drive_service;

    public function __construct()
    {
        $client = $this->getGoogleClient();
        $this->authenticate($client);
        $this->google_drive_service = new Google_Service_Drive($client);
    }

    private function authenticate(Google_Client $client)
    {
        // if there is no cookie throw an exception
        // else, use it to refresh the access token
        // if the refresh fails, throw an exception too
        // finally, instantiate google drive service
    }

    public function upload(string $image_path): Image
    {
        $file_metadata = new Google_Service_Drive_DriveFile([
            'name' => 'image_name'
        ]);
        $content = file_get_contents($image_path);
        $file = $this->google_drive_service->files->create($file_metadata, [
            'data' => $content,
            'mimeType' => mime_content_type($image_path),
            'uploadType' => 'media',
            'fields' => 'id' // TODO: probably public url too
        ]);

        $image_path = new Image();
        $image_path->url = $file->url; // TODO: ??
    }

    private function getGoogleClient(): Google_Client
    {
        $client = new Google_Client();
        $client->setApplicationName('Larabeers');
        $client->setDeveloperKey(env('GOOGLE_DRIVE_API_KEY'));
        $client->setClientId(env('GOOGLE_API_APP_ID'));
        $client->setClientSecret(env('GOOGLE_API_SECRET'));
        $client->setScopes(Google_Service_Drive::DRIVE_METADATA_READONLY); //TODO: add approriate scopes

        $refresh_token = ""; //Cookie::get('google_refresh_token');// TODO: Extract this to an external Cookie manager class (singleton?)
        $client->fetchAccessTokenWithRefreshToken($refresh_token);


        return $client;
    }
}
