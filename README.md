# Leave Management System API

Sistem manajemen cuti karyawan dengan Laravel 11 dan MySQL.

## Fitur Utama
- ✅ Authentication (Konvensional + OAuth Google/GitHub)
- ✅ Role Based Access Control (Admin & Employee)
- ✅ Manajemen Cuti (CRUD, Approve/Reject)
- ✅ Kuota Cuti 12 hari/tahun
- ✅ Upload attachment
- ✅ Filter & Pagination
- ✅ Reporting & Statistics

## Tech Stack
- Laravel 11
- MySQL 8.0
- Laravel Passport (OAuth)
- Spatie Permission
- Laravel Socialite

## Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/yourusername/leave-management-system.git
cd leave-management-system

## Repository
GitHub: https://github.com/YOUR_USERNAME/leave-management-system

## Testing dengan cURL

### Login Admin
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password123"}'
