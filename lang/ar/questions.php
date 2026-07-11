<?php

return [

    'app_name' => 'أسئلة وأجوبة الدورة الشرعية',
    'tagline' => 'اطرح مسألتك الدينية على المشايخ واحصل على إجابة نافعة.',

    'nav' => [
        'home' => 'الرئيسية',
        'ask' => 'إرسال سؤال',
        'list' => 'الأسئلة',
        'language' => 'اللغة',
    ],

    'home' => [
        'have_question' => 'هل لديك سؤال؟',
        'intro' => 'اطرح مسألتك الدينية على المشايخ واحصل على إجابة نافعة.',
        'submit_cta' => 'إرسال سؤال',
        'browse_cta' => 'تصفح الأسئلة',
        'review_note' => 'سيراجع الشيخ سؤالك قبل نشره.',
    ],

    'ask' => [
        'title' => 'إرسال سؤال',
        'intro' => 'سيراجع الشيخ سؤالك قبل نشره.',
        'submit_button' => 'إرسال السؤال',
        'sending' => 'جارٍ الإرسال...',
        'counter_label' => 'الأحرف',
    ],

    'list' => [
        'title' => 'الأسئلة',
        'subtitle' => 'أسئلة تم الإجابة عنها من قبل المشايخ.',
        'empty' => 'لا توجد أسئلة منشورة حتى الآن.',
        'show_more' => 'عرض التفاصيل',
        'published_on' => 'تاريخ النشر',
        'page_title' => 'قائمة الأسئلة',
        'flash_new_one' => 'سؤال جديد واحد',
        'flash_new_many' => ':count أسئلة جديدة',
        'flash_removed_one' => 'تم حذف/سحب سؤال واحد',
        'flash_removed_many' => 'تم حذف/سحب :count أسئلة',
    ],

    'show' => [
        'page_title' => 'تفاصيل السؤال',
        'ref_label' => 'رقم السؤال',
        'published_on' => 'تاريخ النشر',
        'submitted_by' => 'اسم السائل',
        'back_to_list' => 'العودة إلى الأسئلة',
    ],

    'fields' => [
        'name' => 'الاسم',
        'name_help' => 'الاسم (اختياري)',
        'name_placeholder' => 'اسمك',
        'question_body' => 'نص السؤال',
        'question_body_placeholder' => 'اشرح سؤالك بأكبر قدر ممكن من التفصيل...',
        'is_anonymous' => 'إرسال كمجهول',
        'locale' => 'اللغة',
        'lesson_code' => 'رمز الدرس',
    ],

    'anonymous' => 'مجهول',

    'status' => [
        'pending' => 'قيد المراجعة',
        'approved' => 'منشور',
        'rejected' => 'مرفوض',
    ],

    'flash' => [
        'sent_success' => 'تم إرسال السؤال بنجاح',
        'send_error' => 'حدث خطأ أثناء إرسال السؤال',
    ],

    'validation' => [
        'name_max' => 'يجب ألا يتجاوز الاسم :max حرفًا',
        'question_required' => 'يرجى إدخال نص السؤال',
        'question_min' => 'نص السؤال قصير جدًا',
        'question_max' => 'نص السؤال طويل جدًا',
        'locale_invalid' => 'اللغة المختارة غير مدعومة',
    ],

    'admin' => [
        'title' => 'لوحة مراجعة الأسئلة',
        'login_required' => 'يرجى تسجيل الدخول أولاً.',
        'forbidden' => 'غير مسموح بالوصول.',
        'total_items' => 'عنصر',
        'tabs' => [
            'pending' => 'قيد المراجعة',
            'approved' => 'منشورة',
            'rejected' => 'مرفوضة',
        ],
        'filters' => [
            'search_placeholder' => 'ابحث في الاسم أو نص السؤال أو رمز السؤال...',
            'locale' => 'اللغة',
            'lesson_code' => 'رمز الدرس',
            'all_locales' => 'كل اللغات',
            'apply' => 'تطبيق',
            'reset' => 'إعادة تعيين',
        ],
        'bulk' => [
            'label' => 'إجراءات جماعية',
            'action_approve' => 'قبول',
            'action_reject' => 'رفض',
            'action_delete' => 'حذف',
            'apply' => 'تطبيق',
            'no_selection' => 'يرجى اختيار سؤال واحد على الأقل.',
            'confirm_approve' => 'قبول :count سؤال من المختارة؟',
            'confirm_reject' => 'رفض :count سؤال من المختارة؟',
            'confirm_delete' => 'حذف :count سؤال نهائياً؟ لا يمكن التراجع.',
            'confirm_generic' => 'تطبيق الإجراء على :count سؤال من المختارة؟',
            'approved' => 'تم قبول {count} سؤال.',
            'rejected' => 'تم رفض {count} سؤال.',
            'deleted' => 'تم حذف {count} سؤال.',
        ],
        'table' => [
            'ref' => 'الرقم',
            'name' => 'الاسم',
            'preview' => 'معاينة',
            'locale' => 'اللغة',
            'status' => 'الحالة',
            'created_at' => 'تاريخ الإرسال',
            'actions' => 'الإجراءات',
        ],
        'detail' => [
            'page_title' => 'تفاصيل السؤال',
            'back' => 'العودة إلى اللوحة',
            'question_body' => 'نص السؤال',
            'meta' => 'بيانات السؤال',
            'ref' => 'الرقم',
            'lesson_code' => 'رمز الدرس',
            'anonymous_yes' => 'نعم',
            'anonymous_no' => 'لا',
            'admin_note' => 'ملاحظة الإدارة',
            'admin_note_placeholder' => 'ملاحظة داخلية (اختياري)',
            'approve' => 'قبول ونشر',
            'reject' => 'رفض',
        ],
        'already_approved' => 'هذا السؤال منشور بالفعل.',
        'already_rejected' => 'هذا السؤال مرفوض بالفعل.',
        'approved_success' => 'تم نشر السؤال بنجاح.',
        'rejected_success' => 'تم رفض السؤال.',
        'deleted_success' => 'تم حذف السؤال.',
        'flash_approved' => 'تم قبول السؤال',
        'flash_rejected' => 'تم رفض السؤال',
        'flash_deleted' => 'تم حذف السؤال',
        'flash_new_one' => 'سؤال جديد واحد',
        'flash_new_many' => ':count أسئلة جديدة',
        'flash_error' => 'حدث خطأ، حاول مرة أخرى.',
        'empty' => 'لا توجد أسئلة في هذه القائمة.',
        'row' => [
            'action_approve' => 'قبول هذا السؤال',
            'action_reject' => 'رفض هذا السؤال',
            'action_delete' => 'حذف هذا السؤال',
            'confirm_approve' => 'قبول هذا السؤال؟',
            'confirm_reject' => 'رفض هذا السؤال؟',
            'confirm_delete' => 'حذف هذا السؤال نهائياً؟ لا يمكن التراجع.',
        ],
    ],

    'language_switch' => [
        'arabic' => 'العربية',
        'indonesian' => 'Bahasa Indonesia',
        'switch_to' => 'تبديل اللغة إلى',
    ],
];
