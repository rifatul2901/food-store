<?php

namespace App\Filament\Pages;

use App\Models\QrSession;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQR extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static string $view = 'filament.pages.generate-q-r';
    protected static ?string $navigationLabel = 'Generate QR Absensi';
    protected static ?string $title = 'Generate QR Absensi';

    public ?string $qrCode = null;
    public ?string $token = null;
    public ?string $tanggal = null;

    public function generateQR(): void
    {
        $today = now()->toDateString();

        // Kalau QR hari ini sudah ada, pakai yang lama
        $existing = QrSession::where('date', $today)->first();

        if ($existing) {
            $this->token = $existing->token;
        } else {
            $this->token = Str::random(32);
            QrSession::create([
                'token' => $this->token,
                'date'  => $today,
            ]);
        }

        $this->tanggal = $today;

        $this->qrCode = QrCode::format('svg')->size(300)->generate($this->token);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate QR Hari Ini')
                ->icon('heroicon-o-qr-code')
                ->color('success')
                ->action('generateQR'),
        ];
    }
}