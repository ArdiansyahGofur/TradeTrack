<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];


/* =========================================================
   FILTER
========================================================= */

$tanggalMulai = $_GET['tanggal_mulai'] ?? '';
$tanggalAkhir = $_GET['tanggal_akhir'] ?? '';
$menuId       = isset($_GET['menu_id']) ? (int) $_GET['menu_id'] : 0;


/* =========================================================
   WHERE
========================================================= */

$where = [
    'p.user_id = ?'
];

$params = [
    $userId
];


/* Filter tanggal mulai */

if ($tanggalMulai !== '') {

    $where[] = 'p.tanggal >= ?';

    $params[] = $tanggalMulai . ' 00:00:00';
}


/* Filter tanggal akhir */

if ($tanggalAkhir !== '') {

    $where[] = 'p.tanggal < ?';

    $params[] = date(
        'Y-m-d',
        strtotime($tanggalAkhir . ' +1 day')
    ) . ' 00:00:00';
}


/* Filter menu */

if ($menuId > 0) {

    $where[] = "
        EXISTS (
            SELECT 1
            FROM detail_penjualan dp_menu
            WHERE dp_menu.penjualan_id = p.id
              AND dp_menu.menu_id = ?
        )
    ";

    $params[] = $menuId;
}


$whereSql = implode(' AND ', $where);


/* =========================================================
   DATA EXPORT
========================================================= */

$stmt = $conn->prepare("
    SELECT
        p.id,
        p.tanggal,
        p.total,
        p.catatan,
        COUNT(dp.id) AS jumlah_item
    FROM penjualan p

    LEFT JOIN detail_penjualan dp
        ON p.id = dp.penjualan_id

    WHERE {$whereSql}

    GROUP BY
        p.id,
        p.tanggal,
        p.total,
        p.catatan

    ORDER BY
        p.tanggal DESC,
        p.id DESC
");

$stmt->execute($params);

$data = $stmt->fetchAll();


/* =========================================================
   HEADER EXPORT
========================================================= */

$filename = 'laporan-penjualan-' . date('Y-m-d-H-i-s') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header(
    'Content-Disposition: attachment; filename="' . $filename . '"'
);


/* =========================================================
   OUTPUT CSV
========================================================= */

$output = fopen('php://output', 'w');


/*
 * BOM UTF-8
 * Agar karakter Indonesia terbaca dengan baik di Excel.
 */

fprintf($output, "\xEF\xBB\xBF");


/* Header kolom */

fputcsv($output, [
    'No',
    'Tanggal',
    'Jumlah Item',
    'Catatan',
    'Total Pendapatan'
]);


/* Data */

$no = 1;

foreach ($data as $row) {

    fputcsv($output, [
        $no++,
        date('d/m/Y H:i', strtotime($row['tanggal'])),
        $row['jumlah_item'],
        $row['catatan'] ?? '',
        $row['total']
    ]);
}


fclose($output);

exit;