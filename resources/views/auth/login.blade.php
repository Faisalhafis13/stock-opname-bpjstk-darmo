<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Stok Opname BPJS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <style>
        :root {
            --primary: #0d6efd;
            --primary-dark: #0a58ca;
            --text-main: #212529;
            --text-muted: #6c757d;
            --border-color: #e5e7eb;
            --background: #f4f6f9;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            background: var(--background);
            color: var(--text-main);
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            border: 1px solid rgba(0, 0, 0, .04);
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 16px 45px rgba(33, 37, 41, .09);
            overflow: hidden;
        }

        .login-card-body {
            padding: 38px;
        }

        .login-icon {
            width: 76px;
            height: 76px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            background: rgba(13, 110, 253, .10);
            color: var(--primary);
            font-size: 36px;
        }

        .login-title {
            margin-bottom: 8px;
            font-size: 25px;
            font-weight: 750;
            letter-spacing: -.5px;
        }

        .login-subtitle {
            margin-bottom: 0;
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .form-label {
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 650;
        }

        .input-group-text {
            min-width: 46px;
            justify-content: center;
            background: #fff;
            border-color: var(--border-color);
            color: #6c757d;
        }

        .form-control {
            min-height: 48px;
            border-color: var(--border-color);
            font-size: 14px;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, .12);
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
            color: var(--primary);
        }

        .password-toggle {
            min-width: 46px;
            background: #fff;
            border-color: var(--border-color);
            color: #6c757d;
        }

        .password-toggle:hover {
            background: #f8f9fa;
            color: var(--primary);
        }

        .btn-login {
            min-height: 49px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 650;
            transition: background-color .2s, transform .2s;
        }

        .btn-login:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-login:disabled {
            cursor: wait;
            opacity: .8;
        }

        .login-alert {
            border: 0;
            border-radius: 10px;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .login-footer {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid #f0f0f0;
            color: var(--text-muted);
            font-size: 12px;
            text-align: center;
        }

        .field-error {
            display: none;
            margin-top: 6px;
            color: #dc3545;
            font-size: 12px;
        }

        .field-error.show {
            display: block;
        }

        @media (max-width: 480px) {
            .login-wrapper {
                padding: 16px;
            }

            .login-card-body {
                padding: 28px 22px;
            }

            .login-title {
                font-size: 23px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <main class="card login-card">
        <div class="login-card-body">

            {{-- Header --}}
            <div class="text-center mb-4">

                <div class="login-icon">
                    <i class="bi bi-box-seam" aria-hidden="true"></i>
                </div>

                <h1 class="login-title">
                    Stok Opname
                </h1>

                <p class="login-subtitle">
                    Sistem Informasi Stok Opname<br>
                    BPJS Ketenagakerjaan
                </p>

            </div>

            {{-- Notifikasi AJAX --}}
            <div
                id="login-alert"
                class="alert login-alert d-none"
                role="alert"
                aria-live="polite"
            ></div>

            {{-- Notifikasi session, misalnya setelah logout --}}
            @if (session('success'))
                <div class="alert alert-success login-alert" role="alert">
                    <i class="bi bi-check-circle me-2" aria-hidden="true"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form login --}}
            <form
                id="login-form"
                method="POST"
                action="{{ route('login') }}"
            >
                @csrf

                {{-- Email --}}
                <div class="mb-3">

                    <label for="email" class="form-label">
                        Email
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                        </span>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="Masukkan email"
                            autocomplete="username"
                            required
                            autofocus
                            aria-describedby="email-error"
                        >
                    </div>

                    <div
                        id="email-error"
                        class="field-error"
                        role="alert"
                    ></div>

                    @error('email')
                        <div class="field-error show server-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Password --}}
                <div class="mb-4">

                    <label for="password" class="form-label">
                        Password
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock" aria-hidden="true"></i>
                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                            aria-describedby="password-error"
                        >

                        <button
                            type="button"
                            id="toggle-password"
                            class="btn password-toggle"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>

                    <div
                        id="password-error"
                        class="field-error"
                        role="alert"
                    ></div>

                    @error('password')
                        <div class="field-error show server-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Tombol login --}}
                <button
                    type="submit"
                    id="login-button"
                    class="btn btn-primary w-100 btn-login"
                >
                    <span id="login-button-content">
                        <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>
                        Login
                    </span>

                    <span id="login-loading" class="d-none">
                        <span
                            class="spinner-border spinner-border-sm me-2"
                            aria-hidden="true"
                        ></span>
                        Memproses login...
                    </span>
                </button>

            </form>

            <div class="login-footer">
                <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                Halaman autentikasi Sistem Informasi Stok Opname
            </div>

        </div>
    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('login-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const loginButton = document.getElementById('login-button');
    const buttonContent = document.getElementById('login-button-content');
    const loadingContent = document.getElementById('login-loading');
    const alertBox = document.getElementById('login-alert');
    const togglePassword = document.getElementById('toggle-password');

    /**
     * Menampilkan notifikasi.
     */
    function showAlert(message, type = 'danger') {
        alertBox.className = `alert login-alert alert-${type}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
    }

    /**
     * Menyembunyikan notifikasi.
     */
    function hideAlert() {
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
    }

    /**
     * Menghapus pesan validasi sebelumnya.
     */
    function clearErrors() {
        document.querySelectorAll('.field-error:not(.server-error)').forEach(function (element) {
            element.textContent = '';
            element.classList.remove('show');
        });

        document.querySelectorAll('.server-error').forEach(function (element) {
            element.remove();
        });

        emailInput.classList.remove('is-invalid');
        passwordInput.classList.remove('is-invalid');
    }

    /**
     * Menampilkan pesan kesalahan pada input.
     */
    function showFieldError(field, message) {
        const errorElement = document.getElementById(`${field}-error`);
        const input = document.getElementById(field);

        if (!errorElement || !input) {
            return;
        }

        errorElement.textContent = message;
        errorElement.classList.add('show');
        input.classList.add('is-invalid');
    }

    /**
     * Mengatur status tombol login.
     */
    function setLoading(isLoading) {
        loginButton.disabled = isLoading;
        buttonContent.classList.toggle('d-none', isLoading);
        loadingContent.classList.toggle('d-none', !isLoading);
    }

    /**
     * Menampilkan atau menyembunyikan password.
     */
    togglePassword.addEventListener('click', function () {
        const showPassword = passwordInput.type === 'password';

        passwordInput.type = showPassword ? 'text' : 'password';

        const icon = togglePassword.querySelector('i');

        icon.className = showPassword
            ? 'bi bi-eye-slash'
            : 'bi bi-eye';

        togglePassword.setAttribute(
            'aria-label',
            showPassword ? 'Sembunyikan password' : 'Tampilkan password'
        );

        togglePassword.setAttribute(
            'aria-pressed',
            String(showPassword)
        );
    });

    /**
     * Menghapus kesalahan saat input diperbaiki.
     */
    [emailInput, passwordInput].forEach(function (input) {
        input.addEventListener('input', function () {
            input.classList.remove('is-invalid');

            const errorElement = document.getElementById(`${input.name}-error`);

            if (errorElement) {
                errorElement.textContent = '';
                errorElement.classList.remove('show');
            }

            hideAlert();
        });
    });

    /**
     * Proses login menggunakan AJAX.
     */
    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (loginButton.disabled) {
            return;
        }

        clearErrors();
        hideAlert();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        setLoading(true);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form),
                credentials: 'same-origin'
            });

            const data = await response.json().catch(function () {
                return {};
            });

            if (response.ok && data.success) {
                showAlert(
                    data.message || 'Login berhasil.',
                    'success'
                );

                window.location.assign(
                    data.redirect || @json(route('dashboard'))
                );

                return;
            }

            if (response.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(function ([field, messages]) {
                    if (field === 'email' || field === 'password') {
                        const message = Array.isArray(messages)
                            ? messages[0]
                            : messages;

                        showFieldError(field, message);
                    }
                });

                showAlert(
                    data.message ||
                    'Periksa kembali email dan password yang dimasukkan.'
                );

            } else if (response.status === 419) {
                showAlert(
                    'Sesi formulir telah kedaluwarsa. Muat ulang halaman lalu coba lagi.'
                );

            } else {
                showAlert(
                    data.message ||
                    'Login belum berhasil. Silakan coba kembali.'
                );
            }

        } catch (error) {
            showAlert(
                'Tidak dapat terhubung ke server. Periksa koneksi lalu coba lagi.'
            );

        } finally {
            setLoading(false);
        }
    });
});
</script>

</body>
</html>
