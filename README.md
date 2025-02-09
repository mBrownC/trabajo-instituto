# Zapatos3000 - Sistema de Gestión de Tareas

## Descripción del Proyecto

Zapatos3000 es una aplicación web de gestión de tareas diseñada para empresas que buscan optimizar la productividad y el seguimiento de actividades diarias. Desarrollada con tecnologías modernas de backend y frontend, permite a los usuarios crear, administrar y dar seguimiento a sus tareas de manera eficiente.

## Características Principales

- Registro y autenticación de usuarios
- Creación, edición y eliminación de tareas
- Recuperación de contraseña
- Gestión de roles de usuario
- Sistema de tokens seguros

## Estructura de Carpetas

```
proyecto_zapatos3000/
│
├── config/
│   ├── config.php
│   └── database.php
│
├── database/
│   └── schema.sql
│
├── frontend/
│   ├── css/
│   │   └── styles.css
│   ├── js/
│   │   ├── app.js
│   │   ├── auth.js
│   │   ├── config.js
│   │   ├── tareas.js
│   │   └── recuperar-contrasena.html
│   └── index.html
│
├── public/
│   ├── dashboard.php
│   ├── index.php
│   ├── login.php
│   └── registro.php
│
├── src/
│   ├── controllers/
│   │   ├── TareaController.php
│   │   └── UsuarioController.php
│   ├── models/
│   │   ├── Tarea.php
│   │   └── Usuario.php
│   ├── services/
│   │   ├── AuthService.php
│   │   ├── DatabaseService.php
│   │   ├── EmailService.php
│   │   └── RecuperacionService.php
│   └── utils/
│       ├── Middleware.php
│       ├── Sesion.php
│       └── Validador.php
│
├── vendor/
│   └── (dependencias de Composer)
│
├── .env
├── .env.example
├── .gitignore
├── README.md
└── composer.json
```

## Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Composer
- Servidor web (Apache, Nginx)

## Instalación

1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/proyecto_zapatos3000.git
```

2. Instalar dependencias
```bash
composer install
```

3. Configurar la base de datos
- Importar `database/schema.sql`
- Copiar `.env.example` a `.env`
- Configurar credenciales de base de datos

4. Configurar servidor web
- Apuntar directorio raíz a `/public`

## Configuración

1. Editar `.env` con tus credenciales
2. Configurar parámetros en `config/config.php`

## Ejecución

- Iniciar servidor web
- Acceder a `http://localhost/proyecto_zapatos3000/frontend`

## Credenciales Iniciales

- Email: `admin@zapatos3000.com`
- Contraseña: `Zapatos3000Admin2024!`

## Tecnologías Utilizadas

- Backend: PHP
- Frontend: HTML, CSS, JavaScript
- Base de Datos: MySQL
- Autenticación: JWT
- Envío de Correos: PHPMailer


```