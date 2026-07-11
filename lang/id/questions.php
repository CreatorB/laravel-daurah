<?php

return [

    'app_name' => 'Tanya Jawab Daurah Syariyyah',
    'tagline' => "*Ajukan pertanyaan kepada Syaikh dengan menuliskannya dalam bahasa Arab di kolom berikut ini\n*Pertanyaan akan ditinjau terlebih dahulu oleh panitia sebelum dipublikasikan",

    'nav' => [
        'home' => 'Beranda',
        'ask' => 'Kirim Pertanyaan',
        'list' => 'Daftar Pertanyaan',
        'language' => 'Bahasa',
    ],

    'home' => [
        'have_question' => 'Punya pertanyaan?',
        'intro' => "*Ajukan pertanyaan kepada Syaikh dengan menuliskannya dalam bahasa Arab di kolom berikut ini",
        'submit_cta' => 'Kirim Pertanyaan',
        'browse_cta' => 'Lihat Pertanyaan',
    ],

    'ask' => [
        'title' => 'Kirim Pertanyaan',
        'intro' => '*Pertanyaan akan ditinjau terlebih dahulu oleh panitia sebelum dipublikasikan',
        'submit_button' => 'Kirim Pertanyaan',
        'sending' => 'Mengirim...',
        'counter_label' => 'karakter',
    ],

    'list' => [
        'title' => 'Daftar Pertanyaan',
        'subtitle' => 'Pertanyaan yang telah dijawab oleh para ustadz/ulama.',
        'empty' => 'Belum ada pertanyaan yang dipublikasikan.',
        'show_more' => 'Lihat Detail',
        'published_on' => 'Tanggal Publikasi',
        'page_title' => 'Daftar Pertanyaan',
        'flash_new_one' => '1 pertanyaan baru',
        'flash_new_many' => ':count pertanyaan baru',
        'flash_removed_one' => '1 pertanyaan dihapus/dibatalkan',
        'flash_removed_many' => ':count pertanyaan dihapus/dibatalkan',
    ],

    'show' => [
        'page_title' => 'Detail Pertanyaan',
        'ref_label' => 'No. Pertanyaan',
        'published_on' => 'Tanggal Publikasi',
        'submitted_by' => 'Nama Penanya',
        'back_to_list' => 'Kembali ke Daftar Pertanyaan',
    ],

    'fields' => [
        'name' => 'Nama',
        'name_help' => 'Nama (opsional)',
        'name_placeholder' => 'Nama Anda',
        'question_body' => 'Isi pertanyaan',
        'question_body_placeholder' => 'Jelaskan pertanyaan Anda sedetail mungkin...',
        'is_anonymous' => 'Kirim secara anonim',
        'locale' => 'Bahasa',
        'lesson_code' => 'Kode Pelajaran',
    ],

    'anonymous' => 'Anonim',

    'status' => [
        'pending' => 'Menunggu Review',
        'approved' => 'Dipublikasikan',
        'rejected' => 'Ditolak',
    ],

    'flash' => [
        'sent_success' => 'Pertanyaan berhasil dikirim',
        'send_error' => 'Terjadi kesalahan saat mengirim pertanyaan',
    ],

    'validation' => [
        'name_max' => 'Nama tidak boleh lebih dari :max karakter',
        'question_required' => 'Silakan isi pertanyaan',
        'question_min' => 'Isi pertanyaan terlalu singkat',
        'question_max' => 'Isi pertanyaan terlalu panjang',
        'locale_invalid' => 'Bahasa yang dipilih tidak didukung',
    ],

    'admin' => [
        'title' => 'Moderasi Pertanyaan',
        'login_required' => 'Silakan login terlebih dahulu.',
        'forbidden' => 'Anda tidak memiliki akses.',
        'total_items' => 'item',
        'tabs' => [
            'pending' => 'Menunggu',
            'approved' => 'Dipublikasikan',
            'rejected' => 'Ditolak',
        ],
        'filters' => [
            'search_placeholder' => 'Cari nama, isi pertanyaan, atau nomor...',
            'locale' => 'Bahasa',
            'lesson_code' => 'Kode Pelajaran',
            'all_locales' => 'Semua Bahasa',
            'apply' => 'Terapkan',
            'reset' => 'Reset',
        ],
        'bulk' => [
            'label' => 'Aksi massal',
            'action_approve' => 'Setujui',
            'action_reject' => 'Tolak',
            'action_delete' => 'Hapus',
            'apply' => 'Terapkan',
            'no_selection' => 'Pilih minimal satu pertanyaan terlebih dahulu.',
            'confirm_approve' => 'Setujui pertanyaan yang dipilih?',
            'confirm_reject' => 'Tolak pertanyaan yang dipilih?',
            'confirm_delete' => 'Hapus permanen pertanyaan yang dipilih? Tindakan ini tidak dapat dibatalkan.',
            'approved' => '{count} pertanyaan berhasil disetujui.',
            'rejected' => '{count} pertanyaan berhasil ditolak.',
            'deleted' => '{count} pertanyaan berhasil dihapus.',
        ],
        'table' => [
            'ref' => 'No',
            'name' => 'Nama',
            'preview' => 'Cuplikan',
            'locale' => 'Bahasa',
            'status' => 'Status',
            'created_at' => 'Tgl Kirim',
            'actions' => 'Aksi',
        ],
        'detail' => [
            'page_title' => 'Detail Pertanyaan',
            'back' => 'Kembali ke Dashboard',
            'question_body' => 'Isi Pertanyaan',
            'meta' => 'Metadata',
            'ref' => 'No',
            'lesson_code' => 'Kode Pelajaran',
            'anonymous_yes' => 'Ya',
            'anonymous_no' => 'Tidak',
            'admin_note' => 'Catatan Admin',
            'admin_note_placeholder' => 'Catatan internal (opsional)',
            'approve' => 'Setujui & Publikasi',
            'reject' => 'Tolak',
        ],
        'already_approved' => 'Pertanyaan ini sudah dipublikasikan.',
        'already_rejected' => 'Pertanyaan ini sudah ditolak.',
        'approved_success' => 'Pertanyaan berhasil dipublikasikan.',
        'rejected_success' => 'Pertanyaan berhasil ditolak.',
        'deleted_success' => 'Pertanyaan berhasil dihapus.',
        'flash_approved' => 'Pertanyaan disetujui',
        'flash_rejected' => 'Pertanyaan ditolak',
        'flash_deleted' => 'Pertanyaan dihapus',
        'flash_new_one' => '1 pertanyaan baru masuk',
        'flash_new_many' => ':count pertanyaan baru masuk',
        'flash_error' => 'Terjadi kesalahan, coba lagi.',
        'empty' => 'Tidak ada pertanyaan pada daftar ini.',
        'row' => [
            'action_approve' => 'Setujui pertanyaan ini',
            'action_reject' => 'Tolak pertanyaan ini',
            'action_delete' => 'Hapus pertanyaan ini',
            'confirm_approve' => 'Setujui pertanyaan ini?',
            'confirm_reject' => 'Tolak pertanyaan ini?',
            'confirm_delete' => 'Hapus permanen pertanyaan ini? Tindakan tidak dapat dibatalkan.',
        ],
    ],

    'language_switch' => [
        'arabic' => 'العربية',
        'indonesian' => 'Bahasa Indonesia',
        'switch_to' => 'Ganti bahasa ke',
    ],
];
