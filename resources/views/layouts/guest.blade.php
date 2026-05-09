<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Inter', sans-serif;
                margin: 0;
                padding: 0;
            }

            .auth-wrapper {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                overflow: hidden;
            }

            /* Background image */
            .auth-bg {
                position: absolute;
                inset: 0;
                background-image: url('/images/backgroundmangga.jpeg');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                filter: brightness(0.55) saturate(1.1);
                z-index: 0;
                transition: transform 0.5s ease;
            }

            /* Dark gradient overlay */
            .auth-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    135deg,
                    rgba(10, 30, 10, 0.6) 0%,
                    rgba(30, 70, 20, 0.4) 50%,
                    rgba(10, 30, 10, 0.6) 100%
                );
                z-index: 1;
            }

            /* Glassmorphism card */
            .auth-card {
                position: relative;
                z-index: 10;
                width: 100%;
                max-width: 440px;
                margin: 1.5rem;
                padding: 2.5rem 2rem;
                background: rgba(255, 255, 255, 0.12);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.25);
                border-radius: 1.25rem;
                box-shadow:
                    0 8px 32px rgba(0, 0, 0, 0.4),
                    0 2px 8px rgba(0, 0, 0, 0.2),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
                color: #fff;
            }

            /* Logo area */
            .auth-logo {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 1.75rem;
                gap: 0.5rem;
            }

            .auth-logo a {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
                text-decoration: none;
            }

            .auth-logo img {
                height: 4rem;
                width: auto;
                filter: drop-shadow(0 2px 12px rgba(134, 239, 172, 0.6));
            }

            .auth-logo-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: #ffffff;
                letter-spacing: 0.02em;
                text-shadow: 0 1px 4px rgba(0,0,0,0.5);
            }

            .auth-logo-subtitle {
                font-size: 0.75rem;
                color: rgba(255,255,255,0.65);
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            /* Override input styles inside glass card */
            .auth-card label {
                color: rgba(255, 255, 255, 0.85) !important;
                font-weight: 500;
                font-size: 0.875rem;
            }

            .auth-card input,
            .auth-card select {
                background: rgba(255, 255, 255, 0.1) !important;
                border: 1px solid rgba(255, 255, 255, 0.25) !important;
                color: #ffffff !important;
                border-radius: 0.5rem !important;
            }

            .auth-card input::placeholder {
                color: rgba(255, 255, 255, 0.45) !important;
            }

            .auth-card input:focus,
            .auth-card select:focus {
                background: rgba(255, 255, 255, 0.18) !important;
                border-color: rgba(134, 239, 172, 0.7) !important;
                box-shadow: 0 0 0 3px rgba(134, 239, 172, 0.2) !important;
                outline: none !important;
            }

            .auth-card select option {
                background: #1a3a1a;
                color: #fff;
            }

            .auth-card a {
                color: rgba(134, 239, 172, 0.9) !important;
            }

            .auth-card a:hover {
                color: #86efac !important;
            }

            .auth-card span {
                color: rgba(255, 255, 255, 0.7);
            }

            /* Primary button */
            .auth-card button[type="submit"],
            .auth-card .auth-btn {
                background: linear-gradient(135deg, #16a34a, #15803d) !important;
                color: #ffffff !important;
                border: none !important;
                padding: 0.6rem 1.5rem !important;
                border-radius: 0.5rem !important;
                font-weight: 600 !important;
                font-size: 0.875rem !important;
                cursor: pointer !important;
                transition: all 0.2s ease !important;
                box-shadow: 0 2px 8px rgba(22, 163, 74, 0.4) !important;
            }

            .auth-card button[type="submit"]:hover {
                background: linear-gradient(135deg, #15803d, #166534) !important;
                box-shadow: 0 4px 16px rgba(22, 163, 74, 0.5) !important;
                transform: translateY(-1px) !important;
            }

            /* Error messages */
            .auth-card .text-red-600,
            .auth-card [class*="text-red"] {
                color: #fca5a5 !important;
            }

            /* Checkbox */
            .auth-card input[type="checkbox"] {
                width: 1rem !important;
                height: 1rem !important;
            }

            /* Bottom decorative bar */
            .auth-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 2rem;
                right: 2rem;
                height: 3px;
                background: linear-gradient(90deg, transparent, #4ade80, #86efac, #4ade80, transparent);
                border-radius: 0 0 4px 4px;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="auth-wrapper">
            <!-- Background image -->
            <div class="auth-bg"></div>
            <!-- Overlay -->
            <div class="auth-overlay"></div>

            <!-- Card -->
            <div class="auth-card">
                <!-- Logo -->
                <div class="auth-logo">
                    <a href="/">
                        <img src="/images/logoManggo.jpeg" alt="Mango GDD Logo">
                        <span class="auth-logo-title">Mango GDD</span>
                        <span class="auth-logo-subtitle">Monitoring System</span>
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </body>
</html>
