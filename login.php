<?php

require_once 'config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Jika sudah login
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['user_id'])) {

    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard/index.php');
        exit;
    }

    if ($_SESSION['role'] === 'pedagang') {
        header('Location: pedagang/dashboard/index.php');
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Proses Login
|--------------------------------------------------------------------------
*/

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {

        $error = 'Username dan password wajib diisi.';

    } else {

        $stmt = $conn->prepare("
            SELECT *
            FROM users
            WHERE username = ?
            AND status = 'aktif'
            LIMIT 1
        ");

        $stmt->execute([$username]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {

                header('Location: admin/dashboard/index.php');
                exit;

            }

            if ($user['role'] === 'pedagang') {

                header('Location: pedagang/dashboard/index.php');
                exit;

            }

        } else {

            $error = 'Username atau password salah.';

        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - TradeTrack</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">

    <div class="min-h-screen flex items-center justify-center px-4 py-10">

        <div class="w-full max-w-md">

            <!-- Logo / Brand -->

            <div class="text-center mb-8">

                <div class="inline-flex items-center justify-center
                            w-16 h-16
                            rounded-2xl
                            bg-white
                            shadow-md
                            mb-4">

                    <img
                        src="assets/img/logo-new.png"
                        alt="TradeTrack Logo"
                        class="w-12 h-12 object-contain"
                    >

                </div>

                <h1 class="text-3xl font-bold text-slate-800">
                    TradeTrack
                </h1>

                <p class="mt-2 text-slate-500">
                    Pencatatan &amp; Analisis Penjualan
                </p>

            </div>


            <!-- Login Card -->

            <div class="bg-white rounded-3xl
                        shadow-xl shadow-slate-200/60
                        border border-slate-100
                        p-6 sm:p-8">

                <div class="mb-6">

                    <h2 class="text-xl font-bold text-slate-800">
                        Selamat datang 👋
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Silakan masuk ke akun Anda.
                    </p>

                </div>


                <?php if ($error): ?>

                    <div class="mb-5 px-4 py-3
                                bg-red-50
                                border border-red-100
                                text-red-600
                                rounded-xl text-sm">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>


                <form method="POST" class="space-y-5">

                    <!-- Username -->

                    <div>

                        <label
                            for="username"
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Username
                        </label>

                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                            autocomplete="username"
                            required
                            class="w-full px-4 py-3
                                   bg-slate-50
                                   border border-slate-200
                                   rounded-xl
                                   text-slate-800
                                   placeholder-slate-400
                                   outline-none
                                   focus:bg-white
                                   focus:border-blue-500
                                   focus:ring-4
                                   focus:ring-blue-100
                                   transition"
                            placeholder="Masukkan username"
                        >

                    </div>


                    <!-- Password -->

                    <div>
                        <label
                            for="password"
                            class="block text-sm font-medium text-slate-700 mb-2"
                        >
                            Password
                        </label>

                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                autocomplete="current-password"
                                required
                                class="w-full px-4 py-3 pr-12
                                    bg-slate-50
                                    border border-slate-200
                                    rounded-xl
                                    text-slate-800
                                    placeholder-slate-400
                                    outline-none
                                    focus:bg-white
                                    focus:border-blue-500
                                    focus:ring-4
                                    focus:ring-blue-100
                                    transition"
                                placeholder="Masukkan password"
                            >

                            <button
                                type="button"
                                id="togglePassword"
                                aria-label="Tampilkan password"
                                aria-pressed="false"
                                class="absolute inset-y-0 right-0
                                    flex items-center justify-center
                                    w-12 text-slate-400
                                    hover:text-blue-600
                                    transition"
                            >
                                <!-- Ikon mata -->
                                <svg
                                    id="eyeIcon"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="21"
                                    height="21"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    
                    
                    
                    <!-- Button -->
                    
                    <button
                    type="submit"
                    class="w-full py-3.5
                    bg-blue-600
                    hover:bg-blue-700
                    text-white
                    font-semibold
                    rounded-xl
                    shadow-lg shadow-blue-200
                    transition
                    duration-200"
                    >
                    Masuk ke TradeTrack
                    </button>

                </form>

            </div>


            <p class="text-center text-xs text-slate-400 mt-6">

                TradeTrack &copy; <?= date('Y') ?>

            </p>

        </div>

    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');
    
        togglePassword.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
    
            passwordInput.type = isHidden ? 'text' : 'password';
    
            togglePassword.setAttribute(
                'aria-label',
                isHidden ? 'Sembunyikan password' : 'Tampilkan password'
            );
    
            togglePassword.setAttribute(
                'aria-pressed',
                isHidden ? 'true' : 'false'
            );
    
            if (isHidden) {
                // Ikon mata dicoret
                eyeIcon.innerHTML = `
                    <path d="M3 3l18 18"/>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                    <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5
                                c6.4 0 10 7 10 7a14.8 14.8 0 0 1-4 4.6"/>
                    <path d="M6.2 6.2C3.5 8 2 12 2 12
                                s3.6 7 10 7c1.2 0 2.3-.3 3.3-.7"/>
                `;
            } else {
                // Ikon mata normal
                eyeIcon.innerHTML = `
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
                    <circle cx="12" cy="12" r="3"/>
                `;
            }
        });
    </script>


                    
</body>

</html>