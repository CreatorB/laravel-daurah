<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Ditutup - Daurah Syariyyah</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0f4c75 0%, #1a5f7a 25%, #2d98a3 50%, #38bdf8 75%, #7dd3fc 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .action-bar {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 50%, #38bdf8 100%);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .action-bar-icon {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-bar-icon i {
            font-size: 18px;
            color: white;
        }

        .action-bar-title {
            color: white;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .content-container {
            flex: 1;
            padding: 20px 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .closed-card {
            width: 100%;
            max-width: 550px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .closed-header {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 50%, #fcd34d 100%);
            padding: 28px 20px;
            text-align: center;
        }

        .closed-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .closed-icon i {
            font-size: 32px;
            color: white;
        }

        .closed-datetime {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 8px;
            padding: 8px 14px;
            margin-bottom: 14px;
            display: inline-block;
        }

        .closed-datetime-text {
            color: white;
            font-size: 12px;
            font-weight: 600;
            margin: 0;
        }

        .closed-header h1 {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 6px 0;
        }

        .closed-header p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 13px;
            margin: 0;
        }

        .closed-body {
            padding: 28px 24px;
            text-align: center;
        }

        .closed-message {
            color: #1e293b;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .closed-message strong {
            color: #0369a1;
        }

        .info-box {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            text-align: left;
        }

        .info-box-title {
            color: #92400e;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-box-content {
            color: #78350f;
            font-size: 12px;
            line-height: 1.6;
        }

        .schedule-box {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .schedule-title {
            color: #0369a1;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .schedule-time {
            color: #0c4a6e;
            font-size: 14px;
            font-weight: 600;
        }

        .contact-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #38bdf8 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
            color: white;
        }

        .footer-text {
            margin-top: 20px;
            color: #64748b;
            font-size: 12px;
        }

        @media (min-width: 480px) {
            .action-bar {
                padding: 14px 20px;
                gap: 14px;
            }

            .action-bar-icon {
                width: 40px;
                height: 40px;
            }

            .action-bar-icon i {
                font-size: 20px;
            }

            .action-bar-title {
                font-size: 18px;
            }
        }

        @media (min-width: 768px) {
            .content-container {
                padding: 40px 20px;
            }

            .closed-header {
                padding: 36px 28px;
            }

            .closed-header h1 {
                font-size: 24px;
            }

            .closed-body {
                padding: 36px 32px;
            }

            .closed-message {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="action-bar">
        <div class="action-bar-icon">
            <i class="fas fa-mosque"></i>
        </div>
        <span class="action-bar-title">{{ config('app.daurah_name') }}</span>
    </div>

    <div class="content-container">
        <div class="closed-card">
            <div class="closed-header">
                <div class="closed-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="closed-datetime">
                    <p class="closed-datetime-text">{{ $now->locale('id')->isoFormat('dddd, D MMMM YYYY • HH:mm:ss') }} WIB</p>
                </div>
                <h1>Afwan, Link Konfirmasi Ditutup</h1>
                <p>Mohon maaf, periode konfirmasi telah berakhir</p>
            </div>

            <div class="closed-body">
                <p class="closed-message">
                   Semoga keadaan ikhwah kita semua dalam keadaan sehat. Mengingat periode konfirmasi kehadiran untuk <strong>{{ $event->nama_event ?? 'event' }}</strong> telah ditutup.
                </p>

                @if($buka && $tutup)
                <div class="schedule-box">
                    <div class="schedule-title">
                        <i class="fas fa-calendar-alt"></i>
                        Jadwal Konfirmasi
                    </div>
                    <div class="schedule-time">
                        {{ $buka->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB
                        <br>s/d<br>
                        {{ $tutup->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB
                    </div>
                </div>
                @endif

                <div class="info-box">
                    <div class="info-box-title">
                        <i class="fas fa-info-circle"></i>
                        Informasi
                    </div>
                    <div class="info-box-content">
                        bagi ikhwah yang belum melakukan konfirmasi atau terdapat kendala dalam proses konfirmasi, silakan hubungi admin untuk mendapatkan bantuan lebih lanjut. Jazakumullahu khairan atas pengertiannya.
                    </div>
                </div>

                <a href="https://wa.me/6285158850339" target="_blank" class="contact-btn">
                    <i class="fab fa-whatsapp"></i>
                    Hubungi Admin
                </a>

                <p class="footer-text">
                    Semoga kita semua mendapatkan keberkahan dalam kegiatan ini
                </p>
            </div>
        </div>
    </div>

    @include('layouts.partials.footer-white')
</body>
</html>
