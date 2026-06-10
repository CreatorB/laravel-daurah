<?php

namespace App\Services;

class GeolocationService
{
    const EARTH_RADIUS = 6371000;

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return null;
        }

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS * $c;
    }

    public function isWithinRadius($userLat, $userLon, $targetLat, $targetLon, $radiusMeters)
    {
        $distance = $this->calculateDistance($userLat, $userLon, $targetLat, $targetLon);
        
        if ($distance === null) {
            return false;
        }
        
        return $distance <= $radiusMeters;
    }

    public function formatDistance($meters)
    {
        if ($meters < 1000) {
            return round($meters) . ' m';
        }
        
        return round($meters / 1000, 2) . ' km';
    }
}
