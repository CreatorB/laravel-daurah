<?php

namespace App\Services;

class WhatsAppService
{
    public function generateKonfirmasiLink($phone, $namaEvent, $tanggal, $groupLink = null, $sessions = [])
    {
        $phone = $this->formatPhone($phone);

        $tanggalFormatted = date('d F Y', strtotime($tanggal));

        $message = $this->buildKonfirmasiMessage($namaEvent, $tanggalFormatted, $groupLink, $sessions);

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    private function buildKonfirmasiMessage($namaEvent, $tanggal, $groupLink, $sessions = [])
    {
        $msg = "Assalamu'alaikum Warahmatullahi Wabarakatuh\n\n";
        $msg .= "Alhamdulillah, pemberitahuan bahwa Anda telah *terkonfirmasi* untuk hadir inshaAllah di acara:\n\n";
        $msg .= "📌 *{$namaEvent}*\n";
        $msg .= "📅 Tanggal : {$tanggal}\n\n";

        if (!empty($sessions)) {
            $msg .= "🗓 *Jadwal Sesi :*\n";
            foreach ($sessions as $sesi) {
                $msg .= "  • {$sesi['nama_sesi']} : {$sesi['jam_mulai']} - {$sesi['jam_selesai']}\n";
            }
            $msg .= "\n";
        }

        $msg .= "📢 *Himbauan :*\n";
        $msg .= "Harap hadir tepat waktu dan mengikuti seluruh rangkaian acara dengan tertib.\n\n";

        if ($groupLink) {
            $msg .= "📲 Dihimbau untuk segera masuk ke link grup untuk media komunikasi dan informasi acara:\n";
            $msg .= "👉 {$groupLink}\n\n";
        }

        $msg .= "Jazakumullahu khairan atas konfirmasinya.\n";
        $msg .= "Sampai jumpa di acara! 🌿\n\n";
        $msg .= "_Wassalamu'alaikum Warahmatullahi Wabarakatuh_";

        return $msg;
    }

    public function generateReminderLink($phone, $namaEvent, $tanggal, $namaSesi, $jamMulai, $jamSelesai, $groupLink = null)
    {
        $phone = $this->formatPhone($phone);
        
        $tanggalFormatted = date('d F Y', strtotime($tanggal));
        
        $message = "Assalamu'alaikum WR. Wb.\n\n";
        $message .= "📌 Event: {$namaEvent}\n";
        $message .= "📅 Tanggal: {$tanggalFormatted}\n";
        $message .= "🕐 Jadwal:\n";
        $message .= "   {$namaSesi}: {$jamMulai} - {$jamSelesai}\n\n";
        
        if ($groupLink) {
            $message .= "Dihimbau untuk masuk link group untuk media komunikasi:\n";
            $message .= "👉 {$groupLink}\n\n";
        }
        
        $message .= "Jazakumullahu khairan";
        
        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            return '62' . substr($phone, 1);
        }

        if (substr($phone, 0, 2) === '62') {
            return $phone;
        }

        return '62' . $phone;
    }
}
