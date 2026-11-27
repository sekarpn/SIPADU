@extends('layouts.panel')

@section('panel')

@php
    $user = auth()->user();
    $role = strtolower((string) data_get($user, 'role', 'mahasiswa'));
    $isAdmin = $role === 'admin';
    $isEmployee = $role === 'pegawai';

    $profileUpdateRoute = $isAdmin
        ? route('admin.profile.update')
        : ($isEmployee ? route('pegawai.profile.update') : route('borrower.profile.update'));

    $profilePasswordRoute = $isAdmin
        ? route('admin.profile.password')
        : ($isEmployee ? route('pegawai.profile.password') : route('borrower.profile.password'));

    $profilePreferencesRoute = $isAdmin
        ? route('admin.profile.preferences')
        : ($isEmployee ? route('pegawai.profile.preferences') : route('borrower.profile.preferences'));

    $userName = (string) data_get($user, 'name', 'Pengguna');
    $userEmail = (string) data_get($user, 'email', '');
    $initial = mb_strtoupper(mb_substr(trim($userName) !== '' ? trim($userName) : 'U', 0, 2));

    $rawPhotoPath = trim((string) data_get($user, 'profile_photo_path', ''));
    $cleanPhotoPath = ltrim(preg_replace('#^(public/|storage/)#', '', str_replace('\\', '/', $rawPhotoPath)), '/');

    $photoUrl = null;
    if ($cleanPhotoPath !== '') {
        if (\Illuminate\Support\Str::startsWith($rawPhotoPath, ['http://', 'https://', 'data:'])) {
            $photoUrl = $rawPhotoPath;
        } else {
            $publicStorageFile = public_path('storage/' . $cleanPhotoPath);
            $appStorageFile = storage_path('app/public/' . $cleanPhotoPath);

            if (is_file($publicStorageFile)) {
                $photoUrl = asset('storage/' . $cleanPhotoPath) . '?v=' . filemtime($publicStorageFile);
            } elseif (is_file($appStorageFile)) {
                $mime = @mime_content_type($appStorageFile) ?: 'image/jpeg';
                $photoUrl = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($appStorageFile));
            }
        }
    }

    $roleBadgeLabel = $isAdmin ? 'ADMINISTRATOR' : ($isEmployee ? 'PEGAWAI' : 'MAHASISWA');

    $faculties = config('organization.faculties', []);
    $studyProgramsByFaculty = config('organization.study_programs', []);
    $employeeUnits = config('organization.employee_units', []);
    $positions = config('organization.positions', []);

    $selectedFaculty = (string) old('faculty', data_get($user, 'faculty', ''));
    $selectedStudyProgram = (string) old('study_program', data_get($user, 'study_program', ''));
    $selectedEmployeeUnit = (string) old('employee_unit', data_get($user, 'employee_unit', ''));
    $selectedPosition = (string) old('position', data_get($user, 'position', ''));

    $availableStudyPrograms = $selectedFaculty !== '' && isset($studyProgramsByFaculty[$selectedFaculty])
        ? $studyProgramsByFaculty[$selectedFaculty]
        : [];

    $hasIdentityErrors = $errors->hasAny(['profile_photo']);
    $hasPersonalErrors = $errors->hasAny(['name', 'email', 'phone']);
    $hasOrgErrors = $errors->hasAny(['nim_nip', 'faculty', 'study_program', 'employee_unit', 'position']);

    $incompleteBanner = (bool) session('profile_incomplete');
    $initIdentityEdit = $hasIdentityErrors;
    $initPersonalEdit = $hasPersonalErrors || ($incompleteBanner && (trim($userName) === '' || trim($userEmail) === '' || trim((string) data_get($user, 'phone', '')) === ''));
    $initOrgEdit = $hasOrgErrors || ($incompleteBanner && (
        trim((string) data_get($user, 'nim_nip', '')) === '' ||
        (!$isAdmin && !$isEmployee && (trim((string) data_get($user, 'faculty', '')) === '' || trim((string) data_get($user, 'study_program', '')) === '')) ||
        ($isEmployee && (trim((string) data_get($user, 'employee_unit', '')) === '' || trim((string) data_get($user, 'position', '')) === ''))
    ));

    $activeTab = request('tab', 'profil');
    if ($errors->hasAny(['current_password', 'password', 'password_confirmation']) || session('password_success')) {
        $activeTab = 'password';
    } elseif (session('preferences_success')) {
        $activeTab = 'notifikasi';
    }
    if (!in_array($activeTab, ['profil', 'password', 'notifikasi'], true)) {
        $activeTab = 'profil';
    }
@endphp

<style>
    .settings-shell {
        --st-ink: #172b20;
        --st-muted: #556b5f;
        --st-subtle: #68736c;
        --st-line: rgba(22, 54, 38, .09);
        --st-green: #1f7a49;
        --st-green-mid: #2f7d58;
        --st-green-soft: rgba(47, 125, 88, .12);
        --st-glass: linear-gradient(145deg, rgba(255, 255, 255, .64) 0%, rgba(255, 255, 255, .36) 100%);
        --st-card-glass: linear-gradient(145deg, rgba(255, 255, 255, .74) 0%, rgba(255, 255, 255, .44) 100%);
        --st-border: 1px solid rgba(255, 255, 255, .66);
        --st-shadow-soft: 0 4px 16px rgba(15, 23, 42, .045), inset 0 1px 0 rgba(255, 255, 255, .75);
        --st-locked-bg: #eef1f4;
        --st-locked-border: #dde2e7;
        --st-locked-ink: #64748b;
        width: 100%;
        padding: 0 0 36px;
        color: var(--st-ink);
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
    }

    /* PAGE HEADER */
    .settings-header {
        margin-bottom: 22px;
    }

    .settings-header h1 {
        margin: 0;
        font-size: 25px;
        font-weight: 800;
        letter-spacing: -.03em;
        color: var(--st-ink);
    }

    .settings-header p {
        margin: 5px 0 0;
        font-size: 13px;
        color: var(--st-muted);
    }

    /* ALERTS */
    .settings-alert {
        padding: 13px 16px;
        border-radius: 14px;
        margin-bottom: 18px;
        font-size: 12.5px;
        font-weight: 600;
    }

    .settings-alert.success {
        color: #166534;
        background: rgba(220, 252, 231, .92);
        border: 1px solid rgba(74, 222, 128, .4);
    }

    .settings-alert.error {
        color: #991b1b;
        background: rgba(254, 226, 226, .92);
        border: 1px solid rgba(248, 113, 113, .4);
    }

    /* KONTAINER UTAMA PEMBUNGKUS PROFIL, PASSWORD, DAN NOTIFIKASI */
    .settings-container {
        width: 100%;
        box-sizing: border-box;
        background: var(--st-glass);
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: var(--st-border);
        border-radius: 20px;
        padding: 24px 26px;
        box-shadow: var(--st-shadow-soft);
    }

    /* HEADER JUDUL SEKSI DENGAN IKON KOTAK HIJAU */
    .settings-section-header {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--st-line);
    }

    .settings-title-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(47, 125, 88, .12);
        border: 1px solid rgba(255, 255, 255, .55);
        color: var(--st-green);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .settings-title-icon svg {
        width: 19px;
        height: 19px;
    }

    .settings-section-header h2 {
        margin: 0 0 2px;
        font-size: 17px;
        font-weight: 800;
        letter-spacing: -.02em;
        color: var(--st-ink);
    }

    .settings-section-header p {
        margin: 0;
        font-size: 12px;
        color: var(--st-subtle);
    }

    .settings-stack {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* INNER CARDS GLASS DI DALAM KONTAINER UTAMA */
    .settings-inner-card {
        position: relative;
        background: var(--st-card-glass);
        backdrop-filter: blur(16px) saturate(150%);
        -webkit-backdrop-filter: blur(16px) saturate(150%);
        border: 1px solid rgba(255, 255, 255, .68);
        border-radius: 16px;
        padding: 20px 22px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .035), inset 0 1px 0 rgba(255, 255, 255, .82);
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .settings-inner-card.is-card-editing {
        background: linear-gradient(145deg, rgba(255, 255, 255, .84) 0%, rgba(255, 255, 255, .58) 100%);
        border-color: rgba(47, 125, 88, .32);
        box-shadow: 0 6px 18px rgba(15, 23, 42, .05), inset 0 1px 0 rgba(255, 255, 255, .92);
    }

    .settings-inner-card.has-open-dropdown {
        z-index: 35;
    }

    .settings-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .settings-card-top h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: var(--st-ink);
    }

    .settings-card-top p {
        margin: 3px 0 0;
        font-size: 12px;
        color: var(--st-subtle);
    }

    /* TOP IDENTITY SUMMARY CARD */
    .settings-identity-card {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
    }

    .settings-identity-left {
        display: flex;
        align-items: center;
        gap: 18px;
        min-width: 0;
        flex: 1;
    }

    .settings-avatar-wrap {
        position: relative;
        width: 68px;
        height: 68px;
        flex-shrink: 0;
    }

    .settings-avatar {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(135deg, #24563d 0%, #2f7d58 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: .02em;
        overflow: hidden;
        border: 3px solid rgba(255, 255, 255, .92);
        box-shadow: 0 3px 10px rgba(15, 23, 42, .08);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .settings-avatar.has-photo {
        cursor: pointer;
    }

    .settings-avatar.has-photo:hover {
        transform: scale(1.04);
        box-shadow: 0 6px 16px rgba(15, 23, 42, .14);
    }

    .settings-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .settings-avatar img[hidden],
    .settings-avatar span[hidden] {
        display: none !important;
    }

    .settings-avatar-cam {
        position: absolute;
        right: -2px;
        bottom: -2px;
        width: 26px;
        height: 26px;
        padding: 0;
        border-radius: 50%;
        border: 2px solid #ffffff;
        background: var(--st-green-mid);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(15, 23, 42, .14);
        transition: background .18s ease, transform .18s ease;
    }

    .settings-avatar-cam[hidden] {
        display: none !important;
    }

    .settings-avatar-cam:hover {
        background: #276b4c;
        transform: scale(1.06);
    }

    .settings-avatar-cam svg {
        width: 13px;
        height: 13px;
    }

    .settings-identity-meta {
        min-width: 0;
    }

    .settings-identity-meta h3 {
        margin: 0 0 3px;
        font-size: 17px;
        font-weight: 800;
        color: var(--st-ink);
    }

    .settings-identity-meta .meta-email {
        font-size: 13px;
        color: var(--st-muted);
        margin: 0;
        word-break: break-all;
    }

    .settings-identity-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 9px;
    }

    .settings-role-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 999px;
        background: rgba(47, 125, 88, .12);
        border: 1px solid rgba(47, 125, 88, .22);
        color: var(--st-green);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .05em;
        line-height: 1.2;
    }

    .btn-view-photo-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 11px;
        border-radius: 999px;
        border: 1px solid #dce5e0;
        background: rgba(255, 255, 255, .92);
        color: var(--st-ink);
        font-family: inherit;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: background .18s ease, border-color .18s ease, color .18s ease;
    }

    .btn-view-photo-pill[hidden] {
        display: none !important;
    }

    .btn-view-photo-pill:hover {
        background: #eaf4ef;
        border-color: var(--st-green-mid);
        color: var(--st-green-mid);
    }

    .btn-view-photo-pill svg {
        width: 12px;
        height: 12px;
    }

    /* AKSI FOTO PROFIL (MUNCUL SAAT KARTU IDENTITAS DI-EDIT) */
    .settings-photo-editor {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed rgba(22, 54, 38, .12);
    }

    .settings-photo-editor[hidden] {
        display: none !important;
    }

    .settings-photo-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-photo-mini {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 11px;
        border-radius: 8px;
        border: 1px solid #dce5e0;
        background: rgba(255, 255, 255, .92);
        color: var(--st-ink);
        font-family: inherit;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: background .18s ease, border-color .18s ease, color .18s ease;
    }

    .btn-photo-mini[hidden] {
        display: none !important;
    }

    .btn-photo-mini:hover {
        background: #eaf4ef;
        border-color: var(--st-green-mid);
        color: var(--st-green-mid);
    }

    .btn-photo-mini.danger {
        color: #b91c1c;
        border-color: rgba(220, 38, 38, .22);
        background: #fef2f2;
    }

    .btn-photo-mini.danger:hover {
        background: #fee2e2;
        border-color: rgba(220, 38, 38, .38);
    }

    .btn-photo-mini svg {
        width: 13px;
        height: 13px;
    }

    .settings-photo-hint {
        margin-top: 6px;
        font-size: 11.5px;
        color: var(--st-subtle);
    }

    /* KUNCI SCROLL HALAMAN BELAKANG SAAT MODAL TERBUKA */
    html.modal-scroll-locked,
    body.modal-scroll-locked,
    body.modal-scroll-locked .panel-shell,
    body.modal-scroll-locked .panel-main,
    body.modal-scroll-locked .panel-content,
    body.modal-scroll-locked main {
        overflow: hidden !important;
        overscroll-behavior: none !important;
    }

    /* MODAL PREVIEW & ATUR FOTO PROFIL (BACKDROP DIGELAPKAN TANPA BLUR & SMOOTH CLOSE) */
    .settings-photo-modal {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(15, 23, 42, .46);
        overscroll-behavior: contain;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .26s cubic-bezier(.16, 1, .3, 1), visibility .26s cubic-bezier(.16, 1, .3, 1);
    }

    .settings-photo-modal.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .settings-photo-dialog {
        position: relative;
        margin: auto;
        width: 100%;
        max-width: 420px;
        border-radius: 20px;
        padding: 22px;
        background: #ffffff;
        border: 1px solid #e6ece8;
        box-shadow: 0 24px 54px rgba(15, 23, 42, .22);
        color: #172b20;
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        box-sizing: border-box;
        opacity: 0;
        transform: translateY(10px) scale(.96);
        transition: transform .26s cubic-bezier(.16, 1, .3, 1), opacity .24s cubic-bezier(.16, 1, .3, 1);
    }

    .settings-photo-modal.is-open .settings-photo-dialog {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .settings-photo-dialog-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .settings-photo-dialog-head strong {
        display: block;
        font-size: 15px;
        font-weight: 800;
    }

    .settings-photo-dialog-head small {
        display: block;
        font-size: 11.5px;
        color: #68736c;
    }

    .settings-photo-close {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #dce5e0;
        background: #ffffff;
        color: #172b20;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .18s ease, color .18s ease, border-color .18s ease, transform .18s ease;
    }

    .settings-photo-close:hover {
        background: #fee2e2;
        border-color: rgba(220, 38, 38, .28);
        color: #dc2626;
    }

    .settings-photo-frame {
        width: 100%;
        aspect-ratio: 1 / 1;
        max-height: 360px;
        border-radius: 14px;
        overflow: hidden;
        background: #edf4ef;
        border: 1px solid #dce5e0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .settings-photo-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* AREA CROP & ZOOM IN/OUT FOTO */
    .photo-crop-stage {
        position: relative;
        width: 280px;
        height: 280px;
        max-width: 100%;
        margin: 0 auto;
        border-radius: 16px;
        overflow: hidden;
        background: #102118;
        border: 1px solid #dce5e0;
        cursor: grab;
        touch-action: none;
        user-select: none;
        -webkit-user-select: none;
    }

    .photo-crop-stage.is-dragging {
        cursor: grabbing;
    }

    .photo-crop-stage canvas {
        width: 100%;
        height: 100%;
        display: block;
    }

    .photo-crop-ring {
        position: absolute;
        inset: 10px;
        border-radius: 50%;
        border: 2px dashed rgba(255, 255, 255, .85);
        box-shadow: 0 0 0 999px rgba(12, 28, 19, .42);
        pointer-events: none;
    }

    .photo-zoom-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 14px;
        padding: 10px 12px;
        border-radius: 12px;
        background: #f4f8f5;
        border: 1px solid #dce5e0;
    }

    .photo-zoom-btn {
        width: 30px;
        height: 30px;
        padding: 0;
        border-radius: 8px;
        border: 1px solid #dce5e0;
        background: #ffffff;
        color: #172b20;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: background .16s ease, border-color .16s ease, color .16s ease;
    }

    .photo-zoom-btn:hover {
        background: #eaf4ef;
        border-color: #2f7d58;
        color: #2f7d58;
    }

    .photo-zoom-btn svg {
        width: 14px;
        height: 14px;
    }

    .photo-zoom-range {
        flex: 1;
        height: 6px;
        border-radius: 999px;
        background: #d5e2db;
        outline: none;
        accent-color: #2f7d58;
        cursor: pointer;
    }

    .photo-zoom-pct {
        min-width: 42px;
        text-align: right;
        font-size: 11.5px;
        font-weight: 700;
        color: #2f7d58;
    }

    .photo-crop-tip {
        margin-top: 8px;
        text-align: center;
        font-size: 11.5px;
        color: #68736c;
    }

    .settings-photo-dialog-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 16px;
    }

    .settings-photo-dialog-foot.end-only {
        justify-content: flex-end;
    }

    /* EDIT BUTTON */
    .btn-card-edit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 8px;
        border: 1px solid #dce5e0;
        background: rgba(255, 255, 255, .92);
        color: var(--st-ink);
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        flex-shrink: 0;
        transition: background .18s ease, border-color .18s ease, color .18s ease;
    }

    .btn-card-edit:hover {
        background: #eaf4ef;
        border-color: var(--st-green-mid);
        color: var(--st-green-mid);
    }

    .btn-card-edit.is-editing {
        background: var(--st-green-soft);
        border-color: rgba(47, 125, 88, .35);
        color: var(--st-green-mid);
    }

    .btn-card-edit svg {
        width: 13px;
        height: 13px;
    }

    /* 2-COLUMN FIELD GRID */
    .settings-fields-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px 20px;
    }

    .settings-field.full-span {
        grid-column: 1 / -1;
    }

    /* LABEL DISAMAKAN DENGAN HALAMAN LOGIN */
    .settings-field label {
        display: block;
        margin: 0 0 8px;
        color: #20372a;
        font-size: 12px;
        font-weight: 700;
    }

    .settings-input-wrap {
        position: relative;
    }

    /* INPUT FIELD DISAMAKAN DENGAN HALAMAN LOGIN */
    .settings-field input {
        width: 100%;
        height: 44px;
        box-sizing: border-box;
        padding: 0 13px;
        border-radius: 8px;
        border: 1px solid #dce5e0;
        background: #ffffff;
        color: #172b20;
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        font-size: 13px;
        font-weight: 400;
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease;
    }

    .settings-field input::placeholder {
        color: #8c9991;
    }

    /* SAAT BELUM KLIK EDIT (MODE VIEW): DEFAULT ABU-ABU MUDA, TANPA GARIS CARET (|), TETAP BISA DI-BLOCK & COPY */
    .settings-field input[readonly],
    .settings-field input:disabled {
        background: var(--st-locked-bg) !important;
        border-color: var(--st-locked-border) !important;
        color: var(--st-locked-ink);
        cursor: default !important;
        caret-color: transparent !important;
        user-select: text !important;
        -webkit-user-select: text !important;
        box-shadow: none !important;
    }

    .settings-field input[readonly]::selection,
    .settings-field input:disabled::selection {
        background: rgba(47, 125, 88, .18);
        color: var(--st-ink);
    }

    .settings-field input[readonly]::placeholder,
    .settings-field input:disabled::placeholder {
        color: transparent;
    }

    .settings-field input[readonly]:focus,
    .settings-field input:disabled:focus {
        outline: none !important;
        border-color: var(--st-locked-border) !important;
        box-shadow: none !important;
        caret-color: transparent !important;
    }

    /* SAAT KLIK EDIT: FIELD BERWARNA PUTIH AKTIF */
    .settings-field input:not([readonly]):not(:disabled) {
        background: #ffffff;
        border-color: #dce5e0;
        color: #172b20;
        cursor: text;
        caret-color: #172b20;
    }

    .settings-field input:not([readonly]):not(:disabled):focus {
        background: #ffffff;
        border-color: #2f7d58;
        box-shadow: 0 0 0 3px rgba(47, 125, 88, .14);
    }

    .settings-input-wrap input {
        padding-right: 48px;
    }

    /* TOMBOL IKON MATA DISAMAKAN PERSIS DENGAN HALAMAN LOGIN (.password-toggle) */
    .settings-eye-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        padding: 0;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #68736c;
        cursor: pointer;
        transition: background .18s ease, color .18s ease;
    }

    .settings-eye-btn:hover {
        background: #eaf4ef;
        color: #2f7d58;
    }

    .settings-eye-btn svg {
        width: 17px;
        height: 17px;
    }

    /* CUSTOM GLASSMORPHISM DROPDOWN */
    .glass-select {
        position: relative;
        width: 100%;
    }

    .glass-select-trigger {
        width: 100%;
        height: 44px;
        box-sizing: border-box;
        padding: 0 38px 0 13px;
        border-radius: 8px;
        border: 1px solid #dce5e0;
        background: #ffffff;
        color: #172b20;
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        font-size: 13px;
        font-weight: 400;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        cursor: pointer;
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease;
    }

    .glass-select-trigger.is-placeholder {
        color: #8c9991;
    }

    /* SAAT BELUM KLIK EDIT: DEFAULT ABU-ABU MUDA, TANPA GARIS CARET, TETAP BISA DI-BLOCK & COPY */
    .glass-select.is-locked,
    .glass-select.is-locked *,
    .glass-select.is-locked .glass-select-trigger,
    .glass-select-trigger:disabled {
        cursor: default !important;
        caret-color: transparent !important;
    }

    .glass-select.is-locked .glass-select-trigger,
    .glass-select-trigger:disabled {
        background: var(--st-locked-bg) !important;
        border-color: var(--st-locked-border) !important;
        color: var(--st-locked-ink);
        pointer-events: none;
        user-select: text !important;
        -webkit-user-select: text !important;
        box-shadow: none !important;
    }

    .glass-select.is-locked {
        user-select: text !important;
        -webkit-user-select: text !important;
    }

    .glass-select:not(.is-locked) .glass-select-trigger:hover {
        border-color: #2f7d58;
        background: #ffffff;
    }

    .glass-select.is-open .glass-select-trigger,
    .glass-select:not(.is-locked) .glass-select-trigger:focus-visible {
        background: #ffffff;
        border-color: #2f7d58;
        box-shadow: 0 0 0 3px rgba(47, 125, 88, .14);
    }

    .glass-select-value {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        user-select: text !important;
        -webkit-user-select: text !important;
    }

    .glass-select-value::selection {
        background: rgba(47, 125, 88, .18);
        color: var(--st-ink);
    }

    .glass-select-chevron {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: #68736c;
        pointer-events: none;
        transition: transform .2s ease, color .2s ease;
    }

    .glass-select.is-open .glass-select-chevron {
        transform: translateY(-50%) rotate(180deg);
        color: #2f7d58;
    }

    /* MENU DROPDOWN TETAP GLASSMORPHISM, DENGAN SLIDER SCROLLBAR PALET HIJAU UTAMA (#2f7d58) */
    .glass-select-menu {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        z-index: 80;
        max-height: 236px;
        overflow-y: auto;
        padding: 6px;
        border-radius: 12px;
        background: linear-gradient(155deg, rgba(255, 255, 255, .90) 0%, rgba(244, 249, 246, .84) 100%);
        backdrop-filter: blur(22px) saturate(160%);
        -webkit-backdrop-filter: blur(22px) saturate(160%);
        border: 1px solid rgba(255, 255, 255, .85);
        box-shadow: 0 12px 28px rgba(15, 23, 42, .10), inset 0 1px 0 rgba(255, 255, 255, .9);
        box-sizing: border-box;
        scrollbar-width: thin;
        scrollbar-color: #2f7d58 rgba(47, 125, 88, .10);
    }

    .glass-select-menu[hidden] {
        display: none !important;
    }

    .glass-select-menu::-webkit-scrollbar {
        width: 7px;
    }

    .glass-select-menu::-webkit-scrollbar-track {
        background: rgba(47, 125, 88, .10);
        border-radius: 999px;
    }

    .glass-select-menu::-webkit-scrollbar-thumb {
        background: #2f7d58;
        border-radius: 999px;
    }

    .glass-select-menu::-webkit-scrollbar-thumb:hover {
        background: #1f7a49;
    }

    .glass-select-option {
        width: 100%;
        box-sizing: border-box;
        padding: 9px 11px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: var(--st-ink);
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 500;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        cursor: pointer;
        transition: background .15s ease, color .15s ease;
    }

    .glass-select-option:hover,
    .glass-select-option:focus-visible {
        background: #eaf4ef;
        color: #2f7d58;
        outline: none;
    }

    .glass-select-option.is-selected {
        background: rgba(47, 125, 88, .14);
        color: #2f7d58;
        font-weight: 700;
    }

    .glass-select-option svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        color: #2f7d58;
    }

    .glass-select-empty {
        padding: 10px 12px;
        font-size: 12px;
        color: var(--st-subtle);
        text-align: center;
    }

    .settings-error {
        margin-top: 5px;
        font-size: 11.5px;
        font-weight: 600;
        color: #b32e2e;
    }

    .settings-error[hidden] {
        display: none !important;
    }

    /* ACTION BAR PER KARTU */
    .settings-form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid var(--st-line);
    }

    .settings-form-actions.is-hidden {
        display: none;
    }

    .btn-settings-cancel {
        height: 40px;
        padding: 0 16px;
        border-radius: 8px;
        border: 1px solid #dce5e0;
        background: rgba(255, 255, 255, .92);
        color: var(--st-muted);
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: background .18s ease, color .18s ease;
    }

    .btn-settings-cancel:hover {
        background: #f4f8f5;
        color: var(--st-ink);
    }

    .btn-settings-save {
        height: 40px;
        padding: 0 18px;
        border: 1px solid #276b4c;
        border-radius: 8px;
        background: #2f7d58;
        color: #fff;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(15, 23, 42, .08);
        transition: background-color .18s ease;
    }

    .btn-settings-save:hover {
        background: #276b4c;
    }

    /* NOTIFICATION SWITCHES (PALET HIJAU UTAMA #2f7d58) */
    .notification-setting-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 15px 0;
        border-bottom: 1px solid var(--st-line);
    }

    .notification-setting-row:first-of-type {
        padding-top: 4px;
    }

    .notification-setting-row:last-of-type {
        border-bottom: 0;
    }

    .notification-setting-copy {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .notification-setting-copy strong {
        font-size: 13px;
        font-weight: 700;
        color: var(--st-ink);
    }

    .notification-setting-copy span {
        font-size: 12px;
        color: var(--st-subtle);
    }

    .setting-switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 26px;
        flex: 0 0 46px;
        cursor: pointer;
    }

    .setting-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .setting-slider {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: #cbd5cf;
        transition: background .2s ease;
    }

    .setting-slider::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        left: 3px;
        top: 3px;
        border-radius: 50%;
        background: #ffffff;
        transition: transform .2s ease;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .16);
    }

    .setting-switch input:checked + .setting-slider {
        background: #2f7d58;
    }

    .setting-switch input:checked + .setting-slider::before {
        transform: translateX(20px);
    }

    .settings-pane[hidden] {
        display: none !important;
    }

    /* RESPONSIVE */
    @media (max-width: 640px) {
        .settings-container {
            padding: 18px 16px;
        }

        .settings-fields-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .settings-identity-card {
            flex-direction: column;
            align-items: stretch;
        }

        .settings-inner-card {
            padding: 16px;
        }
    }
</style>

<div class="settings-shell">

    <div class="settings-header">
        <h1>Pengaturan</h1>
        <p>Kelola informasi akun, keamanan, dan preferensi notifikasi SIPADU.</p>
    </div>

    @if(session('profile_incomplete'))
        <div class="settings-alert error">
            {{ session('profile_incomplete') }} Lengkapi data identitas yang masih kosong, lalu simpan perubahan.
        </div>
    @endif

    @if(session('success'))
        <div class="settings-alert success">{{ session('success') }}</div>
    @endif

    @if(session('password_success'))
        <div class="settings-alert success">{{ session('password_success') }}</div>
    @endif

    @if(session('preferences_success'))
        <div class="settings-alert success">{{ session('preferences_success') }}</div>
    @endif

    @if($errors->any())
        <div class="settings-alert error">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul style="margin:6px 0 0 18px;padding:0;font-weight:500;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- KONTAINER UTAMA PEMBUNGKUS PROFIL, PASSWORD, DAN NOTIFIKASI --}}
    <div class="settings-container">

        {{-- TAB 1: PROFIL SAYA --}}
        <section class="settings-pane" data-settings-pane="profil" {{ $activeTab !== 'profil' ? 'hidden' : '' }}>
            <div class="settings-section-header">
                <div class="settings-title-icon">
                    <i data-lucide="user"></i>
                </div>
                <div>
                    <h2>Profil Saya</h2>
                    <p>Kelola informasi pribadi dan identitas akun Anda.</p>
                </div>
            </div>

            <form method="POST"
                  action="{{ $profileUpdateRoute }}"
                  id="profileSettingsForm"
                  class="settings-stack"
                  enctype="multipart/form-data"
                  data-study-programs="{{ json_encode($studyProgramsByFaculty, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}">
                @csrf
                @method('PUT')

                <input type="file" id="profilePhotoInput" name="profile_photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" hidden>
                <input type="hidden" id="removeProfilePhotoInput" name="remove_profile_photo" value="0">

                {{-- KARTU 1: RINGKASAN AKUN & FOTO PROFIL --}}
                <div class="settings-inner-card {{ $initIdentityEdit ? 'is-card-editing' : '' }}" data-profile-card="identity">
                    <div class="settings-identity-card">
                        <div class="settings-identity-left">
                            <div class="settings-avatar-wrap">
                                <div class="settings-avatar {{ $photoUrl ? 'has-photo' : '' }}"
                                     id="settingsAvatarBox"
                                     data-user-name="{{ $userName }}"
                                     data-user-initial="{{ $initial }}"
                                     data-saved-photo="{{ $photoUrl ?? '' }}"
                                     title="{{ $photoUrl ? 'Klik untuk preview foto profil' : $userName }}">
                                    <img id="settingsAvatarImg" src="{{ $photoUrl ?? '' }}" alt="Foto profil {{ $userName }}" {{ $photoUrl ? '' : 'hidden' }}>
                                    <span id="settingsAvatarInitial" {{ $photoUrl ? 'hidden' : '' }}>{{ $initial }}</span>
                                </div>
                                <button type="button"
                                        class="settings-avatar-cam"
                                        id="avatarCameraBtn"
                                        data-pick-photo
                                        title="Ubah foto profil"
                                        aria-label="Ubah foto profil"
                                        {{ $initIdentityEdit ? '' : 'hidden' }}>
                                    <i data-lucide="camera"></i>
                                </button>
                            </div>

                            <div class="settings-identity-meta">
                                <h3>{{ $userName }}</h3>
                                <p class="meta-email">{{ $userEmail }}</p>

                                <div class="settings-identity-badges">
                                    <span class="settings-role-badge">
                                        {{ $roleBadgeLabel }}
                                    </span>
                                    <button type="button"
                                            class="btn-view-photo-pill"
                                            id="quickViewPhotoBtn"
                                            data-open-photo-modal
                                            {{ $photoUrl ? '' : 'hidden' }}>
                                        <i data-lucide="eye"></i>
                                        <span>Lihat Foto</span>
                                    </button>
                                </div>

                                {{-- KONTROL EDIT FOTO HANYA MUNCUL SAAT KARTU INI DI-EDIT --}}
                                <div class="settings-photo-editor" id="profilePhotoEditor" {{ $initIdentityEdit ? '' : 'hidden' }}>
                                    <div class="settings-photo-actions">
                                        <button type="button" class="btn-photo-mini" id="viewProfilePhotoBtn" data-open-photo-modal {{ $photoUrl ? '' : 'hidden' }}>
                                            <i data-lucide="eye"></i>
                                            <span>Lihat Foto</span>
                                        </button>
                                        <button type="button" class="btn-photo-mini" data-pick-photo>
                                            <i data-lucide="image-plus"></i>
                                            <span id="pickPhotoBtnLabel">{{ $photoUrl ? 'Ubah Foto' : 'Unggah Foto' }}</span>
                                        </button>
                                        <button type="button" class="btn-photo-mini" id="adjustProfilePhotoBtn" hidden>
                                            <i data-lucide="zoom-in"></i>
                                            <span>Atur Ukuran</span>
                                        </button>
                                        <button type="button" class="btn-photo-mini danger" id="removeProfilePhotoBtn" {{ $photoUrl ? '' : 'hidden' }}>
                                            <i data-lucide="trash-2"></i>
                                            <span>Hapus Foto</span>
                                        </button>
                                    </div>
                                    <div id="profilePhotoHint" class="settings-photo-hint">Format JPG, JPEG, PNG, atau WEBP (maks. 2 MB).</div>
                                    <div id="profilePhotoClientError" class="settings-error" hidden></div>
                                </div>

                                @error('profile_photo')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="button" class="btn-card-edit {{ $initIdentityEdit ? 'is-editing' : '' }}" data-card-edit-btn>
                            <span>Edit</span>
                            <i data-lucide="pencil"></i>
                        </button>
                    </div>

                    <div class="settings-form-actions {{ $initIdentityEdit ? '' : 'is-hidden' }}" data-card-actions>
                        <button type="button" class="btn-settings-cancel" data-card-cancel-btn>Batal</button>
                        <button type="submit" class="btn-settings-save">Simpan Perubahan</button>
                    </div>
                </div>

                {{-- KARTU 2: INFORMASI PRIBADI --}}
                <div class="settings-inner-card {{ $initPersonalEdit ? 'is-card-editing' : '' }}" data-profile-card="personal">
                    <div class="settings-card-top">
                        <div>
                            <h3>Informasi Pribadi</h3>
                        </div>
                        <button type="button" class="btn-card-edit {{ $initPersonalEdit ? 'is-editing' : '' }}" data-card-edit-btn>
                            <span>Edit</span>
                            <i data-lucide="pencil"></i>
                        </button>
                    </div>

                    <div class="settings-fields-grid">
                        <div class="settings-field">
                            <label for="profileName">Nama Lengkap</label>
                            <input id="profileName"
                                   type="text"
                                   name="name"
                                   value="{{ old('name', $userName) }}"
                                   data-edit-placeholder="Masukkan nama lengkap"
                                   placeholder="{{ $initPersonalEdit ? 'Masukkan nama lengkap' : '' }}"
                                   {{ $initPersonalEdit ? '' : 'readonly tabindex=-1' }}
                                   required>
                            @error('name')
                                <div class="settings-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="settings-field">
                            <label for="profileEmail">Alamat Email</label>
                            <input id="profileEmail"
                                   type="email"
                                   name="email"
                                   value="{{ old('email', $userEmail) }}"
                                   data-edit-placeholder="Masukkan email"
                                   placeholder="{{ $initPersonalEdit ? 'Masukkan email' : '' }}"
                                   {{ $initPersonalEdit ? '' : 'readonly tabindex=-1' }}
                                   required>
                            @error('email')
                                <div class="settings-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="settings-field {{ $isAdmin ? '' : 'full-span' }}">
                            <label for="profilePhone">Nomor Telepon</label>
                            <input id="profilePhone"
                                   type="tel"
                                   name="phone"
                                   value="{{ old('phone', data_get($user, 'phone')) }}"
                                   data-edit-placeholder="Masukkan nomor telepon (contoh: 081234567890)"
                                   placeholder="{{ $initPersonalEdit ? 'Masukkan nomor telepon (contoh: 081234567890)' : '' }}"
                                   {{ $initPersonalEdit ? '' : 'readonly tabindex=-1' }}
                                   {{ $isAdmin ? '' : 'required' }}>
                            @error('phone')
                                <div class="settings-error">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($isAdmin)
                            <div class="settings-field">
                                <label>Peran Akun</label>
                                <input type="text" value="Administrator" readonly tabindex="-1">
                            </div>
                        @endif
                    </div>

                    <div class="settings-form-actions {{ $initPersonalEdit ? '' : 'is-hidden' }}" data-card-actions>
                        <button type="button" class="btn-settings-cancel" data-card-cancel-btn>Batal</button>
                        <button type="submit" class="btn-settings-save">Simpan Perubahan</button>
                    </div>
                </div>

                {{-- KARTU 3: INFORMASI AKADEMIK / KEPEGAWAIAN / ADMINISTRATOR --}}
                <div class="settings-inner-card {{ $initOrgEdit ? 'is-card-editing' : '' }}" data-profile-card="organization">
                    <div class="settings-card-top">
                        <div>
                            <h3>{{ $isAdmin ? 'Informasi Administrator' : ($isEmployee ? 'Informasi Kepegawaian' : 'Informasi Akademik') }}</h3>
                        </div>
                        <button type="button" class="btn-card-edit {{ $initOrgEdit ? 'is-editing' : '' }}" data-card-edit-btn>
                            <span>Edit</span>
                            <i data-lucide="pencil"></i>
                        </button>
                    </div>

                    <div class="settings-fields-grid">
                        @if($isAdmin)
                            <div class="settings-field">
                                <label for="profileAdminNip">NIP</label>
                                <input id="profileAdminNip"
                                       type="text"
                                       name="nim_nip"
                                       value="{{ old('nim_nip', data_get($user, 'nim_nip')) }}"
                                       data-edit-placeholder="Masukkan NIP"
                                       placeholder="{{ $initOrgEdit ? 'Masukkan NIP' : '' }}"
                                       {{ $initOrgEdit ? '' : 'readonly tabindex=-1' }}>
                                @error('nim_nip')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="settings-field">
                                <label>Instansi</label>
                                <input type="text" value="UPN Veteran Jakarta" readonly tabindex="-1">
                            </div>
                        @elseif($isEmployee)
                            <div class="settings-field">
                                <label for="profileNimNip">NIP</label>
                                <input id="profileNimNip"
                                       type="text"
                                       name="nim_nip"
                                       value="{{ old('nim_nip', data_get($user, 'nim_nip')) }}"
                                       data-edit-placeholder="Masukkan NIP"
                                       placeholder="{{ $initOrgEdit ? 'Masukkan NIP' : '' }}"
                                       {{ $initOrgEdit ? '' : 'readonly tabindex=-1' }}
                                       required>
                                @error('nim_nip')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- DROPDOWN JABATAN --}}
                            <div class="settings-field">
                                <label id="labelProfilePosition">Jabatan</label>
                                <div class="glass-select {{ $initOrgEdit ? '' : 'is-locked' }}" data-glass-select="position">
                                    <input type="hidden" name="position" value="{{ $selectedPosition }}" data-glass-input>
                                    <button type="button"
                                            class="glass-select-trigger {{ $selectedPosition === '' ? 'is-placeholder' : '' }}"
                                            data-glass-trigger
                                            data-placeholder="Pilih jabatan"
                                            aria-haspopup="listbox"
                                            aria-expanded="false"
                                            aria-labelledby="labelProfilePosition"
                                            {{ $initOrgEdit ? '' : 'tabindex=-1' }}>
                                        <span class="glass-select-value" data-glass-label>{{ $selectedPosition !== '' ? $selectedPosition : ($initOrgEdit ? 'Pilih jabatan' : '') }}</span>
                                        <i data-lucide="chevron-down" class="glass-select-chevron"></i>
                                    </button>
                                    <div class="glass-select-menu" role="listbox" data-glass-menu hidden>
                                        @foreach($positions as $posItem)
                                            <button type="button"
                                                    class="glass-select-option {{ $selectedPosition === $posItem ? 'is-selected' : '' }}"
                                                    role="option"
                                                    aria-selected="{{ $selectedPosition === $posItem ? 'true' : 'false' }}"
                                                    data-value="{{ $posItem }}">
                                                <span>{{ $posItem }}</span>
                                                @if($selectedPosition === $posItem)
                                                    <i data-lucide="check"></i>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                @error('position')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- DROPDOWN UNIT / BIRO KERJA --}}
                            <div class="settings-field full-span">
                                <label id="labelProfileEmployeeUnit">Unit / Biro Kerja</label>
                                <div class="glass-select {{ $initOrgEdit ? '' : 'is-locked' }}" data-glass-select="employee_unit">
                                    <input type="hidden" name="employee_unit" value="{{ $selectedEmployeeUnit }}" data-glass-input>
                                    <button type="button"
                                            class="glass-select-trigger {{ $selectedEmployeeUnit === '' ? 'is-placeholder' : '' }}"
                                            data-glass-trigger
                                            data-placeholder="Pilih unit atau biro kerja"
                                            aria-haspopup="listbox"
                                            aria-expanded="false"
                                            aria-labelledby="labelProfileEmployeeUnit"
                                            {{ $initOrgEdit ? '' : 'tabindex=-1' }}>
                                        <span class="glass-select-value" data-glass-label>{{ $selectedEmployeeUnit !== '' ? $selectedEmployeeUnit : ($initOrgEdit ? 'Pilih unit atau biro kerja' : '') }}</span>
                                        <i data-lucide="chevron-down" class="glass-select-chevron"></i>
                                    </button>
                                    <div class="glass-select-menu" role="listbox" data-glass-menu hidden>
                                        @foreach($employeeUnits as $unitItem)
                                            <button type="button"
                                                    class="glass-select-option {{ $selectedEmployeeUnit === $unitItem ? 'is-selected' : '' }}"
                                                    role="option"
                                                    aria-selected="{{ $selectedEmployeeUnit === $unitItem ? 'true' : 'false' }}"
                                                    data-value="{{ $unitItem }}">
                                                <span>{{ $unitItem }}</span>
                                                @if($selectedEmployeeUnit === $unitItem)
                                                    <i data-lucide="check"></i>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                @error('employee_unit')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="settings-field">
                                <label for="profileNimNip">NIM</label>
                                <input id="profileNimNip"
                                       type="text"
                                       name="nim_nip"
                                       value="{{ old('nim_nip', data_get($user, 'nim_nip')) }}"
                                       data-edit-placeholder="Masukkan NIM"
                                       placeholder="{{ $initOrgEdit ? 'Masukkan NIM' : '' }}"
                                       {{ $initOrgEdit ? '' : 'readonly tabindex=-1' }}
                                       required>
                                @error('nim_nip')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- DROPDOWN FAKULTAS --}}
                            <div class="settings-field">
                                <label id="labelProfileFaculty">Fakultas</label>
                                <div class="glass-select {{ $initOrgEdit ? '' : 'is-locked' }}" data-glass-select="faculty">
                                    <input type="hidden" name="faculty" value="{{ $selectedFaculty }}" data-glass-input>
                                    <button type="button"
                                            class="glass-select-trigger {{ $selectedFaculty === '' ? 'is-placeholder' : '' }}"
                                            data-glass-trigger
                                            data-placeholder="Pilih fakultas"
                                            aria-haspopup="listbox"
                                            aria-expanded="false"
                                            aria-labelledby="labelProfileFaculty"
                                            {{ $initOrgEdit ? '' : 'tabindex=-1' }}>
                                        <span class="glass-select-value" data-glass-label>{{ $selectedFaculty !== '' ? $selectedFaculty : ($initOrgEdit ? 'Pilih fakultas' : '') }}</span>
                                        <i data-lucide="chevron-down" class="glass-select-chevron"></i>
                                    </button>
                                    <div class="glass-select-menu" role="listbox" data-glass-menu hidden>
                                        @foreach($faculties as $facultyItem)
                                            <button type="button"
                                                    class="glass-select-option {{ $selectedFaculty === $facultyItem ? 'is-selected' : '' }}"
                                                    role="option"
                                                    aria-selected="{{ $selectedFaculty === $facultyItem ? 'true' : 'false' }}"
                                                    data-value="{{ $facultyItem }}">
                                                <span>{{ $facultyItem }}</span>
                                                @if($selectedFaculty === $facultyItem)
                                                    <i data-lucide="check"></i>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                @error('faculty')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- DROPDOWN PROGRAM STUDI (TERKAIT FAKULTAS) --}}
                            <div class="settings-field full-span">
                                <label id="labelProfileStudyProgram">Program Studi</label>
                                <div class="glass-select {{ $initOrgEdit ? '' : 'is-locked' }}" data-glass-select="study_program">
                                    <input type="hidden" name="study_program" value="{{ $selectedStudyProgram }}" data-glass-input>
                                    <button type="button"
                                            class="glass-select-trigger {{ $selectedStudyProgram === '' ? 'is-placeholder' : '' }}"
                                            data-glass-trigger
                                            data-placeholder="Pilih program studi"
                                            aria-haspopup="listbox"
                                            aria-expanded="false"
                                            aria-labelledby="labelProfileStudyProgram"
                                            {{ $initOrgEdit ? '' : 'tabindex=-1' }}>
                                        <span class="glass-select-value" data-glass-label>{{ $selectedStudyProgram !== '' ? $selectedStudyProgram : ($initOrgEdit ? 'Pilih program studi' : '') }}</span>
                                        <i data-lucide="chevron-down" class="glass-select-chevron"></i>
                                    </button>
                                    <div class="glass-select-menu" role="listbox" data-glass-menu hidden>
                                        @forelse($availableStudyPrograms as $programItem)
                                            <button type="button"
                                                    class="glass-select-option {{ $selectedStudyProgram === $programItem ? 'is-selected' : '' }}"
                                                    role="option"
                                                    aria-selected="{{ $selectedStudyProgram === $programItem ? 'true' : 'false' }}"
                                                    data-value="{{ $programItem }}">
                                                <span>{{ $programItem }}</span>
                                                @if($selectedStudyProgram === $programItem)
                                                    <i data-lucide="check"></i>
                                                @endif
                                            </button>
                                        @empty
                                            <div class="glass-select-empty">Pilih fakultas terlebih dahulu</div>
                                        @endforelse
                                    </div>
                                </div>
                                @error('study_program')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="settings-form-actions {{ $initOrgEdit ? '' : 'is-hidden' }}" data-card-actions>
                        <button type="button" class="btn-settings-cancel" data-card-cancel-btn>Batal</button>
                        <button type="submit" class="btn-settings-save">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </section>

        {{-- TAB 2: KEAMANAN AKUN (PASSWORD) --}}
        <section class="settings-pane" data-settings-pane="password" {{ $activeTab !== 'password' ? 'hidden' : '' }}>
            <div class="settings-section-header">
                <div class="settings-title-icon">
                    <i data-lucide="lock"></i>
                </div>
                <div>
                    <h2>Keamanan Akun</h2>
                    <p>Ubah password untuk menjaga keamanan akun.</p>
                </div>
            </div>

            <div class="settings-inner-card">
                <div class="settings-card-top">
                    <div>
                        <h3>Ubah Password</h3>
                        <p>Perbarui password secara berkala untuk menjaga keamanan akun.</p>
                    </div>
                </div>

                <form method="POST" action="{{ $profilePasswordRoute }}">
                    @csrf
                    @method('PUT')

                    <div class="settings-fields-grid">
                        <div class="settings-field full-span">
                            <label for="currentPasswordInput">Password Lama</label>
                            <div class="settings-input-wrap">
                                <input id="currentPasswordInput"
                                       type="password"
                                       name="current_password"
                                       placeholder="Masukkan password lama"
                                       required
                                       autocomplete="current-password">
                                <button type="button" class="settings-eye-btn" data-toggle-password="currentPasswordInput" aria-label="Tampilkan password" aria-pressed="false">
                                    <i data-lucide="eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="settings-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="settings-field">
                            <label for="newPasswordInput">Password Baru</label>
                            <div class="settings-input-wrap">
                                <input id="newPasswordInput"
                                       type="password"
                                       name="password"
                                       placeholder="Minimal 8 karakter"
                                       minlength="8"
                                       required
                                       autocomplete="new-password">
                                <button type="button" class="settings-eye-btn" data-toggle-password="newPasswordInput" aria-label="Tampilkan password" aria-pressed="false">
                                    <i data-lucide="eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="settings-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="settings-field">
                            <label for="confirmPasswordInput">Konfirmasi Password Baru</label>
                            <div class="settings-input-wrap">
                                <input id="confirmPasswordInput"
                                       type="password"
                                       name="password_confirmation"
                                       placeholder="Ulangi password baru"
                                       minlength="8"
                                       required
                                       autocomplete="new-password">
                                <button type="button" class="settings-eye-btn" data-toggle-password="confirmPasswordInput" aria-label="Tampilkan password" aria-pressed="false">
                                    <i data-lucide="eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="settings-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="settings-form-actions">
                        <button type="submit" class="btn-settings-save">Ubah Password</button>
                    </div>
                </form>
            </div>
        </section>

        {{-- TAB 3: PREFERENSI NOTIFIKASI --}}
        <section class="settings-pane" data-settings-pane="notifikasi" {{ $activeTab !== 'notifikasi' ? 'hidden' : '' }}>
            <div class="settings-section-header">
                <div class="settings-title-icon">
                    <i data-lucide="bell"></i>
                </div>
                <div>
                    <h2>Preferensi Notifikasi</h2>
                    <p>Atur jenis notifikasi yang ingin Anda terima.</p>
                </div>
            </div>

            <div class="settings-inner-card">
                <div class="settings-card-top">
                    <div>
                        <h3>Pengaturan Notifikasi</h3>
                        <p>Atur saluran pemberitahuan yang ingin Anda terima dari SIPADU.</p>
                    </div>
                </div>

                <form method="POST" action="{{ $profilePreferencesRoute }}">
                    @csrf
                    @method('PUT')

                    <div class="notification-setting-row">
                        <div class="notification-setting-copy">
                            <strong>Notifikasi Email</strong>
                            <span>Izinkan SIPADU mengirim pemberitahuan status pengajuan melalui email.</span>
                        </div>
                        <input type="hidden" name="email_notifications" value="0">
                        <label class="setting-switch" aria-label="Nyalakan notifikasi email">
                            <input type="checkbox" name="email_notifications" value="1" {{ old('email_notifications', $user->email_notifications ?? true) ? 'checked' : '' }}>
                            <span class="setting-slider"></span>
                        </label>
                    </div>

                    <div class="notification-setting-row">
                        <div class="notification-setting-copy">
                            <strong>Notifikasi Sistem</strong>
                            <span>Tampilkan pemberitahuan aktivitas secara langsung di dalam aplikasi SIPADU.</span>
                        </div>
                        <input type="hidden" name="system_notifications" value="0">
                        <label class="setting-switch" aria-label="Nyalakan notifikasi sistem">
                            <input type="checkbox" name="system_notifications" value="1" {{ old('system_notifications', $user->system_notifications ?? true) ? 'checked' : '' }}>
                            <span class="setting-slider"></span>
                        </label>
                    </div>

                    <div class="settings-form-actions">
                        <button type="submit" class="btn-settings-save">Simpan Pengaturan Notifikasi</button>
                    </div>
                </form>
            </div>
        </section>

    </div>
</div>

{{-- MODAL PREVIEW FOTO PROFIL (DITAMPILKAN DI TENGAH LAYAR DENGAN BACKDROP DIGELAPKAN & SMOOTH CLOSE) --}}
<div id="settingsPhotoModal" class="settings-photo-modal" role="dialog" aria-modal="true" aria-labelledby="settingsPhotoModalTitle" aria-hidden="true">
    <div class="settings-photo-dialog">
        <div class="settings-photo-dialog-head">
            <div>
                <strong id="settingsPhotoModalTitle">Preview Foto Profil</strong>
                <small>{{ $userName }}</small>
            </div>
            <button type="button" id="closeSettingsPhotoModal" class="settings-photo-close" aria-label="Tutup preview foto profil">
                <i data-lucide="x"></i>
            </button>
        </div>

        <div class="settings-photo-frame">
            <img id="modalSettingsPhotoImg" src="{{ $photoUrl ?? '' }}" alt="Preview foto profil">
        </div>

        <div class="settings-photo-dialog-foot end-only">
            <button type="button" id="closeSettingsPhotoModalBtn" class="btn-photo-mini">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- MODAL ATUR UKURAN (ZOOM IN / OUT & GESER POSISI) FOTO PROFIL SEBELUM SAVE --}}
<div id="photoCropModal" class="settings-photo-modal" role="dialog" aria-modal="true" aria-labelledby="photoCropModalTitle" aria-hidden="true">
    <div class="settings-photo-dialog">
        <div class="settings-photo-dialog-head">
            <div>
                <strong id="photoCropModalTitle">Atur Ukuran & Posisi Foto</strong>
                <small>Geser foto atau atur zoom sebelum disimpan</small>
            </div>
            <button type="button" id="closePhotoCropModal" class="settings-photo-close" aria-label="Batal atur foto">
                <i data-lucide="x"></i>
            </button>
        </div>

        <div class="photo-crop-stage" id="photoCropStage">
            <canvas id="photoCropCanvas" width="280" height="280"></canvas>
            <div class="photo-crop-ring"></div>
        </div>

        <div class="photo-zoom-bar">
            <button type="button" class="photo-zoom-btn" id="photoZoomOutBtn" title="Perkecil (Zoom Out)" aria-label="Perkecil foto">
                <i data-lucide="zoom-out"></i>
            </button>
            <input type="range" id="photoZoomRange" class="photo-zoom-range" min="1" max="3" step="0.02" value="1" aria-label="Tingkat zoom foto">
            <button type="button" class="photo-zoom-btn" id="photoZoomInBtn" title="Perbesar (Zoom In)" aria-label="Perbesar foto">
                <i data-lucide="zoom-in"></i>
            </button>
            <span class="photo-zoom-pct" id="photoZoomPct">100%</span>
        </div>

        <div class="photo-crop-tip">Seret gambar di dalam kotak untuk mengatur posisi wajah.</div>

        <div class="settings-photo-dialog-foot">
            <button type="button" id="resetPhotoCropBtn" class="btn-photo-mini">
                <i data-lucide="rotate-ccw"></i>
                <span>Reset</span>
            </button>
            <div style="display:flex;gap:8px;">
                <button type="button" id="cancelPhotoCropBtn" class="btn-settings-cancel" style="height:36px;padding:0 14px;">Batal</button>
                <button type="button" id="applyPhotoCropBtn" class="btn-settings-save" style="height:36px;padding:0 16px;">Terapkan Foto</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const panes = document.querySelectorAll('[data-settings-pane]');

    window.sipaduActivateSettingsTab = function (tabKey) {
        panes.forEach(function (pane) {
            pane.hidden = pane.dataset.settingsPane !== tabKey;
        });
    };

    const urlParams = new URLSearchParams(window.location.search);
    const initialParamTab = urlParams.get('tab');
    if (initialParamTab && ['profil', 'password', 'notifikasi'].includes(initialParamTab)) {
        window.sipaduActivateSettingsTab(initialParamTab);
    }

    const profileForm = document.getElementById('profileSettingsForm');
    let studyProgramsMap = {};
    if (profileForm && profileForm.dataset.studyPrograms) {
        try {
            studyProgramsMap = JSON.parse(profileForm.dataset.studyPrograms);
        } catch (e) {
            studyProgramsMap = {};
        }
    }

    /* HELPER: FOKUS & TARUH CURSOR GARIS DI UJUNG KANAN TULISAN */
    function focusAtEnd(input) {
        if (!input) return;
        input.focus();
        const val = input.value || '';
        const len = val.length;
        try {
            input.setSelectionRange(len, len);
        } catch (e) {
            input.value = '';
            input.value = val;
        }
    }

    /* CUSTOM GLASSMORPHISM DROPDOWN */
    function closeAllGlassSelects(exceptSelect) {
        document.querySelectorAll('[data-glass-select].is-open').forEach(function (sel) {
            if (exceptSelect && sel === exceptSelect) return;
            sel.classList.remove('is-open');
            const card = sel.closest('.settings-inner-card');
            if (card) card.classList.remove('has-open-dropdown');
            const trig = sel.querySelector('[data-glass-trigger]');
            const menu = sel.querySelector('[data-glass-menu]');
            if (trig) trig.setAttribute('aria-expanded', 'false');
            if (menu) menu.hidden = true;
        });
    }

    function setGlassSelectValue(selectRoot, newValue, triggerChange) {
        if (!selectRoot) return;
        const hiddenInput = selectRoot.querySelector('[data-glass-input]');
        const trigger = selectRoot.querySelector('[data-glass-trigger]');
        const labelEl = selectRoot.querySelector('[data-glass-label]');
        const menu = selectRoot.querySelector('[data-glass-menu]');
        const isLocked = selectRoot.classList.contains('is-locked');
        const placeholder = trigger ? (trigger.dataset.placeholder || 'Pilih opsi') : 'Pilih opsi';

        if (hiddenInput) {
            hiddenInput.value = newValue || '';
        }

        if (labelEl && trigger) {
            if (newValue) {
                labelEl.textContent = newValue;
                trigger.classList.remove('is-placeholder');
            } else {
                labelEl.textContent = isLocked ? '' : placeholder;
                trigger.classList.add('is-placeholder');
            }
        }

        if (menu) {
            menu.querySelectorAll('.glass-select-option').forEach(function (opt) {
                const isMatch = opt.dataset.value === newValue;
                opt.classList.toggle('is-selected', isMatch);
                opt.setAttribute('aria-selected', isMatch ? 'true' : 'false');
                const oldCheck = opt.querySelector('svg');
                if (oldCheck) oldCheck.remove();
                if (isMatch) {
                    const icon = document.createElement('i');
                    icon.setAttribute('data-lucide', 'check');
                    opt.appendChild(icon);
                }
            });
            if (window.lucide) window.lucide.createIcons();
        }

        if (triggerChange && selectRoot.dataset.glassSelect === 'faculty') {
            populateStudyPrograms(newValue, '');
        }
    }

    function populateStudyPrograms(facultyName, selectedProgram) {
        const prodiSelect = document.querySelector('[data-glass-select="study_program"]');
        if (!prodiSelect) return;

        const menu = prodiSelect.querySelector('[data-glass-menu]');
        const programs = (facultyName && Array.isArray(studyProgramsMap[facultyName]))
            ? studyProgramsMap[facultyName]
            : [];

        if (menu) {
            menu.innerHTML = '';
            if (programs.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'glass-select-empty';
                empty.textContent = 'Pilih fakultas terlebih dahulu';
                menu.appendChild(empty);
            } else {
                programs.forEach(function (prog) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'glass-select-option' + (selectedProgram === prog ? ' is-selected' : '');
                    btn.setAttribute('role', 'option');
                    btn.setAttribute('aria-selected', selectedProgram === prog ? 'true' : 'false');
                    btn.dataset.value = prog;

                    const span = document.createElement('span');
                    span.textContent = prog;
                    btn.appendChild(span);

                    if (selectedProgram === prog) {
                        const icon = document.createElement('i');
                        icon.setAttribute('data-lucide', 'check');
                        btn.appendChild(icon);
                    }
                    menu.appendChild(btn);
                });
            }
        }

        const validSelection = programs.includes(selectedProgram) ? selectedProgram : '';
        setGlassSelectValue(prodiSelect, validSelection, false);
    }

    document.querySelectorAll('[data-glass-select]').forEach(function (selectRoot) {
        const trigger = selectRoot.querySelector('[data-glass-trigger]');
        const menu = selectRoot.querySelector('[data-glass-menu]');

        if (trigger && menu) {
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                if (selectRoot.classList.contains('is-locked')) return;

                const willOpen = !selectRoot.classList.contains('is-open');
                closeAllGlassSelects(selectRoot);

                selectRoot.classList.toggle('is-open', willOpen);
                const card = selectRoot.closest('.settings-inner-card');
                if (card) card.classList.toggle('has-open-dropdown', willOpen);
                trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                menu.hidden = !willOpen;
            });

            menu.addEventListener('click', function (e) {
                const option = e.target.closest('.glass-select-option');
                if (!option) return;
                setGlassSelectValue(selectRoot, option.dataset.value || '', true);
                closeAllGlassSelects();
                trigger.focus();
            });
        }
    });

    document.addEventListener('click', function () {
        closeAllGlassSelects();
    });

    /* FOTO PROFIL, MODAL LIHAT & MODAL CROP/ZOOM SEBELUM SAVE */
    const photoInput = document.getElementById('profilePhotoInput');
    const removePhotoInput = document.getElementById('removeProfilePhotoInput');
    const avatarBox = document.getElementById('settingsAvatarBox');
    const avatarImg = document.getElementById('settingsAvatarImg');
    const avatarInitial = document.getElementById('settingsAvatarInitial');
    const avatarCameraBtn = document.getElementById('avatarCameraBtn');
    const photoEditor = document.getElementById('profilePhotoEditor');
    const quickViewPhotoBtn = document.getElementById('quickViewPhotoBtn');
    const viewPhotoBtn = document.getElementById('viewProfilePhotoBtn');
    const adjustPhotoBtn = document.getElementById('adjustProfilePhotoBtn');
    const removePhotoBtn = document.getElementById('removeProfilePhotoBtn');
    const pickPhotoLabel = document.getElementById('pickPhotoBtnLabel');
    const photoHint = document.getElementById('profilePhotoHint');
    const photoClientError = document.getElementById('profilePhotoClientError');
    const photoModal = document.getElementById('settingsPhotoModal');
    const modalPhotoImg = document.getElementById('modalSettingsPhotoImg');

    /* ELEMEN MODAL CROP / ZOOM */
    const cropModal = document.getElementById('photoCropModal');
    const cropStage = document.getElementById('photoCropStage');
    const cropCanvas = document.getElementById('photoCropCanvas');
    const cropCtx = cropCanvas ? cropCanvas.getContext('2d') : null;
    const zoomRange = document.getElementById('photoZoomRange');
    const zoomOutBtn = document.getElementById('photoZoomOutBtn');
    const zoomInBtn = document.getElementById('photoZoomInBtn');
    const zoomPct = document.getElementById('photoZoomPct');
    const resetCropBtn = document.getElementById('resetPhotoCropBtn');
    const cancelCropBtn = document.getElementById('cancelPhotoCropBtn');
    const closeCropModalBtn = document.getElementById('closePhotoCropModal');
    const applyCropBtn = document.getElementById('applyPhotoCropBtn');

    if (photoModal && photoModal.parentElement !== document.body) {
        document.body.appendChild(photoModal);
    }
    if (cropModal && cropModal.parentElement !== document.body) {
        document.body.appendChild(cropModal);
    }

    const userDisplayName = avatarBox ? (avatarBox.dataset.userName || '') : '';
    const userInitialText = avatarBox ? (avatarBox.dataset.userInitial || 'U') : 'U';
    const savedPhotoUrl = avatarBox && avatarBox.dataset.savedPhoto ? avatarBox.dataset.savedPhoto : null;
    let currentPhotoUrl = savedPhotoUrl;

    /* SINKRONKAN AVATAR DI TOPBAR JIKA GAMBARNYA BROKEN ATAU BELUM TERMUAT */
    function syncTopbarAvatar(src) {
        document.querySelectorAll('header img, nav img, [class*="topbar"] img, [class*="avatar"] img').forEach(function (img) {
            if (img === avatarImg || img === modalPhotoImg) return;
            const altText = (img.getAttribute('alt') || '').trim();
            const imgSrc = (img.getAttribute('src') || '').trim();
            const isUserAvatar =
                img.hasAttribute('data-topbar-avatar-img') ||
                (userDisplayName && altText.toLowerCase() === userDisplayName.toLowerCase()) ||
                imgSrc.includes('/storage/') ||
                imgSrc.includes('profile_photo');

            if (!isUserAvatar) return;

            const parent = img.parentElement;
            if (src) {
                img.src = src;
                img.style.display = 'block';
                img.hidden = false;
                if (parent) {
                    const fallbackSpan = parent.querySelector('span');
                    if (fallbackSpan) fallbackSpan.style.display = 'none';
                }
            } else {
                img.style.display = 'none';
                img.hidden = true;
                if (parent) {
                    let fallbackSpan = parent.querySelector('span');
                    if (!fallbackSpan) {
                        fallbackSpan = document.createElement('span');
                        fallbackSpan.textContent = userInitialText;
                        parent.appendChild(fallbackSpan);
                    }
                    fallbackSpan.style.display = 'flex';
                    fallbackSpan.style.alignItems = 'center';
                    fallbackSpan.style.justifyContent = 'center';
                    fallbackSpan.style.width = '100%';
                    fallbackSpan.style.height = '100%';
                }
            }
        });
    }

    /* SINKRONISASI AWAL SAAT HALAMAN DIMUAT */
    syncTopbarAvatar(savedPhotoUrl);

    /* STATE UNTUK EDITOR ZOOM & POSISI FOTO */
    let rawCropImage = null;
    let rawFileName = 'profile.jpg';
    let rawFileType = 'image/jpeg';
    let cropScale = 1;
    let cropOffsetX = 0;
    let cropOffsetY = 0;
    let isDraggingCrop = false;
    let dragStartX = 0;
    let dragStartY = 0;

    function isAnyPhotoModalOpen() {
        return Boolean(
            (photoModal && photoModal.classList.contains('is-open')) ||
            (cropModal && cropModal.classList.contains('is-open'))
        );
    }

    function syncBodyScrollLock() {
        const locked = isAnyPhotoModalOpen();
        document.documentElement.classList.toggle('modal-scroll-locked', locked);
        document.body.classList.toggle('modal-scroll-locked', locked);
    }

    /* CEGAH WHEEL / TOUCH SCROLL MENEMBUS KE BACKGROUND SAAT MODAL TERBUKA */
    function preventBackgroundScroll(e) {
        if (!isAnyPhotoModalOpen()) return;
        if (cropStage && cropStage.contains(e.target)) return;
        e.preventDefault();
    }

    window.addEventListener('wheel', preventBackgroundScroll, { passive: false });
    window.addEventListener('touchmove', preventBackgroundScroll, { passive: false });

    function updatePhotoPreview(src, hasPendingAdjustedFile) {
        currentPhotoUrl = src || null;
        const hasPhoto = Boolean(currentPhotoUrl);

        if (avatarImg) {
            avatarImg.src = hasPhoto ? currentPhotoUrl : '';
            avatarImg.hidden = !hasPhoto;
        }
        if (avatarInitial) {
            avatarInitial.hidden = hasPhoto;
        }
        if (modalPhotoImg) {
            modalPhotoImg.src = hasPhoto ? currentPhotoUrl : '';
        }
        if (avatarBox) {
            avatarBox.classList.toggle('has-photo', hasPhoto);
            avatarBox.title = hasPhoto ? 'Klik untuk preview foto profil' : userDisplayName;
        }
        if (quickViewPhotoBtn) {
            quickViewPhotoBtn.hidden = !hasPhoto;
        }
        if (viewPhotoBtn) {
            viewPhotoBtn.hidden = !hasPhoto;
        }
        if (adjustPhotoBtn) {
            adjustPhotoBtn.hidden = !Boolean(hasPendingAdjustedFile && rawCropImage);
        }
        if (removePhotoBtn) {
            removePhotoBtn.hidden = !hasPhoto;
        }
        if (pickPhotoLabel) {
            pickPhotoLabel.textContent = hasPhoto ? 'Ubah Foto' : 'Unggah Foto';
        }
    }

    /* FALLBACK JIKA FILE GAMBAR GAGAL DIMUAT */
    if (avatarImg) {
        avatarImg.addEventListener('error', function () {
            if (!avatarImg.getAttribute('src')) return;
            updatePhotoPreview(null, false);
            syncTopbarAvatar(null);
        });
    }

    function openPhotoModal() {
        if (!currentPhotoUrl || !photoModal) return;
        photoModal.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(function () {
            photoModal.classList.add('is-open');
            syncBodyScrollLock();
        });
    }

    function closePhotoModal() {
        if (!photoModal) return;
        photoModal.classList.remove('is-open');
        photoModal.setAttribute('aria-hidden', 'true');
        syncBodyScrollLock();
    }

    function clampCropOffsets() {
        if (!rawCropImage || !cropCanvas) return;
        const size = cropCanvas.width;
        const baseScale = Math.max(size / rawCropImage.width, size / rawCropImage.height);
        const drawW = rawCropImage.width * baseScale * cropScale;
        const drawH = rawCropImage.height * baseScale * cropScale;
        const maxOffsetX = Math.max(0, (drawW - size) / 2);
        const maxOffsetY = Math.max(0, (drawH - size) / 2);

        cropOffsetX = Math.min(maxOffsetX, Math.max(-maxOffsetX, cropOffsetX));
        cropOffsetY = Math.min(maxOffsetY, Math.max(-maxOffsetY, cropOffsetY));
    }

    function renderCropCanvas() {
        if (!rawCropImage || !cropCanvas || !cropCtx) return;
        const size = cropCanvas.width;
        clampCropOffsets();

        cropCtx.clearRect(0, 0, size, size);
        cropCtx.fillStyle = '#102118';
        cropCtx.fillRect(0, 0, size, size);

        const baseScale = Math.max(size / rawCropImage.width, size / rawCropImage.height);
        const drawW = rawCropImage.width * baseScale * cropScale;
        const drawH = rawCropImage.height * baseScale * cropScale;
        const drawX = (size - drawW) / 2 + cropOffsetX;
        const drawY = (size - drawH) / 2 + cropOffsetY;

        cropCtx.imageSmoothingEnabled = true;
        cropCtx.imageSmoothingQuality = 'high';
        cropCtx.drawImage(rawCropImage, drawX, drawY, drawW, drawH);

        if (zoomRange) zoomRange.value = String(cropScale);
        if (zoomPct) zoomPct.textContent = Math.round(cropScale * 100) + '%';
    }

    function openCropModal(resetPosition) {
        if (!rawCropImage || !cropModal) return;
        if (resetPosition) {
            cropScale = 1;
            cropOffsetX = 0;
            cropOffsetY = 0;
        }
        renderCropCanvas();
        cropModal.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(function () {
            cropModal.classList.add('is-open');
            syncBodyScrollLock();
        });
    }

    function closeCropModal() {
        if (!cropModal) return;
        cropModal.classList.remove('is-open');
        cropModal.setAttribute('aria-hidden', 'true');
        isDraggingCrop = false;
        if (cropStage) cropStage.classList.remove('is-dragging');
        syncBodyScrollLock();
    }

    function setZoomLevel(nextScale) {
        cropScale = Math.min(3, Math.max(1, Number(nextScale) || 1));
        renderCropCanvas();
    }

    if (zoomRange) {
        zoomRange.addEventListener('input', function () {
            setZoomLevel(zoomRange.value);
        });
    }
    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function () {
            setZoomLevel(cropScale - 0.15);
        });
    }
    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function () {
            setZoomLevel(cropScale + 0.15);
        });
    }
    if (resetCropBtn) {
        resetCropBtn.addEventListener('click', function () {
            cropScale = 1;
            cropOffsetX = 0;
            cropOffsetY = 0;
            renderCropCanvas();
        });
    }

    if (cropStage) {
        cropStage.addEventListener('pointerdown', function (e) {
            if (!rawCropImage) return;
            isDraggingCrop = true;
            dragStartX = e.clientX - cropOffsetX;
            dragStartY = e.clientY - cropOffsetY;
            cropStage.classList.add('is-dragging');
            cropStage.setPointerCapture(e.pointerId);
        });

        cropStage.addEventListener('pointermove', function (e) {
            if (!isDraggingCrop) return;
            cropOffsetX = e.clientX - dragStartX;
            cropOffsetY = e.clientY - dragStartY;
            renderCropCanvas();
        });

        function endCropDrag(e) {
            if (!isDraggingCrop) return;
            isDraggingCrop = false;
            cropStage.classList.remove('is-dragging');
            if (e && e.pointerId !== undefined) {
                try { cropStage.releasePointerCapture(e.pointerId); } catch (err) {}
            }
        }

        cropStage.addEventListener('pointerup', endCropDrag);
        cropStage.addEventListener('pointercancel', endCropDrag);

        cropStage.addEventListener('wheel', function (e) {
            e.preventDefault();
            const delta = e.deltaY < 0 ? 0.1 : -0.1;
            setZoomLevel(cropScale + delta);
        }, { passive: false });
    }

    if (applyCropBtn) {
        applyCropBtn.addEventListener('click', function () {
            if (!rawCropImage || !cropCanvas) return;

            const outputSize = 512;
            const outCanvas = document.createElement('canvas');
            outCanvas.width = outputSize;
            outCanvas.height = outputSize;
            const outCtx = outCanvas.getContext('2d');

            const stageSize = cropCanvas.width;
            const ratio = outputSize / stageSize;
            const baseScale = Math.max(stageSize / rawCropImage.width, stageSize / rawCropImage.height);
            const drawW = rawCropImage.width * baseScale * cropScale * ratio;
            const drawH = rawCropImage.height * baseScale * cropScale * ratio;
            const drawX = (outputSize - drawW) / 2 + cropOffsetX * ratio;
            const drawY = (outputSize - drawH) / 2 + cropOffsetY * ratio;

            outCtx.imageSmoothingEnabled = true;
            outCtx.imageSmoothingQuality = 'high';
            outCtx.drawImage(rawCropImage, drawX, drawY, drawW, drawH);

            const exportMime = (rawFileType === 'image/png' || rawFileType === 'image/webp')
                ? rawFileType
                : 'image/jpeg';

            outCanvas.toBlob(function (blob) {
                if (!blob) return;

                if (photoInput && typeof DataTransfer !== 'undefined') {
                    const dt = new DataTransfer();
                    const croppedFile = new File([blob], rawFileName, {
                        type: exportMime,
                        lastModified: Date.now()
                    });
                    dt.items.add(croppedFile);
                    photoInput.files = dt.files;
                }

                if (removePhotoInput) removePhotoInput.value = '0';

                const previewDataUrl = outCanvas.toDataURL(exportMime, 0.92);
                updatePhotoPreview(previewDataUrl, true);
                if (photoHint) {
                    photoHint.textContent = 'Foto siap disimpan: ' + rawFileName + ' (klik Simpan Perubahan).';
                }
                closeCropModal();
            }, exportMime, 0.92);
        });
    }

    if (cancelCropBtn) {
        cancelCropBtn.addEventListener('click', function () {
            closeCropModal();
            if (photoInput && !photoInput.files.length) {
                setTimeout(function () {
                    if (!cropModal.classList.contains('is-open')) {
                        rawCropImage = null;
                    }
                }, 280);
            }
        });
    }
    if (closeCropModalBtn) {
        closeCropModalBtn.addEventListener('click', closeCropModal);
    }
    if (cropModal) {
        cropModal.addEventListener('click', function (e) {
            if (e.target === cropModal) closeCropModal();
        });
    }
    if (adjustPhotoBtn) {
        adjustPhotoBtn.addEventListener('click', function () {
            openCropModal(false);
        });
    }

    document.querySelectorAll('[data-open-photo-modal]').forEach(function (btn) {
        btn.addEventListener('click', openPhotoModal);
    });

    if (avatarBox) {
        avatarBox.addEventListener('click', function () {
            if (currentPhotoUrl) openPhotoModal();
        });
    }

    /* MODE EDIT PER MASING-MASING KONTAINER KARTU */
    document.querySelectorAll('[data-profile-card]').forEach(function (card) {
        const cardKey = card.dataset.profileCard;
        const editBtn = card.querySelector('[data-card-edit-btn]');
        const actionBar = card.querySelector('[data-card-actions]');
        const cancelBtn = card.querySelector('[data-card-cancel-btn]');

        const textInputs = Array.from(card.querySelectorAll('input[name]:not([type="hidden"]):not([type="file"])'));
        const glassSelects = Array.from(card.querySelectorAll('[data-glass-select]'));

        const originalInputValues = new Map(textInputs.map(function (inp) {
            return [inp, inp.value];
        }));
        const originalSelectValues = new Map(glassSelects.map(function (sel) {
            const hiddenInp = sel.querySelector('[data-glass-input]');
            return [sel, hiddenInp ? hiddenInp.value : ''];
        }));

        function setCardEditMode(editing, focusFirst) {
            closeAllGlassSelects();
            card.classList.toggle('is-card-editing', editing);

            textInputs.forEach(function (inp) {
                if (editing) {
                    inp.removeAttribute('readonly');
                    inp.removeAttribute('tabindex');
                    inp.placeholder = inp.dataset.editPlaceholder || '';
                } else {
                    inp.setAttribute('readonly', 'readonly');
                    inp.setAttribute('tabindex', '-1');
                    inp.placeholder = '';
                }
            });

            glassSelects.forEach(function (sel) {
                sel.classList.toggle('is-locked', !editing);
                const trig = sel.querySelector('[data-glass-trigger]');
                const hiddenInp = sel.querySelector('[data-glass-input]');
                if (trig) {
                    if (editing) {
                        trig.removeAttribute('tabindex');
                    } else {
                        trig.setAttribute('tabindex', '-1');
                    }
                }
                setGlassSelectValue(sel, hiddenInp ? hiddenInp.value : '', false);
            });

            if (cardKey === 'identity') {
                if (avatarCameraBtn) avatarCameraBtn.hidden = !editing;
                if (photoEditor) photoEditor.hidden = !editing;
            }

            if (editBtn) {
                editBtn.classList.toggle('is-editing', editing);
            }

            if (actionBar) {
                actionBar.classList.toggle('is-hidden', !editing);
            }

            if (editing && focusFirst && textInputs.length > 0) {
                focusAtEnd(textInputs[0]);
            }
        }

        if (editBtn) {
            editBtn.addEventListener('click', function () {
                const isAlreadyEditing = card.classList.contains('is-card-editing');
                if (!isAlreadyEditing) {
                    setCardEditMode(true, true);
                } else if (textInputs.length > 0) {
                    focusAtEnd(textInputs[0]);
                }
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                textInputs.forEach(function (inp) {
                    if (originalInputValues.has(inp)) {
                        inp.value = originalInputValues.get(inp);
                    }
                });

                if (cardKey === 'organization') {
                    const facultySel = card.querySelector('[data-glass-select="faculty"]');
                    const prodiSel = card.querySelector('[data-glass-select="study_program"]');
                    if (facultySel && prodiSel) {
                        const origFaculty = originalSelectValues.get(facultySel) || '';
                        const origProdi = originalSelectValues.get(prodiSel) || '';
                        setGlassSelectValue(facultySel, origFaculty, false);
                        populateStudyPrograms(origFaculty, origProdi);
                    } else {
                        glassSelects.forEach(function (sel) {
                            setGlassSelectValue(sel, originalSelectValues.get(sel) || '', false);
                        });
                    }
                }

                if (cardKey === 'identity') {
                    rawCropImage = null;
                    if (photoInput) photoInput.value = '';
                    if (removePhotoInput) removePhotoInput.value = '0';
                    if (photoClientError) {
                        photoClientError.hidden = true;
                        photoClientError.textContent = '';
                    }
                    if (photoHint) {
                        photoHint.textContent = 'Format JPG, JPEG, PNG, atau WEBP (maks. 2 MB).';
                    }
                    updatePhotoPreview(savedPhotoUrl, false);
                }

                setCardEditMode(false, false);
            });
        }
    });

    document.querySelectorAll('[data-pick-photo]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (photoInput) {
                photoInput.value = '';
                photoInput.click();
            }
        });
    });

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            if (photoClientError) {
                photoClientError.hidden = true;
                photoClientError.textContent = '';
            }

            const file = photoInput.files && photoInput.files[0];
            if (!file) return;

            const allowedMimes = ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/webp'];
            const allowedExts = /\.(jpe?g|png|webp)$/i;
            if (!allowedMimes.includes(file.type) && !allowedExts.test(file.name)) {
                photoInput.value = '';
                if (photoClientError) {
                    photoClientError.textContent = 'Format foto harus JPG, JPEG, PNG, atau WEBP.';
                    photoClientError.hidden = false;
                }
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                photoInput.value = '';
                if (photoClientError) {
                    photoClientError.textContent = 'Ukuran foto profil maksimal 2 MB.';
                    photoClientError.hidden = false;
                }
                return;
            }

            rawFileName = file.name || 'profile.jpg';
            rawFileType = (file.type === 'image/png' || file.type === 'image/webp') ? file.type : 'image/jpeg';

            const reader = new FileReader();
            reader.onload = function (ev) {
                const img = new Image();
                img.onload = function () {
                    rawCropImage = img;
                    openCropModal(true);
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    if (removePhotoBtn) {
        removePhotoBtn.addEventListener('click', function () {
            rawCropImage = null;
            if (photoInput) photoInput.value = '';
            if (removePhotoInput) removePhotoInput.value = '1';
            updatePhotoPreview(null, false);
            if (photoHint) {
                photoHint.textContent = 'Foto profil akan dihapus setelah klik Simpan Perubahan.';
            }
        });
    }

    const closeModal1 = document.getElementById('closeSettingsPhotoModal');
    const closeModal2 = document.getElementById('closeSettingsPhotoModalBtn');
    if (closeModal1) closeModal1.addEventListener('click', closePhotoModal);
    if (closeModal2) closeModal2.addEventListener('click', closePhotoModal);
    if (photoModal) {
        photoModal.addEventListener('click', function (e) {
            if (e.target === photoModal) closePhotoModal();
        });
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeCropModal();
            closePhotoModal();
            closeAllGlassSelects();
        }
    });

    /* TOGGLE LIHAT PASSWORD (DISAMAKAN DENGAN HALAMAN LOGIN) */
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.togglePassword);
            if (!input) return;

            const showing = input.type === 'password';
            input.type = showing ? 'text' : 'password';

            button.setAttribute(
                'aria-label',
                showing ? 'Sembunyikan password' : 'Tampilkan password'
            );

            button.setAttribute(
                'aria-pressed',
                showing ? 'true' : 'false'
            );

            button.innerHTML = showing
                ? '<i data-lucide="eye-off"></i>'
                : '<i data-lucide="eye"></i>';

            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>

@endsection