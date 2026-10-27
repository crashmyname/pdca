<?php
$input       = $_POST['input'] ?? '';
$jsonOutput  = '';
$copyNote    = '';

if ($input) {
    $dept   = trim($_POST['dept']   ?? 'PRODUCTION ENGINEERING');
    $divisi = strtoupper(trim($_POST['divisi'] ?? 'PE'));

    $lines     = explode("\n", trim($input));
    $employees = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;

        // Pisah Tab atau 2+ spasi
        $parts = preg_split('/\t+|\s{2,}/', $line);
        if (count($parts) < 2) continue;

        $nik  = trim($parts[0]);
        $nama = trim($parts[1]);
        if ($nik === '' || $nama === '') continue;

        $employees[] = [
            'nik'    => $nik,
            'nama'   => $nama,
            'dept'   => $dept,
            'divisi' => $divisi,
        ];
    }

    if (!empty($employees)) {
        $jsonOutput = buildAlignedJson($divisi, $employees);
    }
}

/**
 * Bangun JSON ter-align per baris.
 * Padding ditambahkan SETELAH koma supaya field berikutnya
 * mulai di kolom yang sama untuk semua baris.
 */
function buildAlignedJson(string $divisiKey, array $employees): string
{
    // ── Cari panjang maksimum tiap kolom ──
    $maxNik  = 0;
    $maxNama = 0;
    $maxDept = 0;
    foreach ($employees as $e) {
        $maxNik  = max($maxNik,  strlen($e['nik']));
        $maxNama = max($maxNama, strlen($e['nama']));
        $maxDept = max($maxDept, strlen($e['dept']));
    }

    $lines = [];
    foreach ($employees as $e) {
        // Minimal 1 spasi setelah koma; tambah jika nilai lebih pendek
        $nikSp  = str_repeat(' ', $maxNik  - strlen($e['nik'])  + 1);
        $namaSp = str_repeat(' ', $maxNama - strlen($e['nama']) + 1);
        $deptSp = str_repeat(' ', $maxDept - strlen($e['dept']) + 1);

        $lines[] = sprintf(
            '    { "nik": "%s",%s"nama": "%s",%s"dept": "%s",%s"divisi": "%s" }',
            $e['nik'],  $nikSp,
            $e['nama'], $namaSp,
            $e['dept'], $deptSp,
            $e['divisi']
        );
    }

    // Fragment siap-paste: "PE": [ ... ],
    return "\"{$divisiKey}\": [\n" . implode(",\n", $lines) . "\n  ],";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Excel → Employee JSON (Aligned)</title>
    <style>
        body { font-family: system-ui, sans-serif; padding: 20px; background: #f9fafb; color: #111827; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 24px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #1e40af; }
        h3 { color: #374151; margin: 0; }
        label { font-weight: 600; font-size: 14px; display: block; margin-bottom: 4px; }
        input[type="text"] { padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        textarea { width: 100%; height: 180px; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-family: monospace; font-size: 14px; resize: vertical; box-sizing: border-box; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; margin-top: 12px; font-size: 14px; }
        button:hover { background: #1e40af; }
        button.copy { background: #16a34a; padding: 6px 14px; font-size: 12px; }
        button.copy:hover { background: #15803d; }
        .result { background: #1f2937; color: #10b981; padding: 16px; border-radius: 8px; font-family: monospace; font-size: 13px; white-space: pre; overflow-x: auto; margin-top: 8px; tab-size: 4; }
        .result-row { display: flex; align-items: center; justify-content: space-between; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Excel → Employee JSON (Aligned)</h2>
        <form method="POST">
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <div>
                    <label>Dept:</label>
                    <input type="text" name="dept" value="<?= htmlspecialchars($_POST['dept'] ?? 'PRODUCTION ENGINEERING') ?>" style="width: 320px;">
                </div>
                <div>
                    <label>Divisi / Section Code:</label>
                    <input type="text" name="divisi" value="<?= htmlspecialchars($_POST['divisi'] ?? 'PE') ?>" style="width: 120px; text-transform: uppercase;">
                </div>
            </div>

            <p style="margin-top: 16px; color: #6b7280; font-size: 14px;">
                <i>Paste dari Excel (format: NIK → NAMA → JABATAN). Kolom JABATAN diabaikan.</i>
            </p>

            <textarea name="input" placeholder="74030&#9;DIDIK RIYANTO&#9;OPERATOR&#10;80045&#9;AGUNG SETYAWAN&#9;OPERATOR"><?= htmlspecialchars($input) ?></textarea>

            <br>
            <button type="submit">Convert</button>
        </form>

        <?php if ($jsonOutput): ?>
            <div class="result-row">
                <h3>Hasil (siap-paste ke JSON):</h3>
                <button type="button" class="copy" onclick="copyResult()">📋 Copy</button>
            </div>
            <div class="result" id="jsonResult"><?= htmlspecialchars($jsonOutput) ?></div>
        <?php endif; ?>
    </div>

    <script>
        function copyResult() {
            const text = document.getElementById('jsonResult').textContent;
            navigator.clipboard.writeText(text).then(() => {
                const btn = event.target;
                const old = btn.textContent;
                btn.textContent = '✓ Tersalin!';
                setTimeout(() => btn.textContent = old, 1200);
            });
        }
    </script>
</body>
</html>