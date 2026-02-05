# SIM PAH Implementation Plan

## Overview

This document outlines the implementation plan for the Sistem Informasi Manajemen URT PAH.
Status: Core Setup Completed.

## Technology Stack

- **Framework**: Laravel 12
- **Frontend**: Vue.js 3 + Inertia.js (Breeze)
- **CSS**: Tailwind CSS + Custom Colors
- **Database**: MySQL 8.0+

## Progress

- [x] **Project Initialization**: Installed Laravel 12.
- [x] **Inertia + Vue Setup**: Installed Laravel Breeze with Vue stack.
- [x] **Database Configuration**: Configured `.env` for MySQL.
- [x] **Dependency Fixes**: Resolved Vite 6 / Tailwind compatibility issues.
- [x] **Database Architecture**:
    - [x] Created `institutions` table.
    - [x] Updated `users` table (added `institution_id`, `role`, `avatar`, `phone`).
    - [x] Created `items` table.
    - [x] Created `item_update_requests` table.
    - [x] Created `requests` table.
    - [x] Created `activity_logs` tables.
- [x] **Models**: Created `Institution`, `Item`, `ItemUpdateRequest`, `Request` models with relationships.
- [x] **Seeding**: Seeded initial institutions and Admin/Karyawan users.
- [x] **Branding**: Configured Tailwind with Gold (#C9A658) and Ivory (#F9F9F9).

## Next Steps

### Phase 1: Authentication & Role Management

- [x] Implement `RoleMiddleware` to separate Admin and Karyawan access.
- [x] Customize Login page with Dropdown Institution (Implicitly handled via User assignment).
- [x] Ensure `HandleInertiaRequests` shares `auth.user.role` and `auth.user.institution`.

### Phase 2: Dashboard Admin (Divisi URT)

- [x] Create `Admin/Dashboard` page (Overview stats).
- [x] Implement CRUD for `Institutions` (Controller + UI).
- [x] Implement User Management (Create/Edit users per institution).
- [x] Implement Item Approval (List `item_update_requests` and approve/reject).
- [x] Implement Request Management (List `requests` and status workflow).

### Phase 3: Dashboard Karyawan (Per Divisi)

- [x] Create `Karyawan/Dashboard` page.
- [x] Implement Item Management (Read-only list, "Update Stock" button -> creates request).
- [x] Implement Request Submission (Form for 'Utilitas', 'B7', 'Darurat').

### Phase 4: Reporting & Logs

- [x] Implement Activity Log (Spatie LogsActivity implemented on all models).
- [ ] Implement Excel Export for Requests (Pending future request).

## Credentials (Local)

- **Admin**: `admin@sim-pah.com` / `password`
- **Karyawan SD**: `sd@sim-pah.com` / `password`
