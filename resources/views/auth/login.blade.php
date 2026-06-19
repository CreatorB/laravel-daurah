<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Daurah Syariyyah</title>
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
        
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 15px;
        }
        
        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 50%, #38bdf8 100%);
            padding: 28px 20px;
            text-align: center;
        }
        
        .logo-icon {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .logo-icon i {
            font-size: 28px;
            color: white;
        }
        
        .login-header h1 {
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }
        
        .login-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 11px;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .login-body {
            padding: 28px 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            color: #1e293b;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
        }
        
        .input-wrapper {
            display: flex;
            flex-direction: column;
        }
        
        .input-prefix {
            background: linear-gradient(135deg, #0ea5e9, #38bdf8);
            color: white;
            padding: 10px;
            font-weight: 600;
            font-size: 13px;
            text-align: center;
            border-radius: 10px 10px 0 0;
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
        
        .help-text {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #38bdf8 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.5);
        }
        
        .login-footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .login-footer p {
            color: #64748b;
            font-size: 13px;
            margin: 0;
        }
        
        .login-footer a {
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
            
            .login-header {
                padding: 36px 28px;
            }
            
            .logo-icon {
                width: 64px;
                height: 64px;
            }
            
            .logo-icon i {
                font-size: 32px;
            }
            
            .login-header h1 {
                font-size: 22px;
            }
            
            .login-body {
                padding: 36px 28px;
            }
            
            .input-wrapper {
                flex-direction: row;
            }
            
            .input-prefix {
                border-radius: 10px 0 0 10px;
                min-width: 65px;
            }
            
            .form-control {
                border-radius: 0 10px 10px 0;
                border-top: 2px solid #e2e8f0;
                border-left: none;
            }
        }
        
        @media (min-width: 768px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .login-card {
                max-width: 420px;
            }
            
            .login-header {
                padding: 44px 36px;
            }
            
            .logo-icon {
                width: 72px;
                height: 72px;
            }
            
            .logo-icon i {
                font-size: 36px;
            }
            
            .login-header h1 {
                font-size: 24px;
            }
            
            .login-header p {
                font-size: 12px;
            }
            
            .login-body {
                padding: 44px 36px;
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
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h1>Selamat Datang</h1>
                <p>Silakan masuk dengan nomor WhatsApp</p>
            </div>
            
            <div class="login-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
                @endif
                
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" 
                               name="nohp" 
                               class="form-control" 
                               placeholder="081234567890" 
                               required 
                               autofocus
                               value="{{ old('nohp') }}"
                               autocomplete="tel">
                        <div class="help-text">
                            <i class="fas fa-info-circle"></i>
                            <span>Contoh: 081234567890</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Masuk</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>