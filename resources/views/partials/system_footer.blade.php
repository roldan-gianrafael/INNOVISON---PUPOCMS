@once
    <style>
        .system-footer {
            width: 100%;
            margin-top: clamp(24px, 4vw, 40px);
            background: #23272b;
            border-top: 3px solid #7a0c10;
            border-bottom: 2px solid rgba(255, 255, 255, 0.72);
            color: #f8fafc;
        }

        .system-footer__inner {
            max-width: 1280px;
            margin: 0 auto;
            min-height: 58px;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
            text-align: center;
            font-size: 14px;
            line-height: 1.4;
        }

        .system-footer strong {
            color: #fff3bf;
            font-weight: 800;
        }

        .system-footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
        }

        .system-footer a:hover,
        .system-footer a:focus-visible {
            color: #facc15;
            text-decoration: underline;
            outline: none;
        }

        .system-footer__separator {
            width: 2px;
            height: 18px;
            background: rgba(255, 255, 255, 0.28);
            border-radius: 999px;
        }

        @media (max-width: 640px) {
            .system-footer__inner {
                flex-direction: column;
                gap: 8px;
                padding-inline: 18px;
            }

            .system-footer__separator {
                display: none;
            }
        }
    </style>
@endonce

<footer class="system-footer" role="contentinfo" aria-label="System footer">
    <div class="system-footer__inner">
        <span>&copy; 1998-{{ now()->year }} <strong>Polytechnic University of the Philippines</strong></span>
        <span class="system-footer__separator" aria-hidden="true"></span>
        <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a>
        <span class="system-footer__separator" aria-hidden="true"></span>
        <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">Privacy Statement</a>
    </div>
</footer>
