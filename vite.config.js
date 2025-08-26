import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pegawai/index.js',
                'resources/js/pegawai/pegawai-profil.js',
                'resources/js/pegawai/pegawai-usulan.js',
                'resources/js/kepegawaian-universitas/index.js',
                'resources/js/kepegawaian-universitas/kepegawaian-universitas.js',
                'resources/js/kepegawaian-universitas/data-pegawai.js',
                'resources/js/kepegawaian-universitas/jabatan.js',
                'resources/js/kepegawaian-universitas/documents.js',
                'resources/js/kepegawaian-universitas/personal-data.js',
                'resources/js/kepegawaian-universitas/employment-data.js',
                'resources/js/kepegawaian-universitas/dosen-data.js',
                'resources/js/kepegawaian-universitas/periode-usulan.js',
                'resources/js/admin-fakultas/index.js',
                'resources/js/admin-fakultas/admin-fakultas.js',
                'resources/js/penilai/index.js',
                'resources/js/penilai/penilai-universitas.js',
                'resources/js/shared/utils.js'
            ],
            refresh: [ // Menjadi sebuah array path
                'resources/views/**',
                'routes/**',
            ],
        }),
    ],
    server: {
        host: true,
        strictPort: true,
        port: 5174,
        hmr: {
            host: 'localhost',
        },
    },
});
