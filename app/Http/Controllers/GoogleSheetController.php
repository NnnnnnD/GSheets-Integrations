<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Sheets;
use App\Models\Magang;

class GoogleSheetController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setAuthConfig(storage_path(env('GOOGLE_CREDENTIALS_FILE')));
        $this->client->setRedirectUri(route('import.callback'));
        $this->client->setAccessType('offline'); // Allows for refreshing access tokens
        $this->client->setIncludeGrantedScopes(true);
        $this->client->addScope('https://www.googleapis.com/auth/drive.readonly');
        $this->client->addScope('https://www.googleapis.com/auth/spreadsheets.readonly');
    }

    public function importView()
    {
        $authUrl = $this->client->createAuthUrl();
        return view('import', compact('authUrl'));
    }

    public function importCallback(Request $request)
    {
        $this->client->authenticate($request->code);
        $token = $this->client->getAccessToken();

        // Save the token to the user session or database for future use
        session(['google_token' => $token]);

        return redirect()->route('import.data');
    }

    public function importData()
    {
        $this->client->setAccessToken(session('google_token'));

        $drive = new Drive($this->client);
        $files = $drive->files->listFiles([
            'q' => "mimeType='application/vnd.google-apps.spreadsheet'",
            'fields' => 'files(id, name)'
        ]);

        return view('importData', compact('files'));
    }

    public function fetchAndInsertData(Request $request){

        $this->client->setAccessToken(session('google_token'));

        $sheetId = $request->input('sheet_id'); // Assuming you capture sheet ID from user input or session

        try {
            // Fetch sheet metadata to get all available sheets
            $sheetService = new Sheets($this->client);
            $spreadsheet = $sheetService->spreadsheets->get($sheetId);
            $sheets = $spreadsheet->getSheets();

            // Assuming the user selects a sheet from the available options
            // For simplicity, this example assumes the first sheet in the list
            $sheetName = $sheets[0]->properties->title;

            // Fetch data from the selected sheet dynamically
            $response = $sheetService->spreadsheets_values->get($sheetId, $sheetName.'!A1:Z');

            $rows = $response->getValues();

            if (count($rows) > 1) { // Skip the header row
                foreach ($rows as $row) {
                    $mulai_magang = $this->convertDateFormat($row[5]);
                    $selesai_magang = $this->convertDateFormat($row[6]);

                    if ($mulai_magang === null || $selesai_magang === null) {
                        continue;
                    }

                    // Extract file IDs from Google Drive URLs
                    $surat_rujukan_id = $this->extractFileId($row[7]);
                    $surat_permohonan_magang_id = $this->extractFileId($row[8]);

                    // Upload the files and get their URLs
                    $surat_rujukan = $this->uploadFile($surat_rujukan_id);
                    $surat_permohonan_magang = $this->uploadFile($surat_permohonan_magang_id);

                    // Check if a record with the same nama and nim already exists
                    Magang::updateOrCreate(
                        ['nama' => $row[1], 'nim' => $row[2]],
                        [
                            'timestamp' => $row[0],
                            'sekolah_universitas' => $row[3],
                            'jurusan_prodi' => $row[4],
                            'mulai_magang' => $mulai_magang,
                            'selesai_magang' => $selesai_magang,
                            'surat_rujukan' => $surat_rujukan,
                            'surat_permohonan_magang' => $surat_permohonan_magang,
                        ]
                    );
                }

                return redirect()->route('import.view')->with('success', 'Data imported successfully!');
            } else {
                return redirect()->route('import.view')->with('error', 'No data found in the specified range.');
            }
        } catch (\Google\Service\Exception $e) {
            // Log or handle the exception
            dd($e->getMessage()); // Output the exact error message for debugging
            return redirect()->route('import.view')->with('error', 'Failed to fetch data: ' . $e->getMessage());
        }
    }


    // Utility function to extract file ID from Google Drive URL
    private function extractFileId($url){
        // Extract file ID from URL
        $url_parts = parse_url($url);
        parse_str($url_parts['query'], $query_params);
        return $query_params['id'] ?? null;
    }

    // Utility function to convert date format
    private function convertDateFormat($date){
        $dateTime = \DateTime::createFromFormat('n/j/Y', $date); // Adjust format as per your Google Sheet date format
        return $dateTime ? $dateTime->format('Y-m-d') : null;
    }


    private function uploadFile($fileUrl){
        if ($fileUrl) {
            return $fileUrl;
        }
        return null;
    }
}

