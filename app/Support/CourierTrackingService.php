<?php

namespace App\Support;

/**
 * Opsional: sambungkan ke RajaOngkir / API kurir. Tanpa kunci ENV, hanya mengembalikan URL pelacakan generik.
 */
class CourierTrackingService
{
    public function __construct(
        protected ?string $rajaongkirKey = null
    ) {
        $this->rajaongkirKey = $this->rajaongkirKey ?? config('services.rajaongkir.key');
    }

    /** URL pelacakan publik berdasarkan kurir + resi (heuristik sederhana). */
    public function publicTrackingUrl(?string $courier, ?string $trackingNumber): ?string
    {
        if (!$trackingNumber) {
            return null;
        }
        $c = strtolower((string) $courier);
        $q = urlencode($trackingNumber);

        return match (true) {
            str_contains($c, 'jne') => 'https://www.jne.co.id/id/tracking/trace/' . $q,
            str_contains($c, 'jnt') => 'https://jet.co.id/track?awb=' . $q,
            str_contains($c, 'sicepat') => 'https://www.sicepat.com/checkAwb/' . $q,
            str_contains($c, 'anteraja') => 'https://anteraja.id/tracking/' . $q,
            default => 'https://www.google.com/search?q=cek+resi+' . $q,
        };
    }
}
