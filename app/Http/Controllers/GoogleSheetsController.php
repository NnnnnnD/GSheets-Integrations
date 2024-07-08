<?php

// app/Http/Controllers/GoogleSheetsController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google\Service\Sheets as Google_Service_Sheets;
use App\Models\Magang; // Replace with your actual model

class GoogleSheetsController extends Controller
{
    private $client;

    public function __construct(){
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path(env('GOOGLE_CREDENTIALS_FILE')));
        $this->client->setScopes([
            Google_Service_Sheets::SPREADSHEETS_READONLY,
            'https://www.googleapis.com/auth/drive.file'
        ]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }

    public function import()
    {
        return view('import');
    }

    public function auth()
    {
        if (!session()->has('access_token')) {
            $authUrl = $this->client->createAuthUrl();
            return redirect($authUrl);
        }
        return redirect()->route('import');
    }

    public function authCallback(Request $request)
    {
        if ($request->has('code')) {
            $token = $this->client->fetchAccessTokenWithAuthCode($request->code);
            session(['access_token' => $token]);
            return redirect()->route('import');
        }

        return abort(403, 'Unauthorized');
    }

    public function fetchSheetData(Request $request)
    {
        if (!$request->session()->has('access_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $accessToken = $request->session()->get('access_token');
        $this->client->setAccessToken($accessToken);

        $sheets = new Google_Service_Sheets($this->client);
        $fileId = $request->input('fileId');
        $response = $sheets->spreadsheets_values->get($fileId, 'Sheet1'); // Adjust range if necessary

        $values = $response->getValues();

        if (!empty($values)) {
            foreach ($values as $row) {
                Magang::updateOrCreate(
                    [
                        'nama' => $row[0],
                        'nim' => $row[1],
                        'sekolah_universitas' => $row[2],
                        'jurusan_prodi' => $row[3],
                        'mulai_magang' => $row[4],
                        'selesai_magang' => $row[5],
                        'surat_rujukan' => $row[6],
                        'surat_permohonan_magang' => $row[7],
                    ]
                );
            }
        }

        return response()->json(['success' => 'Data imported successfully']);
    }
}


