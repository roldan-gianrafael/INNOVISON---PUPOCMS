<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill Up - Health Profile</title>
    <script
        src="{{ asset('js/sienna-accessibility-custom.umd.js') }}?v={{ filemtime(public_path('js/sienna-accessibility-custom.umd.js')) }}"
        data-asw-position="bottom-right"
        data-asw-offset="24,12"
        data-asw-size="small"
        defer
    ></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --clinic-maroon: #7f1d2d;
            --clinic-maroon-dark: #5f0012;
            --clinic-yellow: #facc15;
            --panel: #ffffff;
            --field: #f8fafc;
            --border: #d1d5db;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background:
                linear-gradient(rgba(39, 14, 17, 0.82), rgba(22, 8, 8, 0.84)),
                url('{{ asset('images/PUPBG.jpg') }}') center center / cover no-repeat fixed;
            padding: 28px 12px 120px;
        }

        body.health-form-page .system-footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 100;
            box-shadow: 0 -12px 30px rgba(15, 23, 42, 0.18);
        }

        .health-shell {
            max-width: 980px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(127, 29, 45, 0.16);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }

        .health-header {
            height: 12px;
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            border-bottom: 2px solid var(--clinic-yellow);
        }

        .form-intro {
            margin-bottom: 18px;
        }

        .form-intro h1 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 800;
            color: #70131b;
        }

        .form-intro p {
            margin: 8px 0 0;
            font-size: 0.95rem;
            color: #4b5563;
        }

        .section-body {
            padding: 24px 28px 28px;
        }

        .section-title {
            margin: 0 0 16px;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--clinic-maroon);
            border-bottom: 2px solid rgba(127, 29, 45, 0.12);
            padding-bottom: 8px;
        }

        .stepper-shell {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            min-height: 91px;
            position: fixed;
            left: 50%;
            top: 14px;
            transform: translateX(-50%);
            width: min(972px, calc(100vw - 10px));
            box-sizing: border-box;
            z-index: 50;
            background: rgba(255, 255, 255, 0.92);
            padding: 10px;
            border-radius: 16px;
            border: 1px solid rgba(127, 29, 45, 0.12);
            backdrop-filter: blur(8px);
        }

        :where(.asw-menu-btn),
        :where(#studentQuickActionsFab),
        :where(.student-quick-actions-fab-wrap),
        :where(.student-quick-actions-toggle),
        :where(.student-quick-action-btn),
        :where(#studentAccessibilityLaunch),
        :where(#sienna-accessibility-button),
        :where(.sienna-accessibility-button),
        :where(.sienna-accessibility-trigger),
        :where([data-sienna-accessibility-trigger]) {
            position: fixed !important;
            right: 22px !important;
            bottom: 14px !important;
            z-index: 2147483000 !important;
        }

        :where(.asw-menu-btn),
        :where(.asw-menu-btn *),
        :where(#studentQuickActionsFab),
        :where(#studentQuickActionsFab *),
        :where(.student-quick-actions-fab-wrap),
        :where(.student-quick-actions-fab-wrap *),
        :where(#studentAccessibilityLaunch),
        :where(#studentAccessibilityLaunch *),
        :where(#sienna-accessibility-button),
        :where(#sienna-accessibility-button *),
        :where(.sienna-accessibility-button),
        :where(.sienna-accessibility-button *),
        :where(.sienna-accessibility-trigger),
        :where(.sienna-accessibility-trigger *),
        :where([data-sienna-accessibility-trigger]),
        :where([data-sienna-accessibility-trigger] *) {
            pointer-events: auto !important;
        }

        .stepper-spacer {
            height: 109px;
        }

        .step-chip {
            border: 1px solid rgba(127, 29, 45, 0.2);
            border-radius: 14px;
            padding: 12px 14px;
            background: #fff7d6;
            opacity: 0.78;
            transition: all 0.2s ease;
        }

        .step-chip small {
            display: block;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 0.72rem;
            margin-bottom: 2px;
            font-weight: 700;
        }

        .step-chip strong {
            color: #70131b;
            font-size: 0.95rem;
        }

        .step-chip.is-active {
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            border-color: transparent;
            opacity: 1;
            box-shadow: 0 8px 20px rgba(127, 29, 45, 0.24);
        }

        .step-chip.is-active small,
        .step-chip.is-active strong {
            color: #fff;
        }

        .profile-readonly-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }

        .readonly-item {
            border: 1px solid rgba(127, 29, 45, 0.12);
            background: #fff;
            border-radius: 12px;
            padding: 10px 12px;
        }

        .readonly-item small {
            display: block;
            color: #6b7280;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .readonly-item strong {
            color: #111827;
            font-size: 0.93rem;
        }

        .identity-overview {
            margin-bottom: 18px;
            border: 1px solid rgba(127, 29, 45, 0.14);
            border-radius: 20px;
            background:
                radial-gradient(circle at top left, rgba(250, 204, 21, 0.18), transparent 32%),
                linear-gradient(180deg, #ffffff 0%, #fffaf2 100%);
            box-shadow: 0 14px 30px rgba(127, 29, 45, 0.08);
            overflow: hidden;
        }

        .identity-name-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            padding: 16px;
        }

        .identity-field {
            border: 1px solid rgba(127, 29, 45, 0.12);
            background: rgba(255, 255, 255, 0.86);
            border-radius: 14px;
            padding: 12px 14px;
            min-height: 72px;
        }

        .identity-field small,
        .reference-panel small {
            display: block;
            color: #6b7280;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .identity-field strong {
            color: #111827;
            font-size: 1rem;
            font-weight: 800;
            word-break: break-word;
        }

        .reference-panel {
            text-align: center;
            padding: 20px 18px 22px;
            border-top: 1px solid rgba(127, 29, 45, 0.12);
            background: linear-gradient(135deg, rgba(127, 29, 45, 0.98), rgba(95, 0, 18, 0.98));
        }

        .reference-panel small {
            color: rgba(255, 255, 255, 0.78);
            margin-bottom: 6px;
        }

        .reference-panel strong {
            display: block;
            color: #facc15;
            font-size: clamp(1.8rem, 5vw, 3rem);
            line-height: 1;
            font-weight: 950;
            letter-spacing: 0.05em;
            word-break: break-word;
        }

        .upload-instruction-card {
            margin-bottom: 18px;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid rgba(250, 204, 21, 0.42);
            background: linear-gradient(135deg, #fff8d6 0%, #fffef4 100%);
            color: #4b2e05;
        }

        .upload-instruction-card strong {
            display: block;
            color: #70131b;
            font-size: 0.95rem;
            margin-bottom: 5px;
        }

        .upload-instruction-card p {
            margin: 0;
            font-size: 0.86rem;
            line-height: 1.55;
            font-weight: 600;
        }

        .upload-instruction-card ol {
            margin: 0;
            padding-left: 20px;
            color: #4b2e05;
            font-size: 0.86rem;
            line-height: 1.6;
            font-weight: 650;
        }

        .upload-instruction-card li + li {
            margin-top: 4px;
        }

        .form-label {
            font-weight: 700;
            color: #111827;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--field);
            min-height: 46px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(127, 29, 45, 0.5);
            box-shadow: 0 0 0 0.18rem rgba(127, 29, 45, 0.12);
        }

        .upload-card {
            border: 1px dashed rgba(127, 29, 45, 0.35);
            background: linear-gradient(180deg, #fffef6 0%, #fff8dc 100%);
            border-radius: 14px;
            padding: 14px;
            height: 100%;
        }

        .upload-card strong {
            display: block;
            color: #70131b;
            margin-bottom: 6px;
        }

        .upload-card small {
            color: #5b6470;
            display: block;
            margin-top: 6px;
        }

        .requirement-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .requirement-card {
            border: 1px solid rgba(127, 29, 45, 0.18);
            background: #ffffff;
            border-radius: 16px;
            padding: 14px;
            min-height: 100%;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .requirement-card.file-selected {
            border-color: rgba(127, 29, 45, 0.48);
            box-shadow: 0 14px 30px rgba(127, 29, 45, 0.12);
            transform: translateY(-1px);
        }

        .requirement-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
        }

        .requirement-card-header strong {
            display: block;
            color: #70131b;
            font-size: 0.98rem;
            line-height: 1.35;
        }

        .requirement-badge {
            flex: 0 0 auto;
            border-radius: 999px;
            background: #fef3c7;
            color: #7c2d12;
            border: 1px solid rgba(250, 204, 21, 0.45);
            padding: 4px 8px;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .requirement-guideline {
            margin: 8px 0 12px;
            border-left: 4px solid var(--clinic-yellow);
            border-radius: 12px;
            background: #fff8db;
            color: #51340b;
            padding: 10px 12px;
            font-size: 0.82rem;
            line-height: 1.45;
            font-weight: 650;
        }

        .requirement-extra {
            display: none;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(127, 29, 45, 0.12);
        }

        .requirement-card.file-selected .requirement-extra,
        .requirement-card.has-old-data .requirement-extra,
        .requirement-extra.always-visible {
            display: grid;
        }

        .requirement-extra .form-field {
            padding: 9px 10px;
        }

        .requirement-extra .form-field.span-2 {
            grid-column: span 2;
        }

        .certify-row {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 12px;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid rgba(127, 29, 45, 0.18);
            background: #fffaf0;
        }

        .certify-row input {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            accent-color: var(--clinic-maroon);
        }

        .certify-row label {
            color: #374151;
            font-size: 0.84rem;
            line-height: 1.45;
            font-weight: 700;
        }

        .upload-preview-card {
            display: none;
            align-items: center;
            gap: 12px;
            margin-top: 12px;
            padding: 10px;
            border-radius: 14px;
            border: 1px solid rgba(127, 29, 45, 0.16);
            background: #ffffff;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
        }

        .upload-preview-card.is-visible {
            display: flex;
        }

        .requirement-card.has-upload-preview > input[type="file"],
        .upload-card.has-upload-preview > input[type="file"] {
            display: none;
        }

        .upload-preview-thumb {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            border: 1px solid rgba(127, 29, 45, 0.16);
            background:
                linear-gradient(135deg, rgba(127, 29, 45, 0.10), rgba(250, 204, 21, 0.18)),
                #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--clinic-maroon);
            flex: 0 0 auto;
        }

        .upload-preview-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .upload-preview-thumb svg {
            width: 24px;
            height: 24px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }

        .upload-preview-body {
            min-width: 0;
            flex: 1 1 auto;
        }

        .upload-preview-name {
            display: block;
            color: #111827;
            font-size: 0.84rem;
            font-weight: 800;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .upload-preview-meta {
            display: block;
            margin-top: 2px;
            color: #6b7280;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .upload-preview-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 8px;
        }

        .upload-preview-btn {
            border: 1px solid rgba(127, 29, 45, 0.18);
            border-radius: 999px;
            background: #ffffff;
            color: var(--clinic-maroon);
            padding: 7px 11px;
            font-size: 0.72rem;
            line-height: 1;
            font-weight: 850;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .upload-preview-btn:hover {
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            border-color: transparent;
            color: var(--clinic-yellow);
            text-decoration: none;
            transform: translateY(-1px);
        }

        .clinic-select-wrap {
            position: relative;
        }

        .clinic-select-native {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            width: 0;
            height: 0;
            padding: 0;
            border: 0;
            margin: 0;
        }

        .clinic-select-display {
            width: 100%;
            min-height: 46px;
            padding: 11px 48px 11px 14px;
            border: 1px solid rgba(148, 163, 184, 0.20);
            border-radius: 18px;
            font-size: 0.88rem;
            color: #111111;
            background: linear-gradient(180deg, #ffffff 0%, #fff8f6 100%);
            box-shadow:
                0 12px 22px rgba(15, 23, 42, 0.08),
                inset 0 1px 0 rgba(255,255,255,0.86);
            cursor: pointer;
            font-weight: 650;
            text-align: left;
            transition: all 0.2s ease;
        }

        .clinic-select-display:hover {
            border-color: rgba(139, 0, 0, 0.28);
            box-shadow:
                0 10px 18px rgba(139, 0, 0, 0.05),
                inset 0 1px 0 rgba(255,255,255,0.86);
        }

        .clinic-select-display.is-open,
        .clinic-select-display:focus {
            outline: none;
            border-color: var(--clinic-maroon);
            box-shadow:
                0 0 0 4px rgba(139, 0, 0, 0.06),
                0 10px 18px rgba(139, 0, 0, 0.08);
        }

        .clinic-select-wrap::after {
            content: "";
            position: absolute;
            top: 23px;
            right: 17px;
            width: 10px;
            height: 10px;
            border-right: 2px solid var(--clinic-maroon);
            border-bottom: 2px solid var(--clinic-maroon);
            transform: translateY(-65%) rotate(45deg);
            pointer-events: none;
            transition: transform 0.18s ease;
            z-index: 2;
        }

        .clinic-select-wrap::before {
            content: "";
            position: absolute;
            top: 23px;
            right: 40px;
            transform: translateY(-50%);
            width: 1px;
            height: 22px;
            background: rgba(148, 163, 184, 0.24);
            pointer-events: none;
            z-index: 2;
        }

        .clinic-select-wrap.is-open::after {
            transform: translateY(-25%) rotate(225deg);
        }

        .clinic-select-menu {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            right: 0;
            display: none;
            gap: 10px;
            padding: 12px;
            border-radius: 18px;
            border: 1px solid rgba(139, 0, 0, 0.12);
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.14);
            z-index: 90;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .clinic-select-wrap.is-open .clinic-select-menu {
            display: grid;
        }

        .clinic-select-option {
            width: 100%;
            border: 1px solid rgba(148, 163, 184, 0.22);
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            color: #1e293b;
            border-radius: 999px;
            padding: 11px 14px;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.01em;
            text-align: left;
            cursor: pointer;
            transition: all 0.18s ease;
            box-shadow:
                0 12px 22px rgba(15, 23, 42, 0.08),
                0 1px 0 rgba(255,255,255,0.82) inset;
        }

        .clinic-select-option:hover {
            border-color: transparent;
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            color: var(--clinic-yellow);
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(127, 29, 45, 0.22);
        }

        .clinic-select-option.is-selected {
            border-color: transparent;
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            color: #ffffff;
            box-shadow: 0 14px 26px rgba(127, 29, 45, 0.24);
        }

        .btn-row {
            margin-top: 22px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-health {
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-health svg {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            stroke: currentColor;
            stroke-width: 2.4;
            fill: none;
        }

        .btn-health-back {
            background: #e5e7eb;
            color: #111827;
        }

        .btn-health-submit,
        .btn-health-next {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            color: #fff;
            box-shadow: 0 10px 22px rgba(127, 29, 45, 0.28);
            transition: color 0.22s ease, border-color 0.22s ease, transform 0.22s ease, box-shadow 0.22s ease;
            z-index: 0;
        }

        .btn-health-submit::before,
        .btn-health-next::before {
            content: "";
            position: absolute;
            inset: 0;
            background: var(--clinic-yellow);
            transform: translateX(-105%);
            transition: transform 0.34s ease;
            z-index: -1;
        }

        .btn-health-submit:hover,
        .btn-health-submit:focus,
        .btn-health-next:hover,
        .btn-health-next:focus {
            color: var(--clinic-maroon);
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(250, 204, 21, 0.24);
        }

        .btn-health-submit:hover::before,
        .btn-health-submit:focus::before,
        .btn-health-next:hover::before,
        .btn-health-next:focus::before {
            transform: translateX(0);
        }

        .required {
            color: #b91c1c;
        }

        .pwd-toggle {
            display: flex;
            gap: 10px;
            margin-top: 4px;
        }

        .pwd-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .pwd-option {
            min-width: 92px;
            text-align: center;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .pwd-radio:checked + .pwd-option {
            background: linear-gradient(135deg, var(--clinic-maroon) 0%, var(--clinic-maroon-dark) 100%);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 8px 16px rgba(127, 29, 45, 0.2);
        }

        #pwdUploadWrap {
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        #disabilityTypeWrap.is-hidden,
        #pwdUploadWrap.is-hidden {
            display: none;
        }

        .step-panel.is-hidden {
            display: none;
        }

        .step-one-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(127, 29, 45, 0.12);
            background: #fff;
            border-radius: 12px;
            padding: 10px 12px;
        }

        .form-field.span-2 {
            grid-column: span 2;
        }

        .form-field .form-label {
            display: block;
            color: #6b7280;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .form-field .form-control,
        .form-field .form-select {
            border: 0;
            background: transparent;
            box-shadow: none;
            border-radius: 0;
            min-height: 24px;
            padding: 0;
            color: #111827;
            font-weight: 700;
        }
        .form-field .form-control.field-maroon,
        .form-field .form-select.field-maroon {
            border: 1.5px solid rgba(127, 29, 45, 0.52);
            background: linear-gradient(180deg, #fffafb 0%, #fff6f7 100%);
            box-shadow:
                0 8px 18px rgba(127, 29, 45, 0.06),
                inset 0 1px 0 rgba(255,255,255,0.82);
            border-radius: 12px;
            min-height: 46px;
            padding: 10px 12px;
        }
        .form-field .form-control.field-maroon.is-filled,
        .form-field .form-select.field-maroon.is-filled {
            border: 1px solid rgba(209, 213, 219, 0.9);
            background: #ffffff !important;
            background-color: #ffffff !important;
            box-shadow:
                0 6px 14px rgba(15, 23, 42, 0.04),
                inset 0 1px 0 rgba(255,255,255,0.82);
            border-radius: 12px;
            min-height: 46px;
            padding: 10px 12px;
        }
        .form-field .form-control.field-maroon.is-filled:focus,
        .form-field .form-select.field-maroon.is-filled:focus {
            background: #ffffff !important;
            background-color: #ffffff !important;
        }
        .form-field input[type="number"].field-maroon,
        .form-field input[type="number"].field-maroon.is-filled {
            appearance: textfield;
            -moz-appearance: textfield;
        }
        .form-field input[type="number"].field-maroon::-webkit-outer-spin-button,
        .form-field input[type="number"].field-maroon::-webkit-inner-spin-button,
        .form-field input[type="number"].field-maroon.is-filled::-webkit-outer-spin-button,
        .form-field input[type="number"].field-maroon.is-filled::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .form-field .form-control:focus,
        .form-field .form-select:focus {
            border: 0;
            box-shadow: none;
            background: transparent;
        }
        .form-field .form-control.field-maroon:focus,
        .form-field .form-select.field-maroon:focus {
            border: 1.5px solid var(--clinic-maroon);
            background: linear-gradient(180deg, #fffafb 0%, #fff6f7 100%);
            box-shadow:
                0 0 0 0.18rem rgba(127, 29, 45, 0.12),
                0 10px 22px rgba(127, 29, 45, 0.10);
        }

        .step-fill-note {
            margin: 0 0 12px;
            color: #7f1d2d;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .field-helper {
            margin-top: 6px;
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
        }

        .privacy-note {
            margin: 14px 0 0;
            text-align: center;
            font-size: 0.78rem;
            color: #5b6470;
            line-height: 1.5;
        }

        .submit-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(4px);
        }

        .submit-overlay.is-open {
            display: flex;
        }

        .submit-card {
            width: min(360px, calc(100vw - 26px));
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid rgba(16, 185, 129, 0.25);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.28);
            padding: 26px 20px;
            text-align: center;
        }

        .submit-check {
            width: 72px;
            height: 72px;
            border-radius: 999px;
            margin: 0 auto 12px;
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: popIn 0.35s ease;
        }

        .submit-check svg {
            width: 36px;
            height: 36px;
            stroke: #fff;
            stroke-width: 2.6;
            fill: none;
        }

        .submit-card strong {
            display: block;
            color: #111827;
            font-size: 1rem;
        }

        @keyframes popIn {
            0% { transform: scale(0.7); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 768px) {
            body {
                padding-bottom: 132px;
            }

            .stepper-shell,
            .requirement-grid,
            .profile-readonly-grid,
            .identity-name-grid,
            .step-one-grid {
                grid-template-columns: 1fr;
            }

            .requirement-extra {
                grid-template-columns: 1fr;
            }

            .requirement-extra .form-field.span-2 {
                grid-column: span 1;
            }

            .form-field.span-2 {
                grid-column: span 1;
            }

            .stepper-shell {
                top: 8px;
                width: calc(100vw - 16px);
                min-height: auto;
                padding: 8px;
            }

            .stepper-spacer {
                height: 74px;
            }

            .step-chip {
                display: none;
                padding: 10px 12px;
                border-radius: 12px;
            }

            .step-chip.is-active {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                min-height: 48px;
            }

            .step-chip.is-active small {
                margin: 0;
                font-size: 0.68rem;
                white-space: nowrap;
            }

            .step-chip.is-active small::after {
                content: " of 2";
            }

            .step-chip.is-active strong {
                font-size: 0.9rem;
                text-align: right;
                line-height: 1.2;
            }
        }
    </style>
</head>
<body class="health-form-page">
    @php
        $prefill = $healthFormPrefill ?? [];
    @endphp

    <div class="health-shell">
        <div class="health-header"></div>

        <div class="section-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            @php
                $selectedPwd = old('has_disability', $prefill['has_disability'] ?? 'No');
                $stepTwoErrorFields = ['has_disability', 'disability_type', 'medical_certificate', 'doctor_name', 'med_cert_date', 'med_cert_findings', 'chest_xray_result', 'xray_date', 'xray_findings', 'pwd_id_proof', 'health_form_upload', 'health_form_certified', 'student_photo'];
                $startStep = $errors->any() ? 2 : (collect($stepTwoErrorFields)->contains(fn ($field) => $errors->has($field)) ? 2 : 1);
                $displayFullName = trim((string) old('full_name', $prefill['full_name'] ?? $user->name));
                $nameParts = preg_split('/\s+/', $displayFullName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $displayFirstName = trim((string) old('first_name', $prefill['first_name'] ?? ''));
                $displayMiddleName = trim((string) old('middle_name', $prefill['middle_name'] ?? ''));
                $displayLastName = trim((string) old('last_name', $prefill['last_name'] ?? ''));

                if ($displayFirstName === '' && count($nameParts) >= 1) {
                    $displayFirstName = array_shift($nameParts);
                }

                if ($displayLastName === '' && count($nameParts) >= 1) {
                    $displayLastName = array_pop($nameParts);
                }

                if ($displayMiddleName === '' && count($nameParts) > 0) {
                    $displayMiddleName = implode(' ', $nameParts);
                }

                $displayReferenceNumber = trim((string) old('student_number', $prefill['student_number'] ?? $user->student_number));
            @endphp

            <div class="stepper-shell">
                <div class="step-chip {{ $startStep === 1 ? 'is-active' : '' }}" id="chipStep1">
                    <small>Part 1</small>
                    <strong>Admission Reference</strong>
                </div>
                <div class="step-chip {{ $startStep === 2 ? 'is-active' : '' }}" id="chipStep2">
                    <small>Part 2</small>
                    <strong>Clinic Requirements</strong>
                </div>
            </div>
            <div class="stepper-spacer"></div>

            <form action="{{ route('store.health.form') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course_college" value="{{ old('course_college', $prefill['course_college'] ?? $user->course) }}">
                <input type="hidden" name="student_number" value="{{ old('student_number', $prefill['student_number'] ?? $user->student_number) }}">

                <div class="step-panel {{ $startStep === 2 ? 'is-hidden' : '' }}" id="stepPanel1">
                    <div class="form-intro">
                        <h1>Admission Reference</h1>
                        <p>Confirm your admission reference details, then proceed with the required clinic document uploads.</p>
                    </div>

                    <div class="identity-overview">
                        <div class="identity-name-grid">
                            <div class="identity-field">
                                <small>First Name</small>
                                <strong>{{ $displayFirstName !== '' ? $displayFirstName : 'N/A' }}</strong>
                            </div>
                            <div class="identity-field">
                                <small>Middle Name</small>
                                <strong>{{ $displayMiddleName !== '' ? $displayMiddleName : 'N/A' }}</strong>
                            </div>
                            <div class="identity-field">
                                <small>Last Name</small>
                                <strong>{{ $displayLastName !== '' ? $displayLastName : 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="reference-panel">
                            <small>Reference Number</small>
                            <strong>{{ $displayReferenceNumber !== '' ? $displayReferenceNumber : 'Pending' }}</strong>
                        </div>
                    </div>

                    <div class="upload-instruction-card">
                        <strong>Instructions for Uploading Documents</strong>
                        <ol>
                            <li>Prepare clear PDF copies of your medical certificate, chest X-ray result, and completed health form.</li>
                            <li>If you are a PWD, upload your PWD ID in Step 2.</li>
                            <li>Upload your 2x2 photo as JPG or PNG only.</li>
                        </ol>
                    </div>

                    {{--
                        Legacy profile fields hidden for the simplified health form flow.
                        Restore the old visible field grid here if the clinic decides to collect these details again.
                    --}}
                    <input type="hidden" name="school_year" value="{{ old('school_year', $prefill['school_year'] ?? '2025-2026') }}">
                    <input type="hidden" name="home_address" value="{{ old('home_address', $prefill['home_address'] ?? 'NONE') }}">
                    <input type="hidden" name="zipcode" value="{{ old('zipcode', $prefill['zipcode'] ?? 'NONE') }}">
                    <input type="hidden" name="birthday" id="birthday" value="{{ old('birthday', $prefill['birthday'] ?? '2000-01-01') }}">
                    <input type="hidden" name="age" id="age" value="{{ old('age', $prefill['age'] ?? 18) }}">
                    <input type="hidden" name="sex" value="{{ old('sex', $prefill['sex'] ?? 'NONE') }}">
                    <input type="hidden" name="civil_status" value="{{ old('civil_status', $prefill['civil_status'] ?? 'Single') }}">
                    <input type="hidden" name="height" value="{{ old('height', $prefill['height'] ?? 0) }}">
                    <input type="hidden" name="weight" value="{{ old('weight', $prefill['weight'] ?? 0) }}">
                    <input type="hidden" name="blood_type" value="{{ old('blood_type', $prefill['blood_type'] ?? 'Unknown') }}">
                    <input type="hidden" name="contact_no" value="{{ old('contact_no', $prefill['contact_number'] ?? $user->contact_no ?? 'NONE') }}">
                    <input type="hidden" name="guardian_name" value="{{ old('guardian_name', $prefill['guardian_name'] ?? 'NONE') }}">
                    <input type="hidden" name="cellphone" value="{{ old('cellphone', $prefill['cellphone'] ?? 'NONE') }}">
                    <input type="hidden" name="landline" value="{{ old('landline', $prefill['landline'] ?? 'NONE') }}">

                    <div class="btn-row">
                        <a href="{{ url('/student/account') }}" class="btn btn-health btn-health-back">Back</a>
                        <button type="button" class="btn btn-health btn-health-next" id="nextToStep2">Next</button>
                    </div>
                    <p class="privacy-note">
                        Data Privacy Notice: The information you provide is collected for school clinic documentation and health clearance processing only, in compliance with school data privacy requirements.
                    </p>
                </div>

                <div class="step-panel {{ $startStep === 1 ? 'is-hidden' : '' }}" id="stepPanel2">
                    <h2 class="section-title">Clinic Requirements</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-field">
                                <label class="form-label">Are you a PWD? <span class="required">*</span></label>
                                <div class="pwd-toggle" id="pwdToggle">
                                    <input class="pwd-radio" type="radio" name="has_disability" id="pwd_no" value="No" required {{ $selectedPwd !== 'Yes' ? 'checked' : '' }}>
                                    <label class="pwd-option" for="pwd_no">No</label>
                                    <input class="pwd-radio" type="radio" name="has_disability" id="pwd_yes" value="Yes" {{ $selectedPwd === 'Yes' ? 'checked' : '' }}>
                                    <label class="pwd-option" for="pwd_yes">Yes</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8" id="disabilityTypeWrap">
                            <div class="form-field">
                                <label class="form-label">Disability Type <span class="required">*</span></label>
                                <input id="disability_type" type="text" name="disability_type" class="form-control" value="{{ old('disability_type', $prefill['disability_type'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <h2 class="section-title mt-4">Required Documents</h2>
                    <div class="requirement-grid">
                        <div class="requirement-card" id="pwdUploadWrap">
                            <div class="upload-card">
                                <strong>PWD ID (PDF, if PWD is Yes)</strong>
                                <input id="pwd_id_proof" type="file" name="pwd_id_proof" class="form-control" accept=".pdf,application/pdf" data-upload-input data-preview-kind="pdf">
                                <div class="upload-preview-card" data-upload-preview aria-live="polite"></div>
                                <small>Required only when PWD.</small>
                            </div>
                        </div>
                        <div class="requirement-card {{ old('doctor_name') || old('med_cert_date') || old('med_cert_findings') ? 'has-old-data' : '' }}" data-requirement-card>
                            <div class="requirement-card-header">
                                <strong>Medical Certificate (PDF) <span class="required">*</span></strong>
                                <span class="requirement-badge">PDF</span>
                            </div>
                            <p class="requirement-guideline">Please ensure the doctor's signature and PRC License Number are clearly visible.</p>
                            <input type="file" name="medical_certificate" class="form-control" accept=".pdf,application/pdf" required data-requirement-file data-upload-input data-preview-kind="pdf">
                            <div class="upload-preview-card" data-upload-preview aria-live="polite"></div>
                            <small>Allowed: PDF only, max 4MB.</small>
                            <div class="requirement-extra">
                                <div class="form-field span-2">
                                    <label class="form-label" for="doctor_name">Doctor's Full Name <span class="required">*</span></label>
                                    <input id="doctor_name" type="text" name="doctor_name" class="form-control" value="{{ old('doctor_name') }}" maxlength="255" required data-requirement-extra-field>
                                </div>
                                <div class="form-field">
                                    <label class="form-label" for="med_cert_date">Date of Certificate <span class="required">*</span></label>
                                    <input id="med_cert_date" type="date" name="med_cert_date" class="form-control" value="{{ old('med_cert_date') }}" required data-requirement-extra-field>
                                </div>
                                <div class="form-field">
                                    <label class="form-label" for="med_cert_findings">Findings <span class="required">*</span></label>
                                    <div class="clinic-select-wrap" data-clinic-select>
                                        <select id="med_cert_findings" name="med_cert_findings" class="form-select clinic-select-native" required data-requirement-extra-field>
                                            <option value="">Select findings</option>
                                            <option value="No Findings / Normal" {{ old('med_cert_findings') === 'No Findings / Normal' ? 'selected' : '' }}>No Findings / Normal</option>
                                            <option value="With Findings" {{ old('med_cert_findings') === 'With Findings' ? 'selected' : '' }}>With Findings</option>
                                        </select>
                                        <button type="button" class="clinic-select-display" aria-haspopup="listbox" aria-expanded="false">Select findings</button>
                                        <div class="clinic-select-menu" role="listbox" aria-label="Medical certificate findings options">
                                            <button type="button" class="clinic-select-option" data-select-value="No Findings / Normal">No Findings / Normal</button>
                                            <button type="button" class="clinic-select-option" data-select-value="With Findings">With Findings</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="requirement-card {{ old('xray_date') || old('xray_findings') ? 'has-old-data' : '' }}" data-requirement-card>
                            <div class="requirement-card-header">
                                <strong>Chest X-ray Result (PDF) <span class="required">*</span></strong>
                                <span class="requirement-badge">PDF</span>
                            </div>
                            <p class="requirement-guideline">Please upload the official radiologist's written report, not the actual film scanning image.</p>
                            <input type="file" name="chest_xray_result" class="form-control" accept=".pdf,application/pdf" required data-requirement-file data-upload-input data-preview-kind="pdf">
                            <div class="upload-preview-card" data-upload-preview aria-live="polite"></div>
                            <small>Allowed: PDF only, max 4MB.</small>
                            <div class="requirement-extra">
                                <div class="form-field">
                                    <label class="form-label" for="xray_date">Date of Examination <span class="required">*</span></label>
                                    <input id="xray_date" type="date" name="xray_date" class="form-control" value="{{ old('xray_date') }}" required data-requirement-extra-field>
                                </div>
                                <div class="form-field">
                                    <label class="form-label" for="xray_findings">Findings <span class="required">*</span></label>
                                    <div class="clinic-select-wrap" data-clinic-select>
                                        <select id="xray_findings" name="xray_findings" class="form-select clinic-select-native" required data-requirement-extra-field>
                                            <option value="">Select findings</option>
                                            <option value="Normal" {{ old('xray_findings') === 'Normal' ? 'selected' : '' }}>Normal</option>
                                            <option value="With Findings" {{ old('xray_findings') === 'With Findings' ? 'selected' : '' }}>With Findings</option>
                                        </select>
                                        <button type="button" class="clinic-select-display" aria-haspopup="listbox" aria-expanded="false">Select findings</button>
                                        <div class="clinic-select-menu" role="listbox" aria-label="Chest X-ray findings options">
                                            <button type="button" class="clinic-select-option" data-select-value="Normal">Normal</button>
                                            <button type="button" class="clinic-select-option" data-select-value="With Findings">With Findings</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="requirement-card">
                            <div class="requirement-card-header">
                                <strong>Health Form Upload (PDF) <span class="required">*</span></strong>
                                <span class="requirement-badge">PDF</span>
                            </div>
                            <input type="file" name="health_form_upload" class="form-control" accept=".pdf,application/pdf" required data-upload-input data-preview-kind="pdf">
                            <div class="upload-preview-card" data-upload-preview aria-live="polite"></div>
                            <small>Allowed: PDF only, max 4MB.</small>
                            <div class="certify-row">
                                <input id="health_form_certified" type="checkbox" name="health_form_certified" value="1" required {{ old('health_form_certified') ? 'checked' : '' }}>
                                <label for="health_form_certified">I certify that I have completely filled out and signed all sections of the official PUP Health Form.</label>
                            </div>
                        </div>
                        <div class="requirement-card">
                            <div class="requirement-card-header">
                                <strong>2x2 Photo (Image) <span class="required">*</span></strong>
                                <span class="requirement-badge">JPG/PNG</span>
                            </div>
                            <p class="requirement-guideline">Must be a formal photo on a plain white background, taken within the last 6 months.</p>
                            <input type="file" name="student_photo" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required data-upload-input data-preview-kind="image">
                            <div class="upload-preview-card" data-upload-preview aria-live="polite"></div>
                            <small>Allowed: JPG/PNG only, max 2MB.</small>
                        </div>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="btn btn-health btn-health-back" id="backToStep1">Back</button>
                        <button type="submit" class="btn btn-health btn-health-submit">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path>
                                <path d="M17 21v-8H7v8"></path>
                                <path d="M7 3v5h8"></path>
                            </svg>
                            <span>Save Health Profile</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="submit-overlay" id="submitOverlay" aria-hidden="true">
        <div class="submit-card">
            <div class="submit-check" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
            </div>
            <strong>Thank you for submission.</strong>
        </div>
    </div>

    <script>
        (function () {
            const form = document.querySelector('form[action="{{ route('store.health.form') }}"]');
            const stepPanel1 = document.getElementById('stepPanel1');
            const stepPanel2 = document.getElementById('stepPanel2');
            const chipStep1 = document.getElementById('chipStep1');
            const chipStep2 = document.getElementById('chipStep2');
            const nextToStep2Btn = document.getElementById('nextToStep2');
            const backToStep1Btn = document.getElementById('backToStep1');
            const birthdayInput = document.getElementById('birthday');
            const ageInput = document.getElementById('age');
            const disabilityRadios = document.querySelectorAll('input[name="has_disability"]');
            const disabilityTypeInput = document.getElementById('disability_type');
            const disabilityTypeWrap = document.getElementById('disabilityTypeWrap');
            const pwdProofInput = document.getElementById('pwd_id_proof');
            const pwdUploadWrap = document.getElementById('pwdUploadWrap');
            const submitOverlay = document.getElementById('submitOverlay');
            const requirementFiles = document.querySelectorAll('[data-requirement-file]');
            const clinicSelects = Array.from(document.querySelectorAll('[data-clinic-select]'));
            const uploadInputs = Array.from(document.querySelectorAll('[data-upload-input]'));
            let currentStep = {{ $startStep }};
            let isSubmitting = false;

            function setStep(step) {
                currentStep = step;
                const showStep1 = step === 1;
                stepPanel1?.classList.toggle('is-hidden', !showStep1);
                stepPanel2?.classList.toggle('is-hidden', showStep1);
                chipStep1?.classList.toggle('is-active', showStep1);
                chipStep2?.classList.toggle('is-active', !showStep1);
            }

            function validateStepOne() {
                if (!stepPanel1) return true;
                const requiredFields = Array.from(stepPanel1.querySelectorAll('input[required], select[required], textarea[required]'));
                let isValid = true;

                requiredFields.forEach((field) => {
                    if (typeof field.reportValidity === 'function') {
                        const valid = field.reportValidity();
                        if (!valid && isValid) {
                            field.focus();
                            isValid = false;
                        }
                    } else if (!field.checkValidity()) {
                        if (isValid) {
                            field.focus();
                            isValid = false;
                        }
                    }
                });

                return isValid;
            }

            function updateAgeFromBirthday() {
                if (!birthdayInput || !ageInput || !birthdayInput.value) return;
                const birthday = new Date(birthdayInput.value);
                if (Number.isNaN(birthday.getTime())) return;

                const today = new Date();
                let age = today.getFullYear() - birthday.getFullYear();
                const monthDiff = today.getMonth() - birthday.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
                    age--;
                }

                if (age >= 0) {
                    ageInput.value = age;
                }
            }

            function togglePwdRequirements() {
                if (!disabilityTypeInput || !pwdProofInput) return;
                const selected = document.querySelector('input[name="has_disability"]:checked');
                const isPwd = selected?.value === 'Yes';

                disabilityTypeInput.required = isPwd;
                disabilityTypeInput.disabled = !isPwd;
                pwdProofInput.required = isPwd;
                pwdProofInput.disabled = !isPwd;
                disabilityTypeWrap?.classList.toggle('is-hidden', !isPwd);
                pwdUploadWrap?.classList.toggle('is-hidden', !isPwd);

                if (!isPwd) {
                    disabilityTypeInput.value = '';
                    pwdProofInput.value = '';
                }
            }

            function syncRequirementCard(fileInput) {
                const card = fileInput?.closest('[data-requirement-card]');
                if (!card) return;
                const hasFile = Boolean(fileInput.files && fileInput.files.length > 0);
                const hasOldData = card.classList.contains('has-old-data');
                const shouldEnableExtraFields = hasFile || hasOldData;

                card.classList.toggle('file-selected', hasFile);
                card.querySelectorAll('[data-requirement-extra-field]').forEach((field) => {
                    field.disabled = !shouldEnableExtraFields;
                });
            }

            function closeClinicSelect(wrap) {
                const display = wrap?.querySelector('.clinic-select-display');
                wrap?.classList.remove('is-open');
                display?.classList.remove('is-open');
                display?.setAttribute('aria-expanded', 'false');
            }

            function syncClinicSelect(wrap) {
                const select = wrap?.querySelector('select');
                const display = wrap?.querySelector('.clinic-select-display');
                const options = Array.from(wrap?.querySelectorAll('.clinic-select-option') || []);
                if (!select || !display) return;

                const selectedValue = select.value || '';
                const selectedText = selectedValue
                    ? (select.options[select.selectedIndex]?.text || selectedValue)
                    : 'Select findings';

                display.textContent = selectedText;
                options.forEach((option) => {
                    option.classList.toggle('is-selected', option.dataset.selectValue === selectedValue);
                });
            }

            function initializeClinicSelect(wrap) {
                const select = wrap?.querySelector('select');
                const display = wrap?.querySelector('.clinic-select-display');
                const options = Array.from(wrap?.querySelectorAll('.clinic-select-option') || []);
                if (!select || !display) return;

                syncClinicSelect(wrap);

                display.addEventListener('click', () => {
                    const shouldOpen = !wrap.classList.contains('is-open');
                    clinicSelects.forEach((otherWrap) => {
                        if (otherWrap !== wrap) {
                            closeClinicSelect(otherWrap);
                        }
                    });
                    wrap.classList.toggle('is-open', shouldOpen);
                    display.classList.toggle('is-open', shouldOpen);
                    display.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
                });

                options.forEach((option) => {
                    option.addEventListener('click', () => {
                        select.value = option.dataset.selectValue || '';
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        syncClinicSelect(wrap);
                        closeClinicSelect(wrap);
                    });
                });

                select.addEventListener('change', () => syncClinicSelect(wrap));
            }

            function formatFileSize(bytes) {
                if (!Number.isFinite(bytes) || bytes <= 0) {
                    return 'Selected file';
                }

                if (bytes < 1024 * 1024) {
                    return Math.max(1, Math.round(bytes / 1024)) + ' KB';
                }

                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            function renderUploadPreview(input) {
                const previewScope = input.closest('.upload-card, .requirement-card');
                const preview = previewScope?.querySelector('[data-upload-preview]');
                if (!preview) return;

                if (preview.dataset.objectUrl) {
                    URL.revokeObjectURL(preview.dataset.objectUrl);
                    preview.dataset.objectUrl = '';
                }

                const file = input.files && input.files[0] ? input.files[0] : null;
                if (!file) {
                    previewScope?.classList.remove('has-upload-preview');
                    preview.classList.remove('is-visible');
                    preview.innerHTML = '';
                    return;
                }

                const objectUrl = URL.createObjectURL(file);
                preview.dataset.objectUrl = objectUrl;
                const isImage = input.dataset.previewKind === 'image' || file.type.startsWith('image/');
                const safeName = file.name.replace(/[&<>"']/g, (char) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                })[char]);

                const thumbMarkup = isImage
                    ? `<img src="${objectUrl}" alt="">`
                    : `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><path d="M8 13h8"></path><path d="M8 17h5"></path></svg>`;

                preview.innerHTML = `
                    <div class="upload-preview-thumb">${thumbMarkup}</div>
                    <div class="upload-preview-body">
                        <span class="upload-preview-name">${safeName}</span>
                        <span class="upload-preview-meta">${isImage ? 'Image preview' : 'PDF document'} - ${formatFileSize(file.size)}</span>
                        <div class="upload-preview-actions">
                            <a class="upload-preview-btn" href="${objectUrl}" target="_blank" rel="noopener noreferrer">View</a>
                            <button type="button" class="upload-preview-btn" data-upload-replace>Replace</button>
                        </div>
                    </div>
                `;
                previewScope?.classList.add('has-upload-preview');
                preview.classList.add('is-visible');

                preview.querySelector('[data-upload-replace]')?.addEventListener('click', () => {
                    input.click();
                });
            }

            birthdayInput?.addEventListener('change', updateAgeFromBirthday);
            disabilityRadios.forEach((radio) => {
                radio.addEventListener('change', togglePwdRequirements);
            });
            requirementFiles.forEach((fileInput) => {
                syncRequirementCard(fileInput);
                fileInput.addEventListener('change', () => syncRequirementCard(fileInput));
            });
            clinicSelects.forEach(initializeClinicSelect);
            uploadInputs.forEach((input) => {
                renderUploadPreview(input);
                input.addEventListener('change', () => renderUploadPreview(input));
            });
            document.addEventListener('click', (event) => {
                clinicSelects.forEach((wrap) => {
                    if (!wrap.contains(event.target)) {
                        closeClinicSelect(wrap);
                    }
                });
            });
            togglePwdRequirements();

            const maroonFields = Array.from(document.querySelectorAll('.field-maroon'));

            function syncMaroonFieldState(field) {
                if (!field) return;
                const value = typeof field.value === 'string' ? field.value.trim() : '';
                field.classList.toggle('is-filled', value !== '');
            }

            maroonFields.forEach((field) => {
                syncMaroonFieldState(field);
                field.addEventListener('input', () => syncMaroonFieldState(field));
                field.addEventListener('change', () => syncMaroonFieldState(field));
            });

            nextToStep2Btn?.addEventListener('click', () => {
                if (!validateStepOne()) {
                    return;
                }
                setStep(2);
                stepPanel2?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            backToStep1Btn?.addEventListener('click', () => {
                setStep(1);
                stepPanel1?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            form?.addEventListener('submit', (event) => {
                if (isSubmitting) {
                    return;
                }

                if (currentStep === 1) {
                    event.preventDefault();
                    if (!validateStepOne()) {
                        return;
                    }
                    setStep(2);
                    return;
                }

                if (!form.checkValidity()) {
                    return;
                }

                event.preventDefault();
                isSubmitting = true;
                submitOverlay?.classList.add('is-open');
                const submitButtons = form.querySelectorAll('button[type="submit"], button[type="button"], a.btn');
                submitButtons.forEach((btn) => {
                    btn.setAttribute('aria-disabled', 'true');
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.72';
                });

                window.setTimeout(() => {
                    form.submit();
                }, 850);
            });

            updateAgeFromBirthday();
            togglePwdRequirements();
            setStep(currentStep);
        })();
    </script>

    @include('partials.student_voice_input_support')
    @include('partials.system_footer')
</body>
</html>
