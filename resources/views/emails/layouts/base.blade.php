<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'LibraFlow – Bibliothèque')</title>
    <style>
        /* ── Reset & base ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #3d3d4e;
            line-height: 1.6;
        }

        /* ── Wrapper ── */
        .email-wrapper {
            width: 100%;
            background-color: #f4f4f7;
            padding: 40px 16px;
        }

        /* ── Card ── */
        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        /* ── Header ── */
        .email-header {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            padding: 32px 40px;
            text-align: center;
        }
        .email-header .logo {
            display: inline-block;
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }
        .email-header .logo span {
            color: #c4b5fd;
        }
        .email-header .tagline {
            font-size: 13px;
            color: rgba(255,255,255,0.75);
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        /* ── Body ── */
        .email-body {
            padding: 40px 40px 32px;
        }
        .email-body h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1e1b4b;
            margin-bottom: 16px;
            line-height: 1.3;
        }
        .email-body p {
            font-size: 15px;
            color: #4b5563;
            margin-bottom: 16px;
        }
        .email-body p strong {
            color: #1e1b4b;
        }

        /* ── Info box ── */
        .info-box {
            background-color: #f0f4ff;
            border-left: 4px solid #4F46E5;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin: 24px 0;
        }
        .info-box p {
            margin: 0;
            font-size: 14px;
            color: #374151;
        }
        .info-box .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 600;
        }

        /* ── CTA Button ── */
        .email-cta {
            text-align: center;
            margin: 28px 0;
        }
        .btn-primary {
            display: inline-block;
            background-color: #4F46E5;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 8px;
            letter-spacing: 0.3px;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }

        /* ── Alert / warning box ── */
        .alert-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 0 8px 8px 0;
            padding: 14px 18px;
            margin: 20px 0;
        }
        .alert-box p {
            margin: 0;
            font-size: 14px;
            color: #92400e;
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 28px 0;
        }

        /* ── Footer ── */
        .email-footer {
            background-color: #f9fafb;
            padding: 24px 40px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        .email-footer p {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 4px;
        }
        .email-footer .footer-brand {
            font-size: 13px;
            font-weight: 700;
            color: #4F46E5;
        }
        .email-footer .footer-address {
            font-size: 11px;
            color: #d1d5db;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">

            {{-- Header avec logo --}}
            <div class="email-header">
                <div class="logo">Libra<span>Flow</span></div>
                <div class="tagline">Bibliothèque Numérique</div>
            </div>

            {{-- Contenu principal --}}
            <div class="email-body">
                @yield('content')
            </div>

            {{-- Footer --}}
            <div class="email-footer">
                <p class="footer-brand">LibraFlow – Bibliothèque</p>
                <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                <p class="footer-address">
                    École Supérieure de Génie Logiciel &amp; Systèmes d'Information<br>
                    Service Bibliothèque · bibliotheque@libraflow.local
                </p>
            </div>

        </div>
    </div>
</body>
</html>
