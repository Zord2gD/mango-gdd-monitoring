<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KebunSiapPanenNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $kebun;

    /**
     * Create a new notification instance.
     */
    public function __construct($kebun)
    {
        $this->kebun = $kebun;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // We only use database notifications for UI
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'kebun_id' => $this->kebun->id,
            'nama_kebun' => $this->kebun->nama_kebun,
            'nama_petani' => $this->kebun->user->name ?? 'Petani',
            'total_gdd' => $this->kebun->total_gdd,
            'lokasi' => $this->kebun->lokasi,
            'pesan' => "Kebun {$this->kebun->nama_kebun} milik {$this->kebun->user->name} telah mencapai target kematangan ({$this->kebun->total_gdd} GDD) dan Siap Panen!"
        ];
    }
}
