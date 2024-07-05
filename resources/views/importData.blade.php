<!DOCTYPE html>
<html>
<head>
    <title>Select Google Sheet</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
</head>
<body>
    <h1>Select a Google Sheet</h1>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastr.success('{{ session('success') }}');
            });
        </script>
    @endif
    <form action="{{ route('fetch.and.insert') }}" method="POST">
        @csrf
        <select name="sheet_id">
            @foreach($files as $file)
                <option value="{{ $file->id }}">{{ $file->name }}</option>
            @endforeach
        </select>
        <button type="submit">Import Data</button>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>
</html>
