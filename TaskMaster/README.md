# TaskMaster - Gestor de Tareas / Task Master

Aplicación para gestión de tareas diarias.

Application for daily task management.

## Tecnologías / Tecnologies
- V ue3.5.17
- Laravel Framework 12.19.3
- Artisan
- sqlite3 
- Bootstrap

## Instalación / Installation
```bash
# Backend
cd api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

# Frontend
cd ui
npm install
npm run dev