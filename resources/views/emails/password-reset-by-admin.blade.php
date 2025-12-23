<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .password-box {
            background: white;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .password {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            padding: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Resetovanje lozinke</h1>
    </div>
    
    <div class="content">
        <p>Poštovani <strong>{{ $user->name }}</strong>,</p>
        
        <p>Administrator je resetovao vašu lozinku za pristup platformi.</p>
        
        <div class="password-box">
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Vaša nova lozinka:</p>
            <p class="password">{{ $newPassword }}</p>
        </div>
        
        <div class="warning">
            <strong>⚠️ Važno:</strong> Iz bezbednosnih razloga, molimo vas da odmah nakon prijave promenite ovu lozinku na novu koju ćete zapamtiti.
        </div>
        
        <div style="text-align: center;">
            <a href="{{ url('/login') }}" class="button">Prijavite se</a>
        </div>
        
        <h3>Kako promeniti lozinku:</h3>
        <ol>
            <li>Prijavite se sa novom lozinkom</li>
            <li>Idite na <strong>Profil → Izmeni profil</strong></li>
            <li>Unesite novu lozinku</li>
            <li>Sačuvajte izmene</li>
        </ol>
        
        <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
            Ako niste tražili resetovanje lozinke ili imate pitanja, molimo kontaktirajte administratora.
        </p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Classified Listings. Sva prava zadržana.</p>
        <p>Ovaj email je automatski generisan, molimo ne odgovarajte.</p>
    </div>
</body>
</html>