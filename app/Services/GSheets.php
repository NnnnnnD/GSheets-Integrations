<?php

namespace App\Services;

use Google\Client as Google_Client;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Service\Drive as Google_Service_Drive;

class GSheets
{
    protected $client;
    protected $sheetsService;
    protected $driveService;

    public function __construct()
{
    $credentialsFilePath = config('google.credentials_file');
    if (!file_exists($credentialsFilePath)) {
        throw new \Exception("Credentials file does not exist at path: $credentialsFilePath");
    }

    // Debugging line
    dd($credentialsFilePath);

    $this->client = new Google_Client();
    $this->client->setAuthConfig($credentialsFilePath);
    $this->client->setScopes([
        Google_Service_Sheets::SPREADSHEETS,
        Google_Service_Drive::DRIVE,
    ]);
    $this->client->setAccessType('offline');

    $this->sheetsService = new Google_Service_Sheets($this->client);
    $this->driveService = new Google_Service_Drive($this->client);
}


    public function listSheets()
    {
        $files = $this->driveService->files->listFiles([
            'q' => "mimeType='application/vnd.google-apps.spreadsheet'",
        ]);

        return $files->getFiles();
    }

    public function getSheetData($spreadsheetId, $range = 'Sheet1')
    {
        $response = $this->sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        return $response->getValues();
    }
}
