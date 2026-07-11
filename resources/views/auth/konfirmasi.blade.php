<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pendaftaran - Daurah Syariyyah</title>
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
        
        .form-container {
            flex: 1;
            padding: 20px 15px;
            overflow-y: auto;
        }
        
        .form-card {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 50%, #38bdf8 100%);
            padding: 24px 20px;
            text-align: center;
        }
        
        .form-header h1 {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }
        
        .form-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 11px;
            margin: 0;
            letter-spacing: 0.5px;
        }
        
        .form-body {
            padding: 24px 20px;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-label {
            display: block;
            color: #1e293b;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #0ea5e9;
            background: white;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }

        .form-control::placeholder {
            color: #94a3b8;
        }
        
        .form-select {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13px;
            background: white;
            cursor: pointer;
        }
        
        .form-select:focus {
            outline: none;
            border-color: #0ea5e9;
        }
        
        .help-text {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
        }
        
        .agreement-box {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-radius: 12px;
            padding: 16px;
            margin-top: 8px;
        }
        
        .form-check-input {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
        }
        
        .form-check-label {
            color: #1e293b;
            font-size: 13px;
            cursor: pointer;
        }
        
        .agreement-text {
            max-height: 140px;
            overflow-y: auto;
            font-size: 12px;
            color: #475569;
            margin-top: 10px;
            padding-right: 8px;
        }
        
        .agreement-text ol {
            padding-left: 1.2rem;
            margin: 8px 0;
        }
        
        .agreement-text li {
            margin-bottom: 4px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #38bdf8 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.5);
        }
        
        .form-footer {
            text-align: center;
            padding: 16px 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 13px;
            color: #64748b;
        }
        
        .form-footer a {
            color: #0ea5e9;
            font-weight: 600;
            text-decoration: none;
        }
        
        .alert {
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 13px;
            border: none;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            color: #991b1b;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -8px;
        }
        
        .col-12 {
            width: 100%;
            padding: 0 8px;
        }
        
        .col-md-6 {
            width: 100%;
            padding: 0 8px;
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
            .form-container {
                padding: 30px 20px;
            }
            
            .form-card {
                max-width: 650px;
            }
            
            .form-header {
                padding: 32px 28px;
            }
            
            .form-header h1 {
                font-size: 22px;
            }
            
            .form-body {
                padding: 32px 28px;
            }
            
            .col-md-6 {
                width: 50%;
            }
            
            .input-group-custom {
                flex-direction: row;
            }
            
            .input-prefix {
                border-radius: 10px 0 0 10px;
                min-width: 65px;
            }
            
            .input-suffix {
                border-radius: 0 10px 10px 0;
                border-top: 2px solid #e2e8f0;
                border-left: none;
            }
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded');
        
        const form = document.querySelector('form[action="{{ route("konfirmasi") }}"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                const nohp = document.getElementById('nohp').value.replace(/[^0-9]/g, '');
                if (!nohp.startsWith('08')) {
                    e.preventDefault();
                    alert('Nomor WhatsApp harus diawali dengan 08');
                    document.getElementById('nohp').focus();
                    return;
                }
                console.log('Form submitting...');
                const fileInput = document.getElementById('bukti_undangan');
                if (fileInput && fileInput.files.length > 0) {
                    console.log('File selected:', fileInput.files[0].name, fileInput.files[0].size);
                }
            });
        }
        
        const fileInput = document.getElementById('bukti_undangan');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                console.log('File selected:', file.name, file.size, file.type);
                
                const maxSize = 1024 * 1024;
                if (file.size <= maxSize) {
                    console.log('File already small enough');
                    return;
                }
                
                if (!file.type.match(/^image\/(jpeg|png|jpg)$/)) {
                    console.log('Not an image or PDF - skipping compression');
                    return;
                }
                
                try {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const img = new Image();
                        img.onload = function() {
                            try {
                                const canvas = document.createElement('canvas');
                                const ctx = canvas.getContext('2d');
                                
                                let width = img.width;
                                let height = img.height;
                                const maxDim = 1200;
                                
                                if (width > maxDim || height > maxDim) {
                                    if (width > height) {
                                        height = Math.round((height * maxDim) / width);
                                        width = maxDim;
                                    } else {
                                        width = Math.round((width * maxDim) / height);
                                        height = maxDim;
                                    }
                                }
                                
                                canvas.width = width;
                                canvas.height = height;
                                ctx.drawImage(img, 0, 0, width, height);
                                
                                canvas.toBlob(function(blob) {
                                    if (blob && blob.size < file.size) {
                                        const newFile = new File([blob], file.name, { type: blob.type });
                                        const dataTransfer = new DataTransfer();
                                        dataTransfer.items.add(newFile);
                                        fileInput.files = dataTransfer.files;
                                        console.log('File compressed:', blob.size);
                                    }
                                }, 'image/jpeg', 0.8);
                            } catch (err) {
                                console.error('Compression error:', err);
                            }
                        };
                        img.onerror = function() {
                            console.error('Image load error');
                        };
                        img.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                } catch (err) {
                    console.error('File read error:', err);
                }
            });
        }
    });
    </script>
</head>
<body>
    <div class="action-bar">
        <div class="action-bar-icon">
            <i class="fas fa-mosque"></i>
        </div>
        <span class="action-bar-title">{{ config('app.daurah_name') }}</span>
    </div>
    
    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h1><i class="fas fa-user-plus me-2"></i>Konfirmasi Pendaftaran</h1>
                <p>Konfirmasi ini hanya dikhususkan bagi yang menerima undangan</p>
            </div>
            
            <div class="form-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
                @endif
                
                <form action="{{ route('konfirmasi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                    value="{{ old('nama') }}" required placeholder="Masukkan nama lengkap">
                                @error('nama')
                                <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label">Lembaga <span class="text-danger">*</span></label>
                                <input type="text" name="lembaga" class="form-control @error('lembaga') is-invalid @enderror" 
                                    value="{{ old('lembaga', 'PRIBADI') }}" required placeholder="Nama lembaga atau PRIBADI">
                                <span class="help-text">Jika tidak mewakili lembaga, isi dengan "PRIBADI"</span>
                                @error('lembaga')
                                <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label">Domisili <span class="text-danger">*</span></label>
                                <input type="text" name="domisili" class="form-control @error('domisili') is-invalid @enderror" 
                                    value="{{ old('domisili') }}" required placeholder="Kota domisili">
                                @error('domisili')
                                <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="nohp" id="nohp" class="form-control @error('nohp') is-invalid @enderror" 
                                    value="{{ old('nohp') }}" required placeholder="081234567890" pattern="08.*" title="Nomor harus diawali dengan 08">
                                <span class="help-text">Contoh: 081234567890</span>
                                <div id="nohp-error" class="text-danger mt-1" style="font-size: 11px; display: none;">Nomor WhatsApp harus diawali dengan 08</div>
                                @error('nohp')
                                <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label">Apakah akan menginap? <span class="text-danger">*</span></label>
                                <select name="menginap" class="form-select @error('menginap') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="ya" {{ old('menginap') == 'ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="tidak" {{ old('menginap') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>
                                <span class="help-text">Tempat menginap di Masjid bukan di hotel</span>
                                @error('menginap')
                                <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Bukti Undangan <span class="text-danger">*</span></label>
                                <input type="file" name="bukti_undangan" id="bukti_undangan" class="form-control @error('bukti_undangan') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                                <span class="help-text">Upload bukti undangan (PDF/JPG/PNG, maks 1MB). Gambar akan dikompres otomatis.</span>
                                @error('bukti_undangan')
                                <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

<div class="col-12">
                            <div class="agreement-box">
                                <div class="form-check">
                                    <input type="checkbox" name="agreement" id="agreement" class="form-check-input @error('agreement') is-invalid @enderror"
                                        value="1" {{ old('agreement') ? 'checked' : '' }} required>
                                    <label class="form-check-label fw-semibold" for="agreement">
                                        <i class="fas fa-handshake me-2 text-primary"></i>Agreement / Perjanjian:
                                    </label>
                                </div>
                                <div class="agreement-text">
                                    <p>Dengan ini saya menyatakan kesediaan untuk mengikuti seluruh rangkaian acara Daurah Syariyyah dengan penuh ketertiban, kedisiplinan, dan ketaatan kepada seluruh aturan yang berlaku.</p>
                                </div>
                            </div>
                            @error('agreement')
                            <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check-circle"></i>
                        <span>Daftar Sekarang</span>
                    </button>
                </form>
            </div>
            
            <div class="form-footer">
                <span class="text-muted">Sudah punya akun?</span>
                <a href="{{ route('login') }}"> Login di sini</a>
            </div>
        </div>
    </div>
    
    @include('layouts.partials.footer-white')
</body>
</html>