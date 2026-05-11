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

Submit Leave Request (Employee)
curl -X POST http://127.0.0.1:8000/api/leave-requests \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"start_date":"2026-05-20","end_date":"2026-05-22","reason":"Liburan"}'

Admin Approve Request

curl -X PUT http://127.0.0.1:8000/api/admin/leave-requests/1/approve \
  -H "Authorization: Bearer ADMIN_TOKEN"
text

### 7. Commit perubahan README

```bash
git add README.md
git commit -m "Update README with API examples"
git push

