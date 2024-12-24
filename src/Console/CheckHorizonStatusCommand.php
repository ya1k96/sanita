<?php

namespace Yamil\Sanita\Console;

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
    private function notify(string $message): void
    {
        // Obtener el canal de notificación desde tu configuración.
        $channel = config('sanita.notification_channel', 'email');

        if ($channel === 'email') {
            $this->notifyByEmail($message);
        } else {
            // Manejo de otros canales o advertencia si no se reconoce el canal.
            error_log("Canal de notificación desconocido: {$channel}");
        }
    }

    private function notifyByEmail(string $message): void
    {
        $recipients = config('sanita.notification_recipients.email', []);

        if (empty($recipients)) {
            error_log('No se han configurado destinatarios para notificaciones por email.');
            return;
        }

        foreach ($recipients as $email) {
            try {
                $this->sendEmail($email, 'Alerta: Horizon Caído', $message);
                error_log("Notificación enviada por email a: {$email}");
            } catch (\Exception $e) {
                error_log("Error al enviar email a {$email}: {$e->getMessage()}");
            }
        }
    }

    private function sendEmail(string $to, string $subject, string $body): void
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // Configuración del servidor SMTP usando configuraciones de Laravel
            $mail->isSMTP();
            $mail->Host       = config('mail.host'); // Obtiene el valor de MAIL_HOST
            $mail->SMTPAuth   = true;
            $mail->Username   = config('mail.username'); // Obtiene el valor de MAIL_USERNAME
            $mail->Password   = config('mail.password'); // Obtiene el valor de MAIL_PASSWORD
            $mail->SMTPSecure = config('mail.encryption'); // Obtiene el valor de MAIL_ENCRYPTION
            $mail->Port       = config('mail.port'); // Obtiene el valor de MAIL_PORT

            // Configuración del email
            $mail->setFrom(config('mail.from.address'), config('mail.from.name'));
            $mail->addAddress($to);
            $mail->isHTML(false); // Usa texto plano para los mensajes.
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            throw new \RuntimeException("Error al enviar email: {$e->getMessage()}");
        }
    }
}
