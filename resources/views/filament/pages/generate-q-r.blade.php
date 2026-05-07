<x-filament-panels::page>
    <div class="text-center">
        @if($this->qrCode)
        <div class="mb-4">
            <p class="text-lg font-semibold">
                QR Code untuk Tanggal: {{ $this->tanggal }}
            </p>
            <div class="mx-auto mt-4" style="width: 300px; height: 300px;">
                {!! $this->qrCode !!}
            </div>
            <p class="mt-2 text-sm text-gray-500">
                Token: {{ $this->token }}
            </p>
        </div>
        @else
        <div class="py-12">
            <p class="text-gray-500">
                Klik tombol <strong>Generate QR Hari Ini</strong> untuk membuat QR Code absensi.
            </p>
        </div>
        @endif
    </div>
</x-filament-panels::page>
