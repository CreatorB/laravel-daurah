<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\QrToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class QrController extends Controller
{
    public function download($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        $url = url('/proses-scan/' . $eventId . '?type=static');
        $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($url);
        
        $filename = 'QR_' . Str::slug($event->nama_event) . '_' . date('Ymd') . '.png';
        
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $imageData = file_get_contents($qrImage);
        echo $imageData;
        exit;
    }
}
