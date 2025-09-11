<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kepegawaian UNMUL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .captcha-container {
            background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
            border: 2px solid #d1d5db;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            letter-spacing: 2px;
            text-shadow: 0.5px 0.5px 1px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .captcha-container.animate {
            transform: scale(1.05);
            opacity: 0.9;
        }
        .refresh-captcha {
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.2s ease;
        }
        .refresh-captcha:hover {
            transform: rotate(180deg);
            background-color: #d1d5db;
        }
        body {
            background-image: url('images/bg-unmul.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .form-container {
            animation: slideInLeft 0.5s ease-out;
        }
        .welcome-container {
            animation: slideInRight 0.5s ease-out;
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            transform: translateX(5px);
        }
        .submit-button {
            transition: all 0.3s ease;
        }
        .submit-button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 bg-gray-100/50">

    <main class="w-full max-w-5xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col lg:flex-row">

        <div class="w-full lg:w-1/2 p-6 sm:p-8 md:p-10 bg-white/95 form-container">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Halaman Login</h1>

            @error('nip')
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <p>{{ $message }}</p>
                </div>
            @enderror

            <form action="{{ route('login') }}" method="POST" onsubmit="return validateCaptcha(event)">
                @csrf
                <div class="mb-4">
                    <input type="text" name="nip" placeholder="NIP" required value="{{ old('nip') }}"
                           class="input-field w-full rounded-lg border border-gray-300 bg-gray-50 p-3 text-gray-800 placeholder-gray-500 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition duration-200">
                </div>

                <div class="mb-4">
                    <input type="password" name="password" placeholder="Password" required
                           class="input-field w-full rounded-lg border border-gray-300 bg-gray-50 p-3 text-gray-800 placeholder-gray-500 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition duration-200">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Verifikasi CAPTCHA</label>
                    <div class="flex items-center gap-3">
                        <div id="captcha-display" class="captcha-container px-4 py-2 rounded-lg text-lg text-gray-800 text-center min-w-[100px] sm:min-w-[120px]"></div>
                        <button type="button" onclick="generateCaptcha()"
                                class="refresh-captcha p-2 bg-gray-100 hover:bg-gray-200 rounded-lg border border-gray-300 flex items-center justify-center"
                                title="Refresh CAPTCHA">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <polyline points="1 20 1 14 7 14"></polyline>
                                <path d="m3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                            </svg>
                        </button>
                    </div>
                    <input type="text" id="captcha-input" name="captcha" placeholder="Masukkan hasil perhitungan" required
                           class="input-field mt-3 w-full rounded-lg border border-gray-300 bg-gray-50 p-3 text-gray-800 placeholder-gray-500 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition duration-200"
                           autocomplete="off">
                    <div id="captcha-error" class="mt-2 text-red-600 text-sm hidden">
                        CAPTCHA tidak valid. Silakan coba lagi.
                    </div>
                </div>

                <button type="submit" class="submit-button w-full rounded-lg bg-gray-800 py-3 text-white font-semibold hover:bg-yellow-500 hover:text-gray-900 transition duration-200">
                    SIGN IN
                </button>
            </form>
        </div>

        <div style="background-image: url('images/logo-unmul.png');"
             class="hidden lg:flex lg:w-1/2 bg-contain bg-center bg-no-repeat relative items-center justify-center p-8 bg-yellow-400/95 rounded-tr-2xl rounded-br-2xl welcome-container">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-400/80 to-yellow-600/80 rounded-tr-2xl rounded-br-2xl"></div>
            <div class="relative z-10 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                    Selamat Datang di Website Kepegawaian Universitas Mulawarman
                </h2>
            </div>
        </div>
    </main>

    <script>
        let captchaAnswer = 0;
        function generateCaptcha() {
            const num1 = Math.floor(Math.random() * 20) + 1;
            const num2 = Math.floor(Math.random() * 20) + 1;
            const operators = ['+', '-', '*'];
            const operator = operators[Math.floor(Math.random() * operators.length)];
            let question, answer;
            switch(operator) {
                case '+': question = `${num1} + ${num2}`; answer = num1 + num2; break;
                case '-': const larger = Math.max(num1, num2); const smaller = Math.min(num1, num2); question = `${larger} - ${smaller}`; answer = larger - smaller; break;
                case '*': const smallNum1 = Math.floor(Math.random() * 10) + 1; const smallNum2 = Math.floor(Math.random() * 10) + 1; question = `${smallNum1} × ${smallNum2}`; answer = smallNum1 * smallNum2; break;
            }
            captchaAnswer = answer;
            const captchaDisplay = document.getElementById('captcha-display');
            captchaDisplay.textContent = question + ' = ?';
            captchaDisplay.classList.add('animate');
            setTimeout(() => captchaDisplay.classList.remove('animate'), 300);
            document.getElementById('captcha-input').value = '';
            document.getElementById('captcha-error').classList.add('hidden');
        }
        function validateCaptcha(event) {
            const userAnswer = parseInt(document.getElementById('captcha-input').value);
            if (userAnswer !== captchaAnswer) {
                event.preventDefault();
                document.getElementById('captcha-error').classList.remove('hidden');
                generateCaptcha();
                return false;
            }
            return true;
        }
        document.addEventListener('DOMContentLoaded', generateCaptcha);
        document.getElementById('captcha-input').addEventListener('input', function() {
            document.getElementById('captcha-error').classList.add('hidden');
        });
    </script>
</body>
</html>
