<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penilai Universitas - Kepegawaian UNMUL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-800 mb-4">
                    Dashboard Penilai Universitas
                </h1>
                
                <div class="mb-4">
                    <p class="text-gray-600">Selamat datang, <strong>{{ Auth::user()->nama_lengkap ?? 'Penilai' }}</strong></p>
                    <p class="text-sm text-gray-500">Role: {{ Auth::user()->getRoleNames()->first() ?? 'Penilai Universitas' }}</p>
                </div>
                
                <div class="space-y-2">
                    <div class="text-green-600">✓ Dashboard Penilai Universitas dapat diakses</div>
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