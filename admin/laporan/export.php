<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');

$tanggalMulai = isset($_GET['tanggal_mulai'])
    ? trim($_GET['tanggal_mulai'])
    : '';

$tanggalAkhir = isset($_GET['tanggal_akhir'])
    ? trim($_GET['tanggal_akhir'])
    : '';

$pedagangId = isset($_GET['pedagang_id'])
    ? (int) $_GET['pedagang_id']
    : 0;

$menuId = isset($_GET['menu_id'])
    ? (int) $_GET['menu_id']
    : 0;


/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

$where = [];
$params = [];

if ($tanggalMulai !== '') {
    $where[] = 'p.tanggal >= ?';
    $params[] = $tanggalMulai . ' 00:00:00';
}

if ($tanggalAkhir !== '') {
    $where[] = 'p.tanggal < ?';

    $params[] = date(
        'Y-m-d',
        strtotime($tanggalAkhir . ' +1 day')
    ) . ' 00:00:00';
}

if ($pedagangId > 0) {
    $where[] = 'p.user_id = ?';
    $params[] = $pedagangId;
}

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

$whereSql = '';

if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}


/*
|--------------------------------------------------------------------------
| QUERY DATA
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        p.id,
        p.tanggal,
        u.nama AS nama_pedagang,
        u.username,
        p.total,
        p.catatan,
        COUNT(dp.id) AS jumlah_item
    FROM penjualan p

    INNER JOIN users u
        ON p.user_id = u.id

    LEFT JOIN detail_penjualan dp
        ON p.id = dp.penjualan_id

    {$whereSql}

    GROUP BY
        p.id,
        p.tanggal,
        u.nama,
        u.username,
        p.total,
        p.catatan

    ORDER BY
        p.tanggal DESC,
        p.id DESC
";

$stmt = $conn->prepare($sql);

$bind = 1;

foreach ($params as $param) {
    $stmt->bindValue($bind++, $param);
}

$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| NAMA FILE
|--------------------------------------------------------------------------
*/

$filename = 'laporan-admin-' . date('Y-m-d-H-i-s') . '.csv';


/*
|--------------------------------------------------------------------------
| EXPORT CSV
|--------------------------------------------------------------------------
*/

header('Content-Type: text/csv; charset=UTF-8');

header(
    'Content-Disposition: attachment; filename="' . $filename . '"'
);

header('Pragma: no-cache');
header('Expires: 0');


/*
|--------------------------------------------------------------------------
| UTF-8 BOM
|--------------------------------------------------------------------------
*/

echo "\xEF\xBB\xBF";


$output = fopen('php://output', 'w');


/*
|--------------------------------------------------------------------------
| HEADER CSV
|--------------------------------------------------------------------------
*/

fputcsv($output, [
    'No',
    'Tanggal',
    'Pedagang',
    'Username',
    'Jumlah Item',
    'Catatan',
    'Total Pendapatan'
]);


/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

$no = 1;

foreach ($data as $row) {

    fputcsv($output, [
        $no++,
        date('d-m-Y H:i', strtotime($row['tanggal'])),
        $row['nama_pedagang'],
        $row['username'],
        $row['jumlah_item'],
        $row['catatan'] ?? '-',
        $row['total']
    ]);
}


fclose($output);

exit;