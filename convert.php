<?php
$input = $_POST['input'] ?? '';
$output = '';

if ($input) {
    $lines = explode("\n", trim($input));
    
    foreach ($lines as $line) {
        // Memisahkan berdasarkan Tab (dari Excel) atau 2 spasi atau lebih
        $parts = preg_split('/\t+|\s{2,}/', trim($line));
        
        if (count($parts) >= 3) {
            $nik    = trim($parts[0]);
            $nama   = trim($parts[1]);
            $divisi = trim($parts[2]);
            
            // Escape single quotes (jika ada nama seperti O'CONNOR)
            $nama   = str_replace("'", "\\'", $nama);
            $divisi = str_replace("'", "\\'", $divisi);
            
            // Ambil dept dan section dari form (bisa diubah-ubah)
            $dept    = $_POST['dept'] ?? 'FACT 1 - PART PRODUCTION 1';
            $section = $_POST['section'] ?? 'PO';
            
            $output .= "['nik' => '{$nik}', 'nama' => '{$nama}', 'dept' => '{$dept}', 'divisi' => '{$divisi}', 'section' => '{$section}'],\n";
        } 
        // Jaga-jaga jika hanya copy 2 kolom (NIK & NAMA)
        elseif (count($parts) == 2) {
            $nik  = trim($parts[0]);
            $nama = trim($parts[1]);
            $nama = str_replace("'", "\\'", $nama);
            
            $dept    = $_POST['dept'] ?? 'FACT 1 - PART PRODUCTION 1';
            $section = $_POST['section'] ?? 'PO';
            
            $output .= "['nik' => '{$nik}', 'nama' => '{$nama}', 'dept' => '{$dept}', 'divisi' => '', 'section' => '{$section}'],\n";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel to PHP Array Converter</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; padding: 20px; background: #f9fafb; color: #111827; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 24px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #1e40af; }
        label { font-weight: 600; font-size: 14px; margin-right: 8px; }
        input[type="text"] { padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; margin-bottom: 16px; }
        textarea { width: 100%; height: 200px; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-family: monospace; font-size: 14px; resize: vertical; box-sizing: border-box; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; margin-top: 12px; font-size: 14px; }
        button:hover { background: #1e40af; }
        .result { background: #1f2937; color: #10b981; padding: 16px; border-radius: 8px; font-family: monospace; font-size: 13px; white-space: pre; overflow-x: auto; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Excel to PHP Array</h2>
        <form method="POST">
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <div>
                    <label>Dept:</label><br>
                    <input type="text" name="dept" value="<?= htmlspecialchars($_POST['dept'] ?? 'FACT 1 - PART PRODUCTION 1') ?>" style="width: 300px;">
                </div>
                <div>
                    <label>Section:</label><br>
                    <input type="text" name="section" value="<?= htmlspecialchars($_POST['section'] ?? 'PO') ?>" style="width: 100px;">
                </div>
            </div>
            
            <p style="margin-top: 16px; color: #6b7280; font-size: 14px;">
                <i>Paste data dari Excel (format: NIK &rarr; NAMA &rarr; JABATAN) ke textarea di bawah:</i>
            </p>
            
            <textarea name="input" placeholder="72543&#9;ABDUL RIZAL BISRI&#9;OPERATOR&#10;71965&#9;ADI PURWANTO&#9;OPERATOR"><?= htmlspecialchars($input) ?></textarea>
            
            <br>
            <button type="submit">Convert ke Array PHP</button>
        </form>

        <?php if ($output): ?>
            <h3 style="margin-top: 32px; color: #374151;">Hasil Konversi:</h3>
            <div class="result"><?= htmlspecialchars($output) ?></div>
        <?php endif; ?>
    </div>
</body>
</html>