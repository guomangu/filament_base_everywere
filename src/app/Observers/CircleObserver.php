<?php

namespace App\Observers;

use App\Models\Circle;
use Illuminate\Support\Facades\Http;

class CircleObserver
{
    /**
     * Handle the Circle "saving" event.
     */
    public function saving(Circle $circle): void
    {
        // Only geocode if address changed or coordinates are missing/default
        if ($circle->isDirty('address') || empty($circle->coordinates) || ($circle->coordinates['lat'] == 0 && $circle->coordinates['lng'] == 0)) {
            $this->geocode($circle);
        }
    }

    /**
     * Geocode the circle address using Nominatim.
     */
    protected function geocode(Circle $circle): void
    {
        if (trim(strtolower($circle->address)) === 'remote') {
            $circle->coordinates = ['lat' => 0, 'lng' => 0];
            return;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Antigravity-TrustCircle/1.0'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $circle->address,
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response->successful() && count($response->json()) > 0) {
                $data = $response->json()[0];
                $circle->coordinates = [
                    'lat' => (float) $data['lat'],
                    'lng' => (float) $data['lon'],
                ];
            } else {
                // If not normalized/found, fallback to Remote
                $circle->address = 'Remote';
                $circle->coordinates = ['lat' => 0, 'lng' => 0];
            }
        } catch (\Exception $e) {
            // Fallback to Remote on error
            $circle->address = 'Remote';
            $circle->coordinates = ['lat' => 0, 'lng' => 0];
            \Log::error('Geocoding error for circle ' . $circle->id . ': ' . $e->getMessage());
        }
    }
}
