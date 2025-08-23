<?php

namespace App\Jobs;

use App\Models\KepegawaianUniversitas\Usulan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateUsulanReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $usulan;

    public $tries = 2;
    public $timeout = 180; // 3 minutes for PDF generation

    public function __construct(Usulan $usulan)
    {
        $this->usulan = $usulan;
        $this->onQueue('reports');
    }

    public function handle(): void
    {
        try {
            Log::info('Generating report for usulan', ['usulan_id' => $this->usulan->id]);

            // Load relationships
            $this->usulan->load(['pegawai', 'jabatanLama', 'jabatanTujuan', 'periodeUsulan']);

            // Generate PDF
            $pdf = PDF::loadView('reports.usulan', [
                'usulan' => $this->usulan,
                'pegawai' => $this->usulan->pegawai,
                'dataUsulan' => $this->usulan->data_usulan
            ]);

            // Save PDF
            $filename = 'usulan-' . $this->usulan->id . '-' . now()->format('YmdHis') . '.pdf';
            $path = 'reports/usulan/' . $this->usulan->pegawai_id . '/' . $filename;

            Storage::disk('local')->put($path, $pdf->output());

            // Update usulan with report path
            $dataUsulan = $this->usulan->data_usulan;
            $dataUsulan['report_path'] = $path;
            $dataUsulan['report_generated_at'] = now()->toDateTimeString();

            $this->usulan->update(['data_usulan' => $dataUsulan]);

            Log::info('Report generated successfully', [
                'usulan_id' => $this->usulan->id,
                'path' => $path
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate report', [
                'usulan_id' => $this->usulan->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
