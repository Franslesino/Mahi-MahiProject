<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $user->name }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; background: #f7fafc; margin: 0; padding: 0; }
        .certificate {
            width: 100%;
            max-width: 1100px;
            margin: 40px auto;
            padding: 40px;
            background: #fff;
            border: 8px solid #1f4b99;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            position: relative;
        }
        .certificate:before {
            content: '';
            position: absolute;
            inset: 16px;
            border: 2px dashed #1f4b99;
            opacity: 0.3;
            pointer-events: none;
        }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 32px; color: #1f4b99; letter-spacing: 2px; }
        .header p { margin: 6px 0 0; color: #4a5568; }
        .content { text-align: center; padding: 20px 40px; }
        .recipient { font-size: 26px; font-weight: bold; color: #2d3748; margin: 16px 0; }
        .course { font-size: 22px; color: #1f4b99; margin: 8px 0 20px; }
        .meta { margin-top: 24px; color: #4a5568; }
        .meta div { margin: 4px 0; }
        .footer { display: flex; justify-content: space-between; align-items: center; margin-top: 40px; padding: 0 20px; }
        .sig { text-align: center; }
        .sig-line { width: 220px; border-top: 1px solid #2d3748; margin: 8px auto 4px; }
        .badge { position: absolute; top: 40px; right: 40px; width: 120px; opacity: 0.1; }
        .badge img { width: 100%; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="badge">
            <img src="{{ asset('storage/logo.png') }}" alt="Badge">
        </div>
        <div class="header">
            <h1>SERTIFIKAT KELULUSAN</h1>
            <p>Diberikan kepada</p>
        </div>
        <div class="content">
            <div class="recipient">{{ $user->name }}</div>
            <p>atas keberhasilan menyelesaikan kursus</p>
            <div class="course">{{ $course->title }}</div>
            <p>Dengan dedikasi tinggi dan menyelesaikan seluruh materi yang dipersyaratkan.</p>

            <div class="meta">
                <div>ID Kursus: {{ $course->id }}</div>
                <div>Tanggal Terbit: {{ $issuedAt->format('d M Y') }}</div>
            </div>
        </div>

        <div class="footer">
            <div class="sig">
                <div class="sig-line"></div>
                <div class="text-sm">Instruktur</div>
                <div>{{ $course->instructor->name ?? 'Instruktur' }}</div>
            </div>
            <div class="sig">
                <div class="sig-line"></div>
                <div class="text-sm">Direktur Pelatihan</div>
                <div>Pelayanan TIK PNJ</div>
            </div>
        </div>
    </div>
</body>
</html>
