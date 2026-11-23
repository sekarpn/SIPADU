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

    $photoPath = data_get($user, 'profile_photo_path');
    $photoUrl = $photoPath ? asset('storage/' . $photoPath) : null;

    $roleBadgeLabel = $isAdmin ? 'ADMINISTRATOR' : ($isEmployee ? 'PEGAWAI' : 'MAHASISWA');

    $orgConfig = config('organization', []);
    $faculties = $orgConfig['faculties'] ?? [];
    $studyProgramsByFaculty = $orgConfig['study_programs'] ?? [];
    $employeeUnits = $orgConfig['employee_units'] ?? [];
    $positions = $orgConfig['positions'] ?? [];

    $selectedFaculty = (string) old('faculty', data_get($user, 'faculty', ''));
    $selectedStudyProgram = (string) old('study_program', data_get($user, 'study_program', ''));
    $selectedEmployeeUnit = (string) old('employee_unit', data_get($user, 'employee_unit', ''));
    $selectedPosition = (string) old('position', data_get($user, 'position', ''));

    $hasPersonalErrors = $errors->hasAny(['name', 'email', 'phone']);
    $hasOrgErrors = $errors->hasAny(['nim_nip', 'faculty', 'study_program', 'employee_unit', 'position']);
    $hasPhotoErrors = $errors->has('profile_photo');
    $hasProfileErrors = $hasPersonalErrors || $hasOrgErrors || $hasPhotoErrors;
    $hasPasswordErrors = $errors->hasAny(['current_password', 'password', 'password_confirmation']);
    $initialEditMode = $hasProfileErrors || session('profile_incomplete');

    $activeTab = request('tab', 'profil');
    if ($hasPasswordErrors || session('password_success')) {
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
        --st-ink: #163626;
        --st-muted: #556b5f;
        --st-subtle: #6f8277;
        --st-line: rgba(22, 54, 38, .09);
        --st-green: #1f7a49;
        --st-green-mid: #237c4d;
        --st-green-soft: rgba(33, 128, 77, .14);
        --st-glass: linear-gradient(145deg, rgba(255, 255, 255, .72) 0%, rgba(255, 255, 255, .48) 100%);
        --st-border: 1px solid rgba(255, 255, 255, .68);
        --st-shadow: 0 8px 28px rgba(22, 54, 38, .05);
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
        box-shadow: var(--st-shadow);
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
        background: rgba(33, 128, 77, .14);
        border: 1px solid rgba(255, 255, 255, .45);
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

    /* INNER CARDS DI DALAM KONTAINER UTAMA */
    .settings-inner-card {
        background: rgba(255, 255, 255, .86);
        border: 1px solid rgba(22, 54, 38, .08);
        border-radius: 16px;
        padding: 20px 22px;
        box-shadow: 0 2px 10px rgba(22, 54, 38, .03);
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .settings-inner-card.is-card-editing {
        background: rgba(255, 255, 255, .94);
        border-color: rgba(35, 124, 77, .32);
        box-shadow: 0 8px 24px rgba(22, 54, 38, .07);
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

    .settings-identity-body {
        flex: 1;
        min-width: 0;
    }

    .settings-identity-left {
        display: flex;
        align-items: center;
        gap: 18px;
        min-width: 0;
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
        background: linear-gradient(135deg, #1b5e3d 0%, #2b8a57 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: .02em;
        overflow: hidden;
        border: 3px solid rgba(255, 255, 255, .92);
        box-shadow: 0 6px 16px rgba(31, 122, 73, .18);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .settings-avatar.has-photo {
        cursor: pointer;
    }

    .settings-avatar.has-photo:hover {
        transform: scale(1.03);
        box-shadow: 0 8px 20px rgba(31, 122, 73, .26);
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
        box-shadow: 0 3px 8px rgba(22, 54, 38, .22);
        transition: background .18s ease, transform .18s ease;
    }

    .settings-avatar-cam[hidden] {
        display: none !important;
    }

    .settings-avatar-cam:hover {
        background: #1b663f;
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

    .settings-role-badge {
        display: inline-block;
        margin-top: 9px;
        padding: 5px 12px;
        border-radius: 999px;
        background: rgba(33, 128, 77, .14);
        border: 1px solid rgba(33, 128, 77, .24);
        color: var(--st-green);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .05em;
        line-height: 1.2;
    }

    /* AKSI FOTO PROFIL (MUNCUL HANYA SAAT EDIT KARTU PROFIL AKTIF) */
    .settings-photo-editor {
        margin-top: 12px;
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
        padding: 5px 10px;
        border-radius: 8px;
        border: 1px solid rgba(22, 54, 38, .14);
        background: #f6f9f7;
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
        background: #eaf3ed;
        border-color: var(--st-green-mid);
        color: var(--st-green);
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

    /* MODAL LIHAT FOTO PROFIL */
    .settings-photo-modal {
        position: fixed;
        inset: 0;
        z-index: 11000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(12, 28, 19, .55);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .settings-photo-modal.is-open {
        display: flex;
    }

    .settings-photo-dialog {
        width: 100%;
        max-width: 420px;
        border-radius: 18px;
        padding: 20px;
        background: rgba(255, 255, 255, .94);
        border: 1px solid rgba(255, 255, 255, .75);
        box-shadow: 0 24px 60px rgba(10, 28, 19, .26);
        color: var(--st-ink);
        box-sizing: border-box;
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
        color: var(--st-subtle);
    }

    .settings-photo-close {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        border: 1px solid rgba(22, 54, 38, .12);
        background: #ffffff;
        color: var(--st-ink);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .settings-photo-close:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    .settings-photo-frame {
        width: 100%;
        aspect-ratio: 1 / 1;
        max-height: 360px;
        border-radius: 14px;
        overflow: hidden;
        background: #edf4ef;
        border: 1px solid rgba(22, 54, 38, .1);
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

    .settings-photo-dialog-foot {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 14px;
    }

    /* EDIT BUTTON */
    .btn-card-edit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 10px;
        border: 1px solid rgba(22, 54, 38, .15);
        background: #ffffff;
        color: var(--st-ink);
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        flex-shrink: 0;
        transition: background .18s ease, border-color .18s ease, color .18s ease;
    }

    .btn-card-edit:hover {
        background: #f4f8f5;
        border-color: var(--st-green-mid);
        color: var(--st-green);
    }

    .btn-card-edit.is-editing {
        background: var(--st-green-soft);
        border-color: rgba(35, 124, 77, .35);
        color: var(--st-green);
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

    .settings-field label {
        display: block;
        font-size: 11.5px;
        font-weight: 600;
        color: #7c8b82;
        margin-bottom: 6px;
        transition: color .18s ease;
    }

    .settings-inner-card.is-card-editing .settings-field label {
        color: var(--st-muted);
    }

    .settings-input-wrap {
        position: relative;
    }

    .settings-field input {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 13px;
        border-radius: 10px;
        border: 1px solid rgba(22, 54, 38, .14);
        background: #ffffff;
        color: var(--st-ink);
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease;
    }

    /* TAMPILAN ABU-ABU SAAT BELUM KLIK EDIT */
    .settings-field input[readonly] {
        background: #f1f4f2;
        border-color: rgba(22, 54, 38, .1);
        color: #6b7d73;
        cursor: default;
    }

    .settings-field input:not([readonly]):focus {
        background: #fff;
        border-color: var(--st-green-mid);
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .14);
    }

    .settings-field input:disabled {
        background: #eef2f0;
        border-color: rgba(22, 54, 38, .09);
        color: #7d8e84;
        cursor: not-allowed;
    }

    /* GLASS DROPDOWN KUSTOM (TANPA NATIVE SELECT) */
    .st-glass-select {
        position: relative;
        width: 100%;
    }

    .st-glass-select.is-open {
        z-index: 45;
    }

    .st-glass-select-trigger {
        width: 100%;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 13px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, .82);
        background: linear-gradient(145deg, rgba(255, 255, 255, .92) 0%, rgba(242, 249, 245, .8) 100%);
        backdrop-filter: blur(14px) saturate(145%);
        -webkit-backdrop-filter: blur(14px) saturate(145%);
        color: var(--st-ink);
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        text-align: left;
        cursor: pointer;
        outline: none;
        box-shadow: 0 2px 10px rgba(22, 54, 38, .05), inset 0 1px 0 rgba(255, 255, 255, .9);
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease;
    }

    .st-glass-select-trigger:hover:not(:disabled) {
        background: linear-gradient(145deg, rgba(255, 255, 255, .98) 0%, rgba(236, 246, 240, .88) 100%);
        border-color: rgba(35, 124, 77, .36);
    }

    .st-glass-select-trigger:focus-visible,
    .st-glass-select.is-open .st-glass-select-trigger {
        background: rgba(255, 255, 255, .98);
        border-color: var(--st-green-mid);
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .14), 0 6px 18px rgba(22, 54, 38, .08);
    }

    /* GLASS DROPDOWN KEABU-ABUAN SAAT BELUM EDIT */
    .st-glass-select.is-readonly .st-glass-select-trigger {
        background: linear-gradient(145deg, rgba(237, 241, 239, .86) 0%, rgba(227, 232, 229, .74) 100%);
        border-color: rgba(22, 54, 38, .11);
        color: #6b7d73;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .6);
        cursor: default;
    }

    .st-glass-select.is-readonly .st-glass-select-trigger:hover {
        background: linear-gradient(145deg, rgba(237, 241, 239, .86) 0%, rgba(227, 232, 229, .74) 100%);
        border-color: rgba(22, 54, 38, .11);
    }

    .st-glass-select-value {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .st-glass-select-value.is-placeholder {
        color: var(--st-subtle);
        font-weight: 500;
    }

    .st-glass-select.is-readonly .st-glass-select-value,
    .st-glass-select.is-readonly .st-glass-select-value.is-placeholder {
        color: #7c8d83;
    }

    .st-glass-select-chevron {
        width: 16px;
        height: 16px;
        color: var(--st-muted);
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform .18s ease, color .18s ease;
    }

    .st-glass-select.is-readonly .st-glass-select-chevron {
        color: #8b9b91;
    }

    .st-glass-select-chevron svg {
        width: 15px;
        height: 15px;
    }

    .st-glass-select.is-open .st-glass-select-chevron {
        transform: rotate(180deg);
        color: var(--st-green);
    }

    .st-glass-select-menu {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        z-index: 90;
        max-height: 248px;
        overflow-y: auto;
        padding: 6px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, .82);
        background: linear-gradient(155deg, rgba(255, 255, 255, .94) 0%, rgba(243, 249, 245, .89) 100%);
        backdrop-filter: blur(22px) saturate(165%);
        -webkit-backdrop-filter: blur(22px) saturate(165%);
        box-shadow: 0 16px 36px rgba(22, 54, 38, .14), inset 0 1px 0 rgba(255, 255, 255, .9);
    }

    .st-glass-select-menu[hidden] {
        display: none !important;
    }

    .st-glass-select-option {
        width: 100%;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 9px 11px;
        border: 0;
        border-radius: 9px;
        background: transparent;
        color: var(--st-ink);
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        text-align: left;
        cursor: pointer;
        transition: background .14s ease, color .14s ease;
    }

    .st-glass-select-option:hover {
        background: rgba(35, 124, 77, .1);
        color: var(--st-green);
    }

    .st-glass-select-option.is-selected {
        background: rgba(35, 124, 77, .15);
        color: var(--st-green);
        font-weight: 700;
    }

    .st-glass-select-check {
        width: 14px;
        height: 14px;
        color: var(--st-green);
        flex-shrink: 0;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .st-glass-select-check svg {
        width: 14px;
        height: 14px;
    }

    .st-glass-select-option.is-selected .st-glass-select-check {
        display: inline-flex;
    }

    .st-glass-select-empty {
        padding: 10px 12px;
        font-size: 12px;
        color: var(--st-subtle);
        text-align: center;
    }

    .settings-input-wrap input {
        padding-right: 40px;
    }

    .settings-eye-btn {
        position: absolute;
        right: 9px;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: var(--st-subtle);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .settings-eye-btn:hover {
        background: rgba(22, 54, 38, .06);
        color: var(--st-ink);
    }

    .settings-eye-btn svg {
        width: 16px;
        height: 16px;
    }

    .settings-error {
        margin-top: 5px;
        font-size: 11.5px;
        font-weight: 600;
        color: #b91c1c;
    }

    .settings-error[hidden] {
        display: none !important;
    }

    /* ACTION BAR */
    .settings-form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
    }

    .settings-form-actions.is-hidden {
        display: none;
    }

    .btn-settings-cancel {
        padding: 10px 16px;
        border-radius: 10px;
        border: 1px solid rgba(22, 54, 38, .15);
        background: #ffffff;
        color: var(--st-muted);
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-settings-cancel:hover {
        background: #f4f8f5;
        color: var(--st-ink);
    }

    .btn-settings-save {
        padding: 10px 18px;
        border: 0;
        border-radius: 10px;
        background: var(--st-green-mid);
        color: #fff;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(35, 124, 77, .2);
        transition: background .18s ease, transform .18s ease;
    }

    .btn-settings-save:hover {
        background: #1b663f;
        transform: translateY(-1px);
    }

    /* NOTIFICATION SWITCHES */
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
        background: #fff;
        transition: transform .2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .18);
    }

    .setting-switch input:checked + .setting-slider {
        background: var(--st-green-mid);
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
            align-items: flex-start;
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

            <form method="POST" action="{{ $profileUpdateRoute }}" id="profileSettingsForm" class="settings-stack" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/jpeg,image/png,image/webp" hidden>
                <input type="hidden" id="removeProfilePhotoInput" name="remove_profile_photo" value="0">

                {{-- KARTU 1: RINGKASAN AKUN --}}
                <div class="settings-inner-card settings-identity-card {{ $hasPhotoErrors ? 'is-card-editing' : '' }}" data-profile-card="identity">
                    <div class="settings-identity-body">
                        <div class="settings-identity-left">
                            <div class="settings-avatar-wrap">
                                <div class="settings-avatar {{ $photoUrl ? 'has-photo' : '' }}" id="settingsAvatarBox" data-user-name="{{ $userName }}" data-saved-photo="{{ $photoUrl ?? '' }}" title="{{ $photoUrl ? 'Klik untuk melihat foto profil' : $userName }}">
                                    <img id="settingsAvatarImg" src="{{ $photoUrl ?? '' }}" alt="Foto profil {{ $userName }}" {{ $photoUrl ? '' : 'hidden' }}>
                                    <span id="settingsAvatarInitial" {{ $photoUrl ? 'hidden' : '' }}>{{ $initial }}</span>
                                </div>
                                <button type="button" class="settings-avatar-cam" id="settingsAvatarCamBtn" data-pick-photo title="Ubah foto profil" aria-label="Ubah foto profil" {{ $hasPhotoErrors ? '' : 'hidden' }}>
                                    <i data-lucide="camera"></i>
                                </button>
                            </div>

                            <div class="settings-identity-meta">
                                <h3>{{ $userName }}</h3>
                                <p class="meta-email">{{ $userEmail }}</p>

                                <span class="settings-role-badge">
                                    {{ $roleBadgeLabel }}
                                </span>
                            </div>
                        </div>

                        <div id="profilePhotoEditor" class="settings-photo-editor" {{ $hasPhotoErrors ? '' : 'hidden' }}>
                            <div class="settings-photo-actions">
                                <button type="button" class="btn-photo-mini" id="viewProfilePhotoBtn" {{ $photoUrl ? '' : 'hidden' }}>
                                    <i data-lucide="eye"></i>
                                    <span>Lihat Foto</span>
                                </button>
                                <button type="button" class="btn-photo-mini" data-pick-photo>
                                    <i data-lucide="image-plus"></i>
                                    <span id="pickPhotoBtnLabel">{{ $photoUrl ? 'Ubah Foto' : 'Unggah Foto' }}</span>
                                </button>
                                <button type="button" class="btn-photo-mini danger" id="removeProfilePhotoBtn" {{ $photoUrl ? '' : 'hidden' }}>
                                    <i data-lucide="trash-2"></i>
                                    <span>Hapus Foto</span>
                                </button>
                            </div>

                            <div id="profilePhotoHint" class="settings-photo-hint">Format JPG, PNG, atau WEBP (maks. 2 MB).</div>
                            <div id="profilePhotoClientError" class="settings-error" hidden></div>
                            @error('profile_photo')
                                <div class="settings-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="settings-form-actions {{ $hasPhotoErrors ? '' : 'is-hidden' }}" data-card-actions="identity">
                            <button type="button" class="btn-settings-cancel" data-cancel-card="identity">Batal</button>
                            <button type="submit" class="btn-settings-save">Simpan Perubahan</button>
                        </div>
                    </div>

                    <button type="button" class="btn-card-edit {{ $hasPhotoErrors ? 'is-editing' : '' }}" data-enable-card-edit="identity">
                        <span>Edit</span>
                        <i data-lucide="pencil"></i>
                    </button>
                </div>

                {{-- KARTU 2: INFORMASI PRIBADI --}}
                <div class="settings-inner-card {{ $hasPersonalErrors ? 'is-card-editing' : '' }}" data-profile-card="personal">
                    <div class="settings-card-top">
                        <div>
                            <h3>Informasi Pribadi</h3>
                        </div>
                        <button type="button" class="btn-card-edit {{ $hasPersonalErrors ? 'is-editing' : '' }}" data-enable-card-edit="personal">
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
                                   placeholder="Masukkan nama lengkap"
                                   {{ $hasPersonalErrors ? '' : 'readonly' }}
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
                                   placeholder="Masukkan email"
                                   {{ $hasPersonalErrors ? '' : 'readonly' }}
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
                                   placeholder="Contoh: 081234567890"
                                   {{ $hasPersonalErrors ? '' : 'readonly' }}>
                            @error('phone')
                                <div class="settings-error">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($isAdmin)
                            <div class="settings-field">
                                <label>Peran Akun</label>
                                <input type="text" value="Administrator" disabled>
                            </div>
                        @endif
                    </div>

                    <div class="settings-form-actions {{ $hasPersonalErrors ? '' : 'is-hidden' }}" data-card-actions="personal">
                        <button type="button" class="btn-settings-cancel" data-cancel-card="personal">Batal</button>
                        <button type="submit" class="btn-settings-save">Simpan Perubahan</button>
                    </div>
                </div>

                {{-- KARTU 3: INFORMASI AKADEMIK / KEPEGAWAIAN / ADMINISTRATOR --}}
                @php
                    $orgInitiallyEditing = $hasOrgErrors || (!$hasPersonalErrors && !$hasPhotoErrors && session('profile_incomplete'));
                @endphp
                <div class="settings-inner-card {{ $orgInitiallyEditing ? 'is-card-editing' : '' }}" data-profile-card="organization">
                    <div class="settings-card-top">
                        <div>
                            <h3>{{ $isAdmin ? 'Informasi Administrator' : ($isEmployee ? 'Informasi Kepegawaian' : 'Informasi Akademik') }}</h3>
                        </div>
                        <button type="button" class="btn-card-edit {{ $orgInitiallyEditing ? 'is-editing' : '' }}" data-enable-card-edit="organization">
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
                                       placeholder="Masukkan NIP"
                                       {{ $orgInitiallyEditing ? '' : 'readonly' }}>
                                @error('nim_nip')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="settings-field">
                                <label>Instansi</label>
                                <input type="text" value="UPN Veteran Jakarta" disabled>
                            </div>
                        @elseif($isEmployee)
                            <div class="settings-field">
                                <label for="profileNimNip">NIP</label>
                                <input id="profileNimNip"
                                       type="text"
                                       name="nim_nip"
                                       value="{{ old('nim_nip', data_get($user, 'nim_nip')) }}"
                                       placeholder="Masukkan NIP"
                                       {{ $orgInitiallyEditing ? '' : 'readonly' }}
                                       required>
                                @error('nim_nip')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="settings-field">
                                <label for="profilePositionTrigger">Jabatan</label>
                                <div class="st-glass-select {{ $orgInitiallyEditing ? '' : 'is-readonly' }}" data-st-select="position">
                                    <input type="hidden" id="profilePosition" name="position" value="{{ $selectedPosition }}" required>
                                    <button type="button" id="profilePositionTrigger" class="st-glass-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="st-glass-select-value {{ $selectedPosition === '' ? 'is-placeholder' : '' }}" data-st-select-label>{{ $selectedPosition !== '' ? $selectedPosition : 'Pilih jabatan' }}</span>
                                        <span class="st-glass-select-chevron"><i data-lucide="chevron-down"></i></span>
                                    </button>
                                    <div class="st-glass-select-menu" role="listbox" data-st-select-menu hidden></div>
                                </div>
                                @error('position')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="settings-field full-span">
                                <label for="profileEmployeeUnitTrigger">Unit / Biro Kerja</label>
                                <div class="st-glass-select {{ $orgInitiallyEditing ? '' : 'is-readonly' }}" data-st-select="employee_unit">
                                    <input type="hidden" id="profileEmployeeUnit" name="employee_unit" value="{{ $selectedEmployeeUnit }}" required>
                                    <button type="button" id="profileEmployeeUnitTrigger" class="st-glass-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="st-glass-select-value {{ $selectedEmployeeUnit === '' ? 'is-placeholder' : '' }}" data-st-select-label>{{ $selectedEmployeeUnit !== '' ? $selectedEmployeeUnit : 'Pilih unit atau biro kerja' }}</span>
                                        <span class="st-glass-select-chevron"><i data-lucide="chevron-down"></i></span>
                                    </button>
                                    <div class="st-glass-select-menu" role="listbox" data-st-select-menu hidden></div>
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
                                       placeholder="Masukkan NIM"
                                       {{ $orgInitiallyEditing ? '' : 'readonly' }}
                                       required>
                                @error('nim_nip')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="settings-field">
                                <label for="profileFacultyTrigger">Fakultas</label>
                                <div class="st-glass-select {{ $orgInitiallyEditing ? '' : 'is-readonly' }}" data-st-select="faculty">
                                    <input type="hidden" id="profileFaculty" name="faculty" value="{{ $selectedFaculty }}" required>
                                    <button type="button" id="profileFacultyTrigger" class="st-glass-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="st-glass-select-value {{ $selectedFaculty === '' ? 'is-placeholder' : '' }}" data-st-select-label>{{ $selectedFaculty !== '' ? $selectedFaculty : 'Pilih fakultas' }}</span>
                                        <span class="st-glass-select-chevron"><i data-lucide="chevron-down"></i></span>
                                    </button>
                                    <div class="st-glass-select-menu" role="listbox" data-st-select-menu hidden></div>
                                </div>
                                @error('faculty')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="settings-field full-span">
                                <label for="profileStudyProgramTrigger">Program Studi</label>
                                <div class="st-glass-select {{ $orgInitiallyEditing ? '' : 'is-readonly' }}" data-st-select="study_program">
                                    <input type="hidden" id="profileStudyProgram" name="study_program" value="{{ $selectedStudyProgram }}" required>
                                    <button type="button" id="profileStudyProgramTrigger" class="st-glass-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="st-glass-select-value {{ $selectedStudyProgram === '' ? 'is-placeholder' : '' }}" data-st-select-label>{{ $selectedStudyProgram !== '' ? $selectedStudyProgram : ($selectedFaculty !== '' ? 'Pilih program studi' : 'Pilih fakultas terlebih dahulu') }}</span>
                                        <span class="st-glass-select-chevron"><i data-lucide="chevron-down"></i></span>
                                    </button>
                                    <div class="st-glass-select-menu" role="listbox" data-st-select-menu hidden></div>
                                </div>
                                @error('study_program')
                                    <div class="settings-error">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="settings-form-actions {{ $orgInitiallyEditing ? '' : 'is-hidden' }}" data-card-actions="organization">
                        <button type="button" class="btn-settings-cancel" data-cancel-card="organization">Batal</button>
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
                                <button type="button" class="settings-eye-btn" data-toggle-password="currentPasswordInput" aria-label="Tampilkan password">
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
                                <button type="button" class="settings-eye-btn" data-toggle-password="newPasswordInput" aria-label="Tampilkan password">
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
                                <button type="button" class="settings-eye-btn" data-toggle-password="confirmPasswordInput" aria-label="Tampilkan password">
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

{{-- MODAL LIHAT FOTO PROFIL --}}
<div id="settingsPhotoModal" class="settings-photo-modal" role="dialog" aria-modal="true" aria-labelledby="settingsPhotoModalTitle">
    <div class="settings-photo-dialog">
        <div class="settings-photo-dialog-head">
            <div>
                <strong id="settingsPhotoModalTitle">Foto Profil</strong>
                <small>{{ $userName }} · {{ $roleBadgeLabel }}</small>
            </div>
            <button type="button" id="closeSettingsPhotoModal" class="settings-photo-close" aria-label="Tutup foto profil">
                <i data-lucide="x"></i>
            </button>
        </div>

        <div class="settings-photo-frame">
            <img id="modalSettingsPhotoImg" src="{{ $photoUrl ?? '' }}" alt="Foto profil {{ $userName }}">
        </div>

        <div class="settings-photo-dialog-foot">
            <button type="button" class="btn-photo-mini" data-pick-photo>
                <i data-lucide="camera"></i>
                <span>Ubah Foto</span>
            </button>
            <button type="button" id="closeSettingsPhotoModalBtn" class="btn-photo-mini">
                Tutup
            </button>
        </div>
    </div>
</div>

<script type="application/json" id="sipaduProfileOrgData">{!! json_encode([
    'faculties' => array_values($faculties),
    'studyPrograms' => $studyProgramsByFaculty,
    'employeeUnits' => array_values($employeeUnits),
    'positions' => array_values($positions),
], JSON_UNESCAPED_UNICODE) !!}</script>

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

    /* GLASS DROPDOWN PROFIL (FAKULTAS, PRODI, UNIT / BIRO, JABATAN) */
    const orgDataEl = document.getElementById('sipaduProfileOrgData');
    let orgData = { faculties: [], studyPrograms: {}, employeeUnits: [], positions: [] };
    if (orgDataEl && orgDataEl.textContent) {
        try {
            orgData = Object.assign(orgData, JSON.parse(orgDataEl.textContent));
        } catch (err) {
            // abaikan jika parse gagal
        }
    }

    const glassSelectControllers = new Map();

    function closeAllGlassSelects(exceptKey) {
        glassSelectControllers.forEach(function (ctrl, key) {
            if (exceptKey && key === exceptKey) return;
            ctrl.close();
        });
    }

    function createGlassSelect(wrapper, optionsConfig) {
        if (!wrapper) return null;
        const key = wrapper.dataset.stSelect;
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const trigger = wrapper.querySelector('.st-glass-select-trigger');
        const labelEl = wrapper.querySelector('[data-st-select-label]');
        const menuEl = wrapper.querySelector('[data-st-select-menu]');

        let currentOptions = [];
        let placeholderText = optionsConfig.placeholder || 'Pilih opsi';
        let emptyText = optionsConfig.emptyText || 'Tidak ada pilihan tersedia';
        let isEditing = !wrapper.classList.contains('is-readonly');
        let onChangeCallback = optionsConfig.onChange || null;

        function setOpen(open) {
            if (open && !isEditing) return;
            wrapper.classList.toggle('is-open', open);
            if (trigger) {
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
            }
            if (menuEl) {
                menuEl.hidden = !open;
            }
        }

        function syncTriggerDisplay() {
            const val = hiddenInput ? hiddenInput.value : '';
            if (!labelEl) return;
            if (val && String(val).trim() !== '') {
                labelEl.textContent = String(val);
                labelEl.classList.remove('is-placeholder');
            } else {
                labelEl.textContent = placeholderText;
                labelEl.classList.add('is-placeholder');
            }
        }

        function renderOptions() {
            if (!menuEl) return;
            const selectedVal = hiddenInput ? String(hiddenInput.value || '') : '';
            menuEl.innerHTML = '';

            if (!Array.isArray(currentOptions) || currentOptions.length === 0) {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'st-glass-select-empty';
                emptyDiv.textContent = emptyText;
                menuEl.appendChild(emptyDiv);
                return;
            }

            currentOptions.forEach(function (optText) {
                const optionValue = String(optText);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'st-glass-select-option' + (optionValue === selectedVal ? ' is-selected' : '');
                btn.setAttribute('role', 'option');
                btn.setAttribute('aria-selected', optionValue === selectedVal ? 'true' : 'false');

                const textSpan = document.createElement('span');
                textSpan.textContent = optionValue;

                const checkSpan = document.createElement('span');
                checkSpan.className = 'st-glass-select-check';
                checkSpan.innerHTML = '<i data-lucide="check"></i>';

                btn.appendChild(textSpan);
                btn.appendChild(checkSpan);

                btn.addEventListener('click', function () {
                    setValue(optionValue, true);
                    setOpen(false);
                });

                menuEl.appendChild(btn);
            });

            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        function setValue(nextVal, notify) {
            if (hiddenInput) {
                hiddenInput.value = nextVal || '';
            }
            syncTriggerDisplay();
            renderOptions();
            if (notify && typeof onChangeCallback === 'function') {
                onChangeCallback(hiddenInput ? hiddenInput.value : '');
            }
        }

        function setOptions(nextOptions, nextPlaceholder, nextEmptyText) {
            const list = Array.isArray(nextOptions) ? nextOptions.slice() : [];
            const currentVal = hiddenInput ? String(hiddenInput.value || '') : '';
            if (currentVal && !list.includes(currentVal)) {
                list.unshift(currentVal);
            }
            currentOptions = list;
            if (typeof nextPlaceholder === 'string') {
                placeholderText = nextPlaceholder;
            }
            if (typeof nextEmptyText === 'string') {
                emptyText = nextEmptyText;
            }
            syncTriggerDisplay();
            renderOptions();
        }

        function setEditable(editing) {
            isEditing = Boolean(editing);
            wrapper.classList.toggle('is-readonly', !isEditing);
            if (!isEditing) {
                setOpen(false);
            }
        }

        if (trigger) {
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                if (!isEditing) return;
                const willOpen = !wrapper.classList.contains('is-open');
                closeAllGlassSelects(key);
                setOpen(willOpen);
            });
        }

        const controller = {
            setValue: setValue,
            setOptions: setOptions,
            setEditable: setEditable,
            close: function () { setOpen(false); },
            getValue: function () { return hiddenInput ? hiddenInput.value : ''; }
        };

        glassSelectControllers.set(key, controller);
        setOptions(optionsConfig.options || [], placeholderText, emptyText);
        syncTriggerDisplay();
        return controller;
    }

    const facultySelect = createGlassSelect(
        document.querySelector('[data-st-select="faculty"]'),
        {
            options: orgData.faculties || [],
            placeholder: 'Pilih fakultas',
            onChange: function (selectedFac) {
                const prodiCtrl = glassSelectControllers.get('study_program');
                if (!prodiCtrl) return;
                const mapObj = orgData.studyPrograms || {};
                const list = selectedFac && Array.isArray(mapObj[selectedFac]) ? mapObj[selectedFac] : [];
                const currentProdi = prodiCtrl.getValue();
                if (currentProdi && !list.includes(currentProdi)) {
                    prodiCtrl.setValue('', false);
                }
                prodiCtrl.setOptions(
                    list,
                    selectedFac ? 'Pilih program studi' : 'Pilih fakultas terlebih dahulu',
                    selectedFac ? 'Tidak ada program studi' : 'Pilih fakultas terlebih dahulu'
                );
            }
        }
    );

    const initialFacultyVal = facultySelect ? facultySelect.getValue() : '';
    const initialProdiMap = orgData.studyPrograms || {};
    const initialProdiList = initialFacultyVal && Array.isArray(initialProdiMap[initialFacultyVal])
        ? initialProdiMap[initialFacultyVal]
        : [];

    createGlassSelect(
        document.querySelector('[data-st-select="study_program"]'),
        {
            options: initialProdiList,
            placeholder: initialFacultyVal ? 'Pilih program studi' : 'Pilih fakultas terlebih dahulu',
            emptyText: initialFacultyVal ? 'Tidak ada program studi' : 'Pilih fakultas terlebih dahulu'
        }
    );

    createGlassSelect(
        document.querySelector('[data-st-select="employee_unit"]'),
        {
            options: orgData.employeeUnits || [],
            placeholder: 'Pilih unit atau biro kerja'
        }
    );

    createGlassSelect(
        document.querySelector('[data-st-select="position"]'),
        {
            options: orgData.positions || [],
            placeholder: 'Pilih jabatan'
        }
    );

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.st-glass-select')) {
            closeAllGlassSelects(null);
        }
    });

    /* MODE EDIT PER KARTU & FOTO PROFIL */
    const profileForm = document.getElementById('profileSettingsForm');
    const photoInput = document.getElementById('profilePhotoInput');
    const removePhotoInput = document.getElementById('removeProfilePhotoInput');
    const avatarBox = document.getElementById('settingsAvatarBox');
    const avatarImg = document.getElementById('settingsAvatarImg');
    const avatarInitial = document.getElementById('settingsAvatarInitial');
    const avatarCamBtn = document.getElementById('settingsAvatarCamBtn');
    const photoEditor = document.getElementById('profilePhotoEditor');
    const viewPhotoBtn = document.getElementById('viewProfilePhotoBtn');
    const removePhotoBtn = document.getElementById('removeProfilePhotoBtn');
    const pickPhotoLabel = document.getElementById('pickPhotoBtnLabel');
    const photoHint = document.getElementById('profilePhotoHint');
    const photoClientError = document.getElementById('profilePhotoClientError');
    const photoModal = document.getElementById('settingsPhotoModal');
    const modalPhotoImg = document.getElementById('modalSettingsPhotoImg');

    const userDisplayName = avatarBox ? (avatarBox.dataset.userName || '') : '';
    const savedPhotoUrl = avatarBox && avatarBox.dataset.savedPhoto ? avatarBox.dataset.savedPhoto : null;
    let currentPhotoUrl = savedPhotoUrl;

    function updatePhotoPreview(src) {
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
            avatarBox.title = hasPhoto ? 'Klik untuk melihat foto profil' : userDisplayName;
        }
        if (viewPhotoBtn) {
            viewPhotoBtn.hidden = !hasPhoto;
        }
        if (removePhotoBtn) {
            removePhotoBtn.hidden = !hasPhoto;
        }
        if (pickPhotoLabel) {
            pickPhotoLabel.textContent = hasPhoto ? 'Ubah Foto' : 'Unggah Foto';
        }
    }

    function openPhotoModal() {
        if (!currentPhotoUrl || !photoModal) return;
        photoModal.classList.add('is-open');
    }

    function closePhotoModal() {
        if (!photoModal) return;
        photoModal.classList.remove('is-open');
    }

    if (profileForm) {
        const cards = Array.from(profileForm.querySelectorAll('[data-profile-card]'));
        const editableInputs = Array.from(profileForm.querySelectorAll('input:not([type="hidden"]):not([type="file"]):not([disabled])'));
        const originalValues = new Map(editableInputs.map(function (inp) { return [inp, inp.value]; }));

        const selectHiddenInputs = Array.from(profileForm.querySelectorAll('[data-st-select] input[type="hidden"]'));
        const originalSelectValues = new Map(selectHiddenInputs.map(function (inp) { return [inp.id, inp.value]; }));

        function setCardEditState(cardKey, editing, focusFirst) {
            const card = profileForm.querySelector('[data-profile-card="' + cardKey + '"]');
            if (!card) return;

            card.classList.toggle('is-card-editing', editing);

            const editBtn = card.querySelector('[data-enable-card-edit]');
            if (editBtn) {
                editBtn.classList.toggle('is-editing', editing);
            }

            const actionBar = card.querySelector('[data-card-actions]');
            if (actionBar) {
                actionBar.classList.toggle('is-hidden', !editing);
            }

            if (cardKey === 'identity') {
                if (photoEditor) {
                    photoEditor.hidden = !editing;
                }
                if (avatarCamBtn) {
                    avatarCamBtn.hidden = !editing;
                }
            }

            const cardInputs = Array.from(card.querySelectorAll('input:not([type="hidden"]):not([type="file"]):not([disabled])'));
            cardInputs.forEach(function (inp) {
                if (editing) {
                    inp.removeAttribute('readonly');
                } else {
                    inp.setAttribute('readonly', 'readonly');
                }
            });

            const cardSelects = Array.from(card.querySelectorAll('[data-st-select]'));
            cardSelects.forEach(function (selWrapper) {
                const selKey = selWrapper.dataset.stSelect;
                const ctrl = glassSelectControllers.get(selKey);
                if (ctrl) {
                    ctrl.setEditable(editing);
                }
            });

            if (!editing) {
                closeAllGlassSelects(null);
            } else if (focusFirst && cardInputs.length > 0) {
                cardInputs[0].focus();
            }
        }

        function resetCardValues(cardKey) {
            const card = profileForm.querySelector('[data-profile-card="' + cardKey + '"]');
            if (!card) return;

            card.querySelectorAll('input:not([type="hidden"]):not([type="file"]):not([disabled])').forEach(function (inp) {
                if (originalValues.has(inp)) {
                    inp.value = originalValues.get(inp);
                }
            });

            if (cardKey === 'identity') {
                if (photoInput) photoInput.value = '';
                if (removePhotoInput) removePhotoInput.value = '0';
                if (photoClientError) {
                    photoClientError.hidden = true;
                    photoClientError.textContent = '';
                }
                if (photoHint) {
                    photoHint.textContent = 'Format JPG, PNG, atau WEBP (maks. 2 MB).';
                }
                updatePhotoPreview(savedPhotoUrl);
            }

            if (cardKey === 'organization') {
                const origFac = originalSelectValues.get('profileFaculty') || '';
                const origProdi = originalSelectValues.get('profileStudyProgram') || '';
                const origUnit = originalSelectValues.get('profileEmployeeUnit') || '';
                const origPos = originalSelectValues.get('profilePosition') || '';

                if (glassSelectControllers.has('faculty')) {
                    glassSelectControllers.get('faculty').setValue(origFac, false);
                }
                if (glassSelectControllers.has('study_program')) {
                    const mapObj = orgData.studyPrograms || {};
                    const prodiList = origFac && Array.isArray(mapObj[origFac]) ? mapObj[origFac] : [];
                    glassSelectControllers.get('study_program').setOptions(
                        prodiList,
                        origFac ? 'Pilih program studi' : 'Pilih fakultas terlebih dahulu',
                        origFac ? 'Tidak ada program studi' : 'Pilih fakultas terlebih dahulu'
                    );
                    glassSelectControllers.get('study_program').setValue(origProdi, false);
                }
                if (glassSelectControllers.has('employee_unit')) {
                    glassSelectControllers.get('employee_unit').setValue(origUnit, false);
                }
                if (glassSelectControllers.has('position')) {
                    glassSelectControllers.get('position').setValue(origPos, false);
                }
            }
        }

        cards.forEach(function (card) {
            const cardKey = card.dataset.profileCard;
            const editBtn = card.querySelector('[data-enable-card-edit]');
            const cancelBtn = card.querySelector('[data-cancel-card]');

            if (editBtn) {
                editBtn.addEventListener('click', function () {
                    const isCurrentlyEditing = card.classList.contains('is-card-editing');
                    if (!isCurrentlyEditing) {
                        setCardEditState(cardKey, true, true);
                    } else {
                        const firstInput = card.querySelector('input:not([type="hidden"]):not([type="file"]):not([disabled])');
                        if (firstInput) {
                            firstInput.focus();
                        } else {
                            const firstTrigger = card.querySelector('.st-glass-select-trigger');
                            if (firstTrigger) firstTrigger.focus();
                        }
                    }
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function () {
                    resetCardValues(cardKey);
                    setCardEditState(cardKey, false, false);
                });
            }
        });

        document.querySelectorAll('[data-pick-photo]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                closePhotoModal();
                setCardEditState('identity', true, false);
                if (photoInput) photoInput.click();
            });
        });

        if (viewPhotoBtn) {
            viewPhotoBtn.addEventListener('click', openPhotoModal);
        }

        if (avatarBox) {
            avatarBox.addEventListener('click', function () {
                if (currentPhotoUrl) openPhotoModal();
            });
        }

        if (photoInput) {
            photoInput.addEventListener('change', function () {
                if (photoClientError) {
                    photoClientError.hidden = true;
                    photoClientError.textContent = '';
                }

                const file = photoInput.files && photoInput.files[0];
                if (!file) return;

                const allowed = ['image/jpeg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    photoInput.value = '';
                    if (photoClientError) {
                        photoClientError.textContent = 'Format foto harus JPG, PNG, atau WEBP.';
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

                if (removePhotoInput) removePhotoInput.value = '0';

                const reader = new FileReader();
                reader.onload = function (ev) {
                    updatePhotoPreview(ev.target.result);
                    setCardEditState('identity', true, false);
                    if (photoHint) {
                        photoHint.textContent = 'Foto dipilih: ' + file.name + ' (klik Simpan Perubahan untuk menyimpan).';
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        if (removePhotoBtn) {
            removePhotoBtn.addEventListener('click', function () {
                if (photoInput) photoInput.value = '';
                if (removePhotoInput) removePhotoInput.value = '1';
                updatePhotoPreview(null);
                setCardEditState('identity', true, false);
                if (photoHint) {
                    photoHint.textContent = 'Foto profil akan dihapus setelah klik Simpan Perubahan.';
                }
            });
        }
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
            closePhotoModal();
            closeAllGlassSelects(null);
        }
    });

    /* TOGGLE LIHAT PASSWORD */
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = document.getElementById(btn.dataset.togglePassword);
            if (!target) return;
            const isHidden = target.type === 'password';
            target.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden ? '<i data-lucide="eye-off"></i>' : '<i data-lucide="eye"></i>';
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