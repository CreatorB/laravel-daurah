<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor - {{ $event->nama_event }}</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { 
            background: #121212; color: white; 
            font-family: sans-serif;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center; 
            min-height: 100vh; margin: 0; padding: 15px;
        }
        @media (min-width: 576px) {
            body { padding: 20px; }
        }
        h1 { margin: 0; font-size: clamp(1.25rem, 4vw, 3.5rem); text-align: center; }
        
        .session-box { 
            background: #333; padding: 12px 20px; border-radius: 50px; 
            margin: 15px 0; 
            font-size: clamp(0.875rem, 2.5vw, 2rem);
            font-weight: bold; color: #ffd700;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            text-align: center;
            width: 100%; max-width: 800px;
        }
        @media (min-width: 576px) {
            .session-box { padding: 15px 30px; margin: 20px 0; }
        }
        
        .qr-wrapper { 
            background: white; padding: 15px; border-radius: 20px; 
            display: inline-block;
            max-width: 100%;
        }
        @media (min-width: 576px) {
            .qr-wrapper { padding: 20px; }
        }
        
        .qr-wrapper img {
            width: 100%;
            height: auto;
            max-width: 280px;
            min-width: 200px;
        }
        @media (min-width: 576px) {
            .qr-wrapper img { max-width: 400px; min-width: 250px; }
        }

        .footer { margin-top: 15px; color: #888; font-size: 0.875rem; text-align: center; }
        @media (min-width: 576px) {
            .footer { margin-top: 20px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <h1>{{ $event->nama_event }}</h1>
    
    <div id="session-info" class="session-box">Memuat Sesi...</div>

    <div class="qr-wrapper">
        <div id="qr-area">
            @if($event->qr_mode == 'static')
                @php
                    $url = url('/proses-scan/' . $event->id . '?type=static');
                    $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($url);
                @endphp
                <img src="{{ $qrImage }}" alt="QR Code" />
            @else
                <div style="padding:50px; color:black">Loading...</div>
            @endif
        </div>
    </div>
    
    <div class="footer">
        Mode: {{ strtoupper($event->qr_mode) }} | Scan untuk Absensi
    </div>

    <script>
        function updateSessionInfo() {
            $.get('{{ route("monitor.session", $event->id) }}', function(data) {
                $('#session-info').html(data.html);
            });
        }
        setInterval(updateSessionInfo, 5000);
        updateSessionInfo();

        @if($event->qr_mode == 'dynamic')
        function updateQR() {
            $.get('{{ route("monitor.qr", $event->id) }}', function(data) {
                $('#qr-area').html(data.html);
            });
        }
        setInterval(updateQR, 10000);
        updateQR(); 
        @endif
    </script>
</body>
</html>
