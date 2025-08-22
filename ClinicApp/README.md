# ClinicApp - Sistema de Citas Médicas / Medical Appointment System

Aplicación web completa para la gestión de turnos médicos online

Complete web application for online medical appointment management

## Tecnologías / Technologies

- Vue 3.5.17
- Vite 7.0.4
- Axios 1.11.0 (frontend) / 1.8.2 (backend)
- Tailwind CSS 4.0.0
- Laravel Framework 12.0
- SQLite,
- JWT tokens
- Pinia 3.0.3

## Instalación / Installation

### Requisitos previos

- PHP 8.2+
- Composer
- Node.js 18+
- SQLite

### Pasos de instalación

```bash
# Clonar el repositorio (si no lo tienes) / Clone the repository (if you don't have one)
git clone https://github.com/dayronponce94/Projects-Vue-Laravel.git
cd Projects-Vue-Laravel/ClinicApp

# Backend
cd api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

# Frontend 
cd ../ui
npm install
npm run dev
```
