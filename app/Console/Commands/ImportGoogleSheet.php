<?php

namespace App\Console\Commands;

use App\Models\GSheets;
use App\Models\GSheetsAPI;
use Illuminate\Console\Command;
use Google\Client as Google_Client;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Service\Drive as Google_Service_Drive;

class ImportGoogleSheet extends Command
{
    protected $signature = 'import:sheet';
    protected $description = 'Import data from Google Sheets into the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API Laravel');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY, Google_Service_Drive::DRIVE_READONLY]);
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $client->setAccessType('offline');

        $service = new Google_Service_Sheets($client);
        $driveService = new Google_Service_Drive($client);

        // Prompt the user to enter the Google Sheet ID
        $sheetId = $this->ask('Please enter the Google Sheet ID');

        $response = $service->spreadsheets->get($sheetId);
        $sheetName = $response->sheets[0]->properties->title;

        $range = $sheetName . '!A:Z'; // Adjust range based on your sheet structure
        $response = $service->spreadsheets_values->get($sheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            $this->error('No data found.');
        } else {
            foreach ($values as $row) {
                // Adjust column indexes based on your database structure
                GSheetsAPI::table('your_table')->insert([
                    'column1' => $row[0],
                    'column2' => $row[1],
                    // Add more columns as needed
                ]);
            }
            $this->info('Data imported successfully!');
        }
    }
}
