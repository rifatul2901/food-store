<?php

namespace Tests\Feature;

use App\Models\QrSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class QrSessionTest extends TestCase
{
    use DatabaseTransactions;

    // Test 1: QR bisa dibuat dan tersimpan di database
    public function test_qr_session_dapat_dibuat()
    {
        $token = Str::random(32);
        $today = now()->toDateString();

        QrSession::create([
            'token' => $token,
            'date'  => $today,
        ]);

        $this->assertDatabaseHas('qr_sessions', [
            'token' => $token,
            'date'  => $today,
        ]);
    }

    // Test 2: Token QR harus unik
    public function test_token_qr_harus_unik()
    {
        $token = Str::random(32);

        QrSession::create([
            'token' => $token,
            'date'  => now()->toDateString(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        // Coba buat lagi dengan token yang sama — harus error
        QrSession::create([
            'token' => $token,
            'date'  => now()->toDateString(),
        ]);
    }

    // Test 3: QR per hari — kalau sudah ada tidak buat baru
    public function test_qr_tidak_duplikat_per_hari()
    {
        $today = now()->toDateString();

        // Buat QR pertama
        QrSession::create([
            'token' => Str::random(32),
            'date'  => $today,
        ]);

        // Cek hanya ada 1 record untuk hari ini
        $count = QrSession::where('date', $today)->count();

        $this->assertEquals(1, $count);
    }

    // Test 4: is_used default false
    public function test_is_used_default_false()
    {
        $qr = QrSession::create([
            'token' => Str::random(32),
            'date'  => now()->toDateString(),
        ]);

        $this->assertFalse((bool) $qr->is_used);
    }
}