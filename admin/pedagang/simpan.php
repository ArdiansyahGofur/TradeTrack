<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit;

}


$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$no_hp = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');


/*
|--------------------------------------------------------------------------
| Validasi
|--------------------------------------------------------------------------
*/

if (
    $nama === '' ||
    $username === '' ||
    $password === ''
) {

    header(
        'Location: tambah.php?error=' .
        urlencode('Data wajib belum lengkap.')
    );

    exit;
}


if (strlen($password) < 6) {

    header(
        'Location: tambah.php?error=' .
        urlencode('Password minimal 6 karakter.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Cek username
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM users
    WHERE username = ?
    LIMIT 1
");

$stmt->execute([$username]);

if ($stmt->fetch()) {

    header(
        'Location: tambah.php?error=' .
        urlencode('Username sudah digunakan.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Hash password
|--------------------------------------------------------------------------
*/

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


/*
|--------------------------------------------------------------------------
| Simpan
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO users
    (
        nama,
        username,
        password,
        role,
        no_hp,
        alamat,
        status
    )
    VALUES
    (
        ?, ?, ?, 'pedagang', ?, ?, 'aktif'
    )
");

$stmt->execute([
    $nama,
    $username,
    $passwordHash,
    $no_hp ?: null,
    $alamat ?: null
]);


header(
    'Location: index.php?success=' .
    urlencode('Pedagang berhasil ditambahkan.')
);

exit;