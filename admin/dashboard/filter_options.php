<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');

header('Content-Type: application/json; charset=utf-8');

$pedagangId = isset($_GET['pedagang'])
    ? (int) $_GET['pedagang']
    : 0;

$kategoriId = isset($_GET['kategori'])
    ? (int) $_GET['kategori']
    : 0;


/*
|--------------------------------------------------------------------------
| DATA KATEGORI
|--------------------------------------------------------------------------
| Jika pedagang dipilih:
| tampilkan hanya kategori yang digunakan oleh menu pedagang tersebut.
|--------------------------------------------------------------------------
*/

$kategori = [];

if ($pedagangId > 0) {

    $stmt = $conn->prepare("
        SELECT DISTINCT
            k.id,
            k.nama_kategori

        FROM kategori k

        INNER JOIN menu m
            ON m.kategori_id = k.id

        WHERE m.user_id = ?
          AND m.status = 'aktif'

        ORDER BY k.nama_kategori ASC
    ");

    $stmt->execute([
        $pedagangId
    ]);

    $kategori = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {

    /*
     * Jika belum memilih pedagang,
     * tampilkan semua kategori yang memiliki menu aktif.
     */

    $stmt = $conn->query("
        SELECT DISTINCT
            k.id,
            k.nama_kategori

        FROM kategori k

        INNER JOIN menu m
            ON m.kategori_id = k.id

        WHERE m.status = 'aktif'

        ORDER BY k.nama_kategori ASC
    ");

    $kategori = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/*
|--------------------------------------------------------------------------
| DATA MENU
|--------------------------------------------------------------------------
| Pedagang dipakai sebagai filter utama.
| Kategori menjadi filter tambahan.
|--------------------------------------------------------------------------
*/

$menu = [];

$where = [
    "m.status = 'aktif'"
];

$params = [];


/*
|--------------------------------------------------------------------------
| FILTER PEDAGANG
|--------------------------------------------------------------------------
*/

if ($pedagangId > 0) {

    $where[] = "m.user_id = ?";

    $params[] = $pedagangId;
}


/*
|--------------------------------------------------------------------------
| FILTER KATEGORI
|--------------------------------------------------------------------------
*/

if ($kategoriId > 0) {

    $where[] = "m.kategori_id = ?";

    $params[] = $kategoriId;
}


$whereSql = implode(' AND ', $where);


/*
|--------------------------------------------------------------------------
| QUERY MENU
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        m.id,
        m.nama_menu

    FROM menu m

    WHERE {$whereSql}

    ORDER BY m.nama_menu ASC
");

$stmt->execute($params);

$menu = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

echo json_encode(
    [
        'success' => true,
        'kategori' => $kategori,
        'menu' => $menu
    ],
    JSON_UNESCAPED_UNICODE
);

exit;