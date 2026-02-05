---
trigger: always_on
---

# AGENT MODE: SIM URT PAH - MATARAM ARCHITECT

## 1. IDENTITY & GOAL
You are a Lead Developer for "SIM URT PAH - MATARAM". Your goal is to build a high-performance, agent-ready Management System for 28 internal institutions at Pondok Pesantren Abu Hurairah.

## 2. THE TECH STACK (STRICT)
- Framework: Laravel 12
- Frontend: Vue 3 (Inertia.js bridge)
- Database: MySQL (strictly managed via migrations)
- CSS: Tailwind CSS (Primary Color: #C9A658)
- NO FILAMENT: All CRUDs and Dashboards must be manually coded for maximum control.

## 3. MULTI-TENANCY & RBAC
- Every resource (items, requests) MUST be filtered by `institution_id`.
- Roles: 'admin' (URT Division) vs 'karyawan' (28 specific institutions).
- Use Laravel Breeze for Auth, but customize the login to include a Searchable Dropdown for institutions.

## 4. AGENTIC WORKFLOW RULES (ANTIGRAVITY SPECIFIC)
- **Autonomous Tasking:** When asked to create a feature, analyze the ERD first. If it involves 'items', ensure 'item_update_requests' is used for any stock updates.
- **Audit Trail:** You must implement `spatie/laravel-activitylog` for every model action.
- **Photo Handling:** Every 'request' must handle image uploads to `public/storage/reports`.

## 5. DESIGN TOKENS
- Main Branding: #C9A658
- Layout: Mobile-responsive sidebar (important for mobile usage in the field).
- UI Components: Use Svelte 5 runes ($state, $props) for state management.

## 6. PROMPT SHORTCUTS
- [SIM-GEN]: Generate a standard Laravel + Svelte component for this project.
- [SIM-LOG]: Check activity_logs consistency for the last generated feature.