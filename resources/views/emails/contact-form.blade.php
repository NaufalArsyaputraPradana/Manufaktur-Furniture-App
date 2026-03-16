<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Kontak Baru</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .field {
            margin-bottom: 20px;
        }
        .field label {
            display: block;
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .field .value {
            background: white;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
        .message-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            white-space: pre-wrap;
            min-height: 100px;
        }
        .footer {
            background: #e9ecef;
            padding: 20px;
            border-radius: 0 0 10px 10px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📧 Pesan Kontak Baru</h1>
        <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">
            Anda menerima pesan baru dari website
        </p>
    </div>

    <div class="content">
        <div class="field">
            <label>👤 Nama Pengirim:</label>
            <div class="value">{{ $name }}</div>
        </div>

        <div class="field">
            <label>📨 Email:</label>
            <div class="value">
                <a href="mailto:{{ $email }}" style="color: #667eea; text-decoration: none;">
                    {{ $email }}
                </a>
            </div>
        </div>

        <div class="field">
            <label>📋 Subjek:</label>
            <div class="value">{{ $subject }}</div>
        </div>

        <div class="field">
            <label>💬 Pesan:</label>
            <div class="message-box">{{ $messageContent }}</div>
        </div>

        <div style="text-align: center;">
            <a href="mailto:{{ $email }}" class="btn">
                Balas Pesan Ini
            </a>
        </div>
    </div>

    <div class="footer">
        <p style="margin: 0 0 10px 0;">
            <strong>UDBISA Furniture Manufacturing System</strong>
        </p>
        <p style="margin: 0;">
            Email ini dikirim secara otomatis dari form kontak website.
            <br>
            Tanggal: {{ now()->format('d F Y, H:i:s') }} WIB
        </p>
    </div>
</body>
</html>
