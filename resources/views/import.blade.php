<!-- resources/views/import.blade.php -->
<div class="container">
    <h2>Import Data from Google Sheets</h2>
    <button id="auth-button" class="btn btn-primary">Connect to Google Sheets</button>
    <button id="picker-button" class="btn btn-secondary" style="display:none;">Select Google Sheet</button>
</div>

<script src="https://apis.google.com/js/api.js"></script>
<script>
    let developerKey = '{{ env('GOOGLE_API_KEY') }}';
    let clientId = '{{ env('GOOGLE_CLIENT_ID') }}';
    let scope = ['https://www.googleapis.com/auth/drive.file', 'https://www.googleapis.com/auth/spreadsheets.readonly'];
    let pickerApiLoaded = false;
    let oauthToken;

    function onApiLoad() {
        gapi.load('auth', {'callback': onAuthApiLoad});
        gapi.load('picker', {'callback': onPickerApiLoad});
    }

    function onAuthApiLoad() {
        document.getElementById('auth-button').addEventListener('click', function() {
            gapi.auth.authorize({
                'client_id': clientId,
                'scope': scope,
                'immediate': false
            }, handleAuthResult);
        });
    }

    function onPickerApiLoad() {
        pickerApiLoaded = true;
        createPicker();
    }

    function handleAuthResult(authResult) {
        if (authResult && !authResult.error) {
            oauthToken = authResult.access_token;
            document.getElementById('auth-button').style.display = 'none';
            document.getElementById('picker-button').style.display = 'inline';
            createPicker();
        }
    }

    function createPicker() {
        if (pickerApiLoaded && oauthToken) {
            var picker = new google.picker.PickerBuilder()
                .addView(google.picker.ViewId.SPREADSHEETS)
                .setOAuthToken(oauthToken)
                .setDeveloperKey(developerKey)
                .setCallback(pickerCallback)
                .build();
            picker.setVisible(true);
        }
    }

    function pickerCallback(data) {
        if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
            var fileId = data[google.picker.Response.DOCUMENTS][0].id;
            fetchSheetData(fileId);
        }
    }

    function fetchSheetData(fileId) {
        fetch('{{ route('fetchSheetData') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ fileId: fileId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data imported successfully');
            } else {
                alert('Error importing data');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    gapi.load('client:auth2', onApiLoad);
</script>
