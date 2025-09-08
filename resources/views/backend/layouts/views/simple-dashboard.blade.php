<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - Kepegawaian UNMUL</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-800 mb-4">
                    {{ $title ?? 'Dashboard' }}
                </h1>

                <div class="mb-4">
                    <p class="text-gray-600">Selamat datang, <strong>{{ $user->nama_lengkap ?? 'User' }}</strong></p>
                    <p class="text-sm text-gray-500">Role: {{ $user->getRoleNames()->first() ?? 'No Role' }}</p>
                </div>

                @if(isset($error))
                    <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
                        {{ $error }}
                    </div>
                @endif

                <div class="space-y-2">
                    <div class="text-green-600">✓ Dashboard dapat diakses</div>
                    <div class="text-gray-500">Waktu: {{ now()->format('Y-m-d H:i:s') }}</div>
                </div>

                <div class="mt-6">
                    <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-800">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
