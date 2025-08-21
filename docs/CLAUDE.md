# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Backend (Laravel)
- **Start development server**: `composer run dev` (runs server, queue, logs, and vite concurrently)
- **Run individual services**:
  - Server: `php artisan serve`
  - Queue worker: `php artisan queue:listen --tries=1`
  - Logs: `php artisan pail --timeout=0`
- **Database operations**:
  - Migrations: `php artisan migrate`
  - Seeders: `php artisan db:seed`
  - Fresh database: `php artisan migrate:fresh --seed`
- **Code quality**:
  - Tests: `composer run test` (clears config and runs PHPUnit)
  - IDE Helper: `php artisan ide-helper:generate`
  - Laravel Pint (code formatting): `vendor/bin/pint`

### Frontend (Vite + TailwindCSS)
- **Development**: `npm run dev`
- **Production build**: `npm run build`
- **Install dependencies**: `npm install`

## Architecture Overview

### Core Application Structure
This is a **Laravel-based employee management system (Kepegawaian UNMUL)** with role-based access control using Spatie permissions. The application manages employee proposals (usulan) for university staff.

### Key Models and Database Structure
- **Pegawai**: Main employee model with authentication (`app/Models/BackendUnivUsulan/Pegawai.php`)
  - Uses `pegawai` guard for authentication
  - Implements Spatie roles and permissions
  - Contains employee data, positions, ranks, and educational information

- **Core entities**:
  - `UnitKerja`, `SubUnitKerja`, `SubSubUnitKerja`: Organizational hierarchy
  - `Jabatan`: Positions with hierarchy levels
  - `Pangkat`: Ranks with hierarchy levels and status
  - `PeriodeUsulan`: Proposal periods with date ranges and types
  - `Usulan`: Employee proposals with validation workflow
  - `UsulanDokumen`: Document attachments for proposals

### Role-Based Access Control
The application uses multiple user roles with distinct dashboards:
- **Admin Universitas**: University-level administration
- **Admin Universitas Usulan**: Proposal administration
- **Admin Fakultas**: Faculty administration  
- **Admin Keuangan**: Finance administration
- **Pegawai Unmul**: Regular employees (proposal submission)
- **Penilai Universitas**: University evaluators
- **Tim Senat**: Senate team

### Route Organization
- **Frontend routes**: Public pages (`routes/frontend.php`)
- **Backend routes**: Protected admin/employee areas (`routes/backend.php`)
- All backend routes require `auth:pegawai` middleware
- Role-based route groups with specific middleware

### Controller Structure
Controllers are organized by role in `app/Http/Controllers/Backend/`:
- `AdminUniversitas/`: University admin controllers
- `AdminUnivUsulan/`: Proposal admin controllers
- `AdminFakultas/`: Faculty admin controllers
- `AdminKeuangan/`: Finance admin controllers
- `PegawaiUnmul/`: Employee controllers (various proposal types)
- `PenilaiUniversitas/`: Evaluator controllers
- `TimSenat/`: Senate controllers

### Proposal (Usulan) System
The core workflow involves employees submitting various types of proposals:
- **Usulan types**: Position proposals, rank promotions, education certificates, etc.
- **Workflow**: Submission → Validation → Review → Approval/Rejection
- **Document management**: File uploads with validation and access logging
- **Audit trail**: Comprehensive logging system via `UsulanLog`

### Frontend Technology
- **TailwindCSS**: Utility-first CSS framework
- **Alpine.js**: Lightweight JavaScript framework for interactivity
- **Vite**: Build tool and development server
- **Blade templates**: Laravel's templating engine

### File Storage
- **Private files**: `storage/app/private/` (employee documents, proposal documents)
- **Public files**: `storage/app/public/` (accessible documents)
- **Document access**: Controlled access with logging for sensitive files

### Performance Considerations
- Database indexes optimization (migration: `2025_08_15_033417_add_additional_performance_indexes.php`)
- Lazy loading relationships to prevent N+1 queries
- Queue system for background tasks (document processing, notifications)

### Testing
- PHPUnit test suite in `tests/` directory
- Feature tests for major functionality
- Unit tests for isolated components