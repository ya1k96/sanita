<?php

namespace MyVendor\HorizonNotifier\Console;

use Illuminate\Console\Command;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Illuminate\Support\Facades\Mail;

class CheckHorizonStatusCommand extends Command
{
    protected $signature = 'horizon:check-status';
    protected $description = 'Check if Horizon is running and notify if it is down';
    public function handle()
    {
        if ($this->isHorizonActive()) {
            $this->info('Horizon is running correctly.');
        } else {
            $this->notify("Horizon está caído.");
            $this->error('Horizon is not running!');
        }
    }

    protected function isHorizonActive() : bool
    {
        if (! class_exists(MasterSupervisorRepository::class)) {
            return false; // Horizon no está instalado.
        }
    
        if (! $masters = app(MasterSupervisorRepository::class)->all()) {
            return false; // No hay supervisores maestros.
        }
    
        return collect($masters)->some(fn($master) => $master->status !== 'paused');
    }
    private function notify(string $message)
    {
        // Aquí puedes usar un sistema de notificación.
        $channel = config('sanita-config.notification_channel', 'email');
        if ($channel === 'email') {
            $recipients = config('sanita-config.notification_recipients.email', []);
            foreach ($recipients as $email) {
                Mail::raw($message, function ($mail) use ($email) {
                    $mail->to($email)->subject('Alerta: Horizon Caído');
                });
            }
        }

        // Similar para Slack u otros canales.
    }
}
