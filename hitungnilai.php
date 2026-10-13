<?php
/**
 * Program Perhitungan Nilai Mahasiswa
 * Nilai akhir = UTS 40% + UAS 60%
 */

// ===== FUNCTION BUATAN SENDIRI =====

// Menghitung nilai akhir berdasarkan bobot (perulangan foreach)
function hitungNilaiAkhir(array $skor, array $bobot): float
{
    $total = 0;
    foreach ($skor as $komponen => $nilai) {
        $total += $nilai * $bobot[$komponen];
    }
    return $total;
}

// Menentukan grade huruf (percabangan if-elseif-else)
function tentukanGrade(float $nilai): string
{
    if ($nilai >= 85) {
        return "A";
    } elseif ($nilai >= 75) {
        return "B";
    } elseif ($nilai >= 65) {
        return "C";
    } elseif ($nilai >= 50) {
        return "D";
    }
    return "E";
}

// ===== VARIABEL DENGAN TIPE DATA BERBEDA =====
$nama       = "";             // string
$nim        = "";             // string
$batasLulus = 65.0;           // float
$tampil     = false;          // boolean
$bobot      = [               // array
    "UTS" => 0.40,
    "UAS" => 0.60,
];
$jumlahKomp = count($bobot);  // integer
$input      = ["UTS" => "", "UAS" => ""];
$error      = "";

// ===== PROSES FORM =====
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama  = trim($_POST["nama"] ?? "");
    $nim   = trim($_POST["nim"] ?? "");
    $valid = ($nama !== "" && $nim !== "");

    foreach ($bobot as $komponen => $persen) {
        $nilai = $_POST[$komponen] ?? "";
        $input[$komponen] = $nilai;

        if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
            $valid = false;
        }
    }

    if ($valid) {
        $nilaiAkhir = hitungNilaiAkhir($input, $bobot);
        $grade      = tentukanGrade($nilaiAkhir);
        $lulus      = ($nilaiAkhir >= $batasLulus);
        $tampil     = true;
    } else {
        $error = "Isi nama, NIM, dan semua nilai dengan angka 0 - 100.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perhitungan Nilai Mahasiswa</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px 16px;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f7f6;
            color: #263238;
        }
        .wrapper {
            max-width: 460px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .08);
        }
        .header {
            padding: 24px 32px;
            background: linear-gradient(90deg, #00897b, #26a69a);
            color: #fff;
        }
        .header h1 { margin: 0; font-size: 22px; }
        .header p  { margin: 4px 0 0; opacity: .9; font-size: 14px; }
        .body { padding: 28px 32px; }
        label { display: block; font-size: 13px; font-weight: 600; margin: 14px 0 4px; }
        input {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #b0bec5;
            border-radius: 6px;
            font-size: 14px;
        }
        input:focus { outline: 2px solid #26a69a; border-color: transparent; }
        button {
            width: 100%;
            margin-top: 20px;
            padding: 11px;
            background: #00897b;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover { background: #00695c; }
        .error {
            margin-top: 16px;
            padding: 10px 14px;
            background: #ffebee;
            color: #c62828;
            border-radius: 6px;
            font-size: 14px;
        }
        .hasil {
            margin-top: 24px;
            padding: 20px;
            text-align: center;
            background: #f1f8f7;
            border-radius: 8px;
        }
        .hasil small  { color: #607d8b; }
        .hasil .angka { margin: 6px 0; font-size: 34px; font-weight: 600; color: #00695c; }
        .hasil .angka.gagal { color: #c62828; }
        .rincian { margin: 8px 0 0; padding: 0; list-style: none; font-size: 13px; color: #546e7a; }
        .ok    { color: #2e7d32; font-weight: 600; }
        .tidak { color: #c62828; font-weight: 600; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Perhitungan Nilai Mahasiswa</h1>
        <p>UTS 40% &middot; UAS 60%</p>
    </div>

    <div class="body">
        <form method="post">
            <label for="nama">Nama Mahasiswa</label>
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>">

            <label for="nim">NIM</label>
            <input type="text" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>">

            <?php foreach ($bobot as $komponen => $persen): ?>
                <label for="<?= $komponen ?>">Nilai <?= $komponen ?> (<?= $persen * 100 ?>%)</label>
                <input type="number" id="<?= $komponen ?>" name="<?= $komponen ?>"
                       min="0" max="100" step="any"
                       value="<?= htmlspecialchars($input[$komponen]) ?>">
            <?php endforeach; ?>

            <button type="submit">Hitung Nilai</button>
        </form>

        <?php if ($error !== ""): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($tampil): ?>
            <div class="hasil">
                <small>Nilai Akhir <?= htmlspecialchars($nama) ?> (<?= htmlspecialchars($nim) ?>)</small>
                <div class="angka <?= $lulus ? '' : 'gagal' ?>">
                    <?= number_format($nilaiAkhir, 2) ?> (<?= $grade ?>)
                </div>

                <ul class="rincian">
                    <?php foreach ($input as $komponen => $skor): ?>
                        <li><?= $komponen ?>: <?= $skor ?> x <?= $bobot[$komponen] * 100 ?>%</li>
                    <?php endforeach; ?>
                </ul>

                <p>
                    <?php if ($lulus): ?>
                        <span class="ok">LULUS</span>
                    <?php else: ?>
                        <span class="tidak">TIDAK LULUS</span>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>