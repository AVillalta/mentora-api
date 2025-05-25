# Mentora API

## ¿Qué es Mentora API?

**Mentora API** es el backend de Mentora, un campus virtual para universidades. Es una API RESTful que permite a estudiantes, profesores y administradores gestionar cursos, materiales (PDFs, videos), tareas y calificaciones. Está hecha con **Laravel 11** y usa **Docker** para probarla fácilmente en local.

### ¿Qué hace?
- **Estudiantes**: Ver cursos, descargar materiales, enviar tareas, consultar notas.
- **Profesores**: Crear cursos, subir materiales, revisar tareas, poner notas.
- **Administradores**: Gestionar usuarios y configuraciones.
- Usa MySQL (base de datos), MinIO (almacenamiento), Redis (caché) y Mailhog (emails).

## Requisitos

- **Docker** y **Docker Compose** (instalados y funcionando).
- **Git** (para clonar el código).
- Terminal (Linux, macOS, o WSL en Windows).
- 4GB de RAM libres para Docker.

## Cómo probar la API en local

Sigue estos pasos para levantar la API con **Laravel Sail** (simplifica Docker).

### 1. Clona el repositorio

Abre una terminal y descarga el código:

```bash
git clone https://github.com/<tu-usuario>/mentora-api.git
cd mentora-api
```

### 2. Copia el archivo de configuración

Copia `.env.example` para crear `.env`:

```bash
cp .env.example .env
```

No necesitas cambiar nada en `.env`. Los valores por defecto funcionan (puerto `8000`, base de datos `mentora_db`).

### 3. Inicia los servicios

Levanta los contenedores (API, base de datos, almacenamiento, emails):

```bash
./vendor/bin/sail up -d
```

Esto activa:
- **API**: `http://localhost:8000` (para navegador o Postman).
- **Base de datos (MySQL)**: `localhost:3306`.
- **Almacenamiento (MinIO)**: `localhost:9000`.
- **Emails (Mailhog)**: `localhost:8025`.

**Puertos**: La API usa el puerto `80` dentro del contenedor, pero accedes en `http://localhost:8000` porque Docker mapea `8000:80`. No cambies `APP_URL` en `.env`.

### 4. Instala las dependencias

Instala las librerías de PHP:

```bash
sail composer install
```

### 5. Genera la clave de la aplicación

Crea una clave única para la API:

```bash
sail artisan key:generate
```

### 6. Configura la base de datos

Crea las tablas necesarias:

```bash
sail artisan migrate
```

### 7. Carga los usuarios de prueba

Ejecuta el seeder para crear usuarios:

```bash
sail artisan db:seed
```

Usuarios creados:
- **Profesor**:
  - Email: `professor@example.com`
  - Contraseña: `password1234`
- **Estudiante**:
  - Email: `student@example.com`
  - Contraseña: `password1234`
- **Admin**:
  - Email: `admin@example.com`
  - Contraseña: `password1234`

**Nota**: Los administradores pueden crear más usuarios desde el panel admin en el frontend (por ejemplo, `/admin/users`).

### 8. Configura el almacenamiento

Habilita el acceso a archivos públicos:

```bash
sail artisan storage:link
```

Esto permite que los archivos subidos (como materiales) sean accesibles. No necesitas configurar MinIO manualmente; el paquete Media Library lo hace por ti.

### 9. Prueba la API

Usa `curl` o Postman.

#### a. Inicia sesión
Prueba con el usuario profesor:

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"professor@example.com","password":"password1234"}'
```

Esto devuelve un token (por ejemplo, `1|abcdef...`). Cópialo.

Prueba también con `student@example.com` o `admin@example.com`.

#### b. Lista cursos
Usa el token:

```bash
curl -X GET http://localhost:8000/api/courses \
  -H "Authorization: Bearer <tu-token>"
```

#### c. Otros ejemplos
- Detalles de un curso: `GET http://localhost:8000/api/courses/<course_id>`
- Estudiantes de un curso: `GET http://localhost:8000/api/enrollments?course_id=<course_id>`
- Subir material (profesor): `POST http://localhost:8000/api/contents` (form-data).

### 10. Para la API

Detén los contenedores:

```bash
sail down
```

## Si algo falla

- **No carga `http://localhost:8000`**:
  - Verifica contenedores: `docker ps`.
  - Reinicia: `sail down && sail up -d`.
- **Error de base de datos**:
  - Confirma `.env`: `DB_HOST=mysql`, `DB_USERNAME=sail`, `DB_PASSWORD=password`.
  - Refresca: `sail artisan migrate:refresh`.
- **Archivos no se suben**:
  - Asegúrate de ejecutar `sail artisan storage:link`.
  - Verifica MinIO en `http://localhost:9001` (usuario: `minioadmin`, contraseña: `miniopassword`).
- **Error de CORS**:
  - Confirma `.env`: `SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8000`.

## Qué puedes probar

- **Profesor**: Sube un PDF (`POST /api/contents`), crea una tarea (`POST /api/assignments`), consulta notas (`GET /api/courses/<course_id>/grades`).
- **Estudiante**: Ve cursos (`GET /api/courses`), envía tareas (`POST /api/assignments/<id>/submit`).
- **Admin**: Gestiona usuarios desde el frontend (por ejemplo, `/admin/users`).

## Más info

- **Endpoints**: Mira `routes/api.php`.
- **Archivos**: Guardados en MinIO (`http://localhost:9001`).
- **Emails**: En Mailhog (`http://localhost:8025`).

## Contacto

Si algo falla, escribe a [tu-email@example.com](mailto:tu-email@example.com).

## Licencia

MIT License. Ver [LICENSE](LICENSE).