# ClinicApp - Sistema de Citas Médicas / Medical Appointment System

Aplicación para gestión de citas médicas con reservas en tiempo real.

Application for medical appointment management with real-time bookings.

## Tecnologías / Technologies

- Vue 3.5.17,
- Vite 7.0.4,
- Axios 1.8.2,
- Tailwind CSS 4.0.0,
- Laravel Framework 12.0,
- SQLite,
- JWT tokens
- Pinia 3.0.3

## Instalación / Installation

### Requisitos previos

- PHP 8.1+
- Composer
- Node.js 16+
- SQLite

### Pasos de instalación

```bash
# Clonar el repositorio (si no lo tienes)
git clone https://github.com/dayronponce94/Projects-Vue-Laravel.git
cd Projects-Vue-Laravel/ClinicApp

# Backend
cd api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

# Frontend (en otra terminal)
cd ../ui
npm install
npm run dev
```
