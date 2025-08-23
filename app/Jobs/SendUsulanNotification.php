<?php

namespace App\Jobs;

use App\Models\KepegawaianUniversitas\Usulan;
use App\Mail\UsulanSubmitted;
use App\Mail\UsulanStatusChanged;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendUsulanNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $usulan;
    protected $type;
    protected $oldStatus;

    public $tries = 3;
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(Usulan $usulan, string $type = 'submitted', $oldStatus = null)
    {
        $this->usulan = $usulan;
        $this->type = $type;
        $this->oldStatus = $oldStatus;
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $pegawai = $this->usulan->pegawai;

            switch ($this->type) {
                case 'submitted':
                    Mail::to($pegawai->email)
                        ->send(new UsulanSubmitted($this->usulan));
                    break;

                case 'status_changed':
                    Mail::to($pegawai->email)
                        ->send(new UsulanStatusChanged($this->usulan, $this->oldStatus));
                    break;

                default:
                    Log::warning('Unknown notification type', ['type' => $this->type]);
            }

            Log::info('Notification sent successfully', [
                'usulan_id' => $this->usulan->id,
                'type' => $this->type,
                'to' => $pegawai->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'usulan_id' => $this->usulan->id,
                'type' => $this->type,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
