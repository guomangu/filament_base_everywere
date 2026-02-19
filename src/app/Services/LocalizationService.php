<?php

namespace App\Services;

use App\Models\Circle;
use App\Models\User;

class LocalizationService
{
    /**
     * Parse a Nominatim display_name or structured data into components
     */
    public static function parseAddress(string $address, ?array $raw = null): array
    {
        if ($raw) {
            return [
                'neighborhood' => $raw['address']['suburb'] ?? $raw['address']['neighbourhood'] ?? null,
                'city' => $raw['address']['city'] ?? $raw['address']['town'] ?? $raw['address']['village'] ?? null,
                'region' => $raw['address']['state'] ?? $raw['address']['region'] ?? null,
                'country' => $raw['address']['country'] ?? null,
                'full_address' => $raw['display_name'] ?? $address,
            ];
        }
        $parts = array_map('trim', explode(',', $address));
        $count = count($parts);

        $neighborhood = null;
        $city = null;
        $region = null;

        if ($count >= 5) {
            $neighborhood = $parts[1];
            $city = $parts[2];
            $region = $parts[3];
        } elseif ($count === 4) {
            $neighborhood = $parts[1];
            $city = $parts[2];
            $region = $parts[3] ?? null;
        } elseif ($count === 3) {
            $city = $parts[1];
            $region = $parts[2];
        } elseif ($count === 2) {
            $city = $parts[0];
            $region = $parts[1];
        } else {
            $city = $address;
        }

        return [
            'neighborhood' => $neighborhood,
            'city' => $city,
            'region' => $region,
            'country' => $parts[$count - 1] ?? null,
            'full_address' => $address,
        ];
    }

    /**
     * Find an existing circle or create one for a given user and address
     */
    public static function findOrCreateCircleForUser(User $user, string $address, ?array $raw = null): Circle
    {
        $parsed = self::parseAddress($address, $raw);
        
        $query = Circle::query();
        
        if ($parsed['neighborhood'] && $parsed['city']) {
            $query->where('city', $parsed['city'])->where('neighborhood', $parsed['neighborhood']);
        } elseif ($parsed['city']) {
            $query->where('city', $parsed['city'])->whereNull('neighborhood');
        } else {
            $query->where('address', $address);
        }

        $circle = $query->first();

        if ($circle) {
            $circle->addMember($user);
            return $circle;
        }

        $circle = Circle::create([
            'name' => ($parsed['neighborhood'] ?: $parsed['city']) ?: 'Mon Cercle',
            'address' => $parsed['full_address'],
            'city' => $parsed['city'],
            'neighborhood' => $parsed['neighborhood'],
            'region' => $parsed['region'],
            'country' => $parsed['country'],
            'owner_id' => $user->id,
            'type' => 'place',
            'coordinates' => ['lat' => 0, 'lng' => 0],
        ]);

        // Automatically add owner as active member
        $circle->addMember($user, 'admin', 'active');

        return $circle;
    }
}
