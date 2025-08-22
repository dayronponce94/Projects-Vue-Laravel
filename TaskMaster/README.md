# TaskMaster - Gestor de Tareas / Task Master

Aplicación para gestión de tareas diarias.

Application for daily task management.

## Tecnologías / Technologies

- Vue 3.5.17
- Vite 7.0.0 (frontend) / 6.2.4 (backend)
- Axios 1.10.0 (frontend) / 1.8.2 (backend)
- Bootstrap 5.3.0 + Bootstrap Icons 1.13.1
- Laravel Framework 12.19.3
- SQLite
- JWT token
- Pinia 3.0.3

## Instalación / Installation

### Requisitos previos / Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- SQLite

### Pasos de instalación / Installation steps

```bash
# Clonar el repositorio (si no lo tienes) / Clone the repository (if you don't have one)
git clone https://github.com/dayronponce94/Projects-Vue-Laravel.git
cd Projects-Vue-Laravel/TaskMaster

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
