# PRO413_2_2026_HITO3_error418

**Sistema de Gestión Escolar con PHP, Laravel, Docker y doble conexión MySQL**

Evaluación procesual - Hito 3 | Programación III | Ingeniería de Sistemas | Grupo `error418`

---

## 1. Descripción del problema

Una institución educativa necesita un sistema web para administrar su información académica y dar seguimiento a estudiantes, inscripciones y resultados.

El reto principal es la **distribución de los datos en dos servidores MySQL independientes**, sin duplicar tablas entre ellos:

| Servidor | Responsabilidad | Tablas |
|---|---|---|
| **MySQL Docker** (contenedor del grupo) | Información académica transaccional | `estudiantes`, `asignaturas`, `inscripciones`, `calificaciones` |
| **MySQL institucional** (red local, del docente) | Datos maestros de la institución | `docentes`, `aulas`, `gestion_academica`, `asignacion_docentes` |

**Solución implementada:** una aplicación Laravel que mantiene dos conexiones configuradas de forma independiente (`mysql` e `institucional`) y opera con lectura y escritura reales en ambas. Como MySQL no permite llaves foráneas entre servidores, las relaciones entre ellos (por ejemplo `asignacion_docentes.asignatura_id`, que apunta a `asignaturas` en el servidor Docker) se **resuelven desde la aplicación**, en una capa de servicio, con validaciones que preservan la integridad de los datos.

## 2. Descripción funcional

- **Gestión en la BD Docker:** registrar, consultar, modificar y eliminar Estudiantes, Asignaturas, Inscripciones y Calificaciones.
- **Gestión en la BD institucional:** registrar y consultar Docentes, Aulas, Gestión académica y Asignación de docentes.
- **Reporte cruzado (`/reporte`):** combina datos de ambos servidores (asignaturas del servidor Docker con docentes y aulas del servidor institucional).
- **Validaciones:** en las solicitudes (Form Requests) y en la persistencia (restricciones `UNIQUE`, `FOREIGN KEY` y `CHECK` en SQL).
- **Manejo de errores:** las excepciones se registran en el log y el usuario recibe mensajes comprensibles, sin información sensible.
- **Tipado estricto:** `declare(strict_types=1);` en los archivos PHP propios.

### Rutas principales

| Ruta | Descripción |
|---|---|
| `/` | Redirige a `/estudiantes` |
| `/estudiantes` | CRUD de estudiantes (BD Docker) |
| `/reporte` | Reporte JSON que cruza ambas bases de datos |

## 3. Integrantes y responsabilidades

| Integrante | Rol o responsabilidades | Usuario de GitHub |
|---|---|---|
| Aranda Silva Alan Cristhofer | Backend Laravel | [@alanhack777](https://github.com/alanhack777) |
| Quispe Mamani Rosmery | Funcionalidad web | [@rosmeryqm2020-afk](https://github.com/rosmeryqm2020-afk) |
| Copa Pérez Neyda Milena | Bases de datos e integración | [@Milenacopa120-0409](https://github.com/Milenacopa120-0409) |
| Canchilla Mamani Fabricio Isaac | Docker, despliegue y documentación | [@FabricioCanchilla](https://github.com/FabricioCanchilla) |

## 4. Arquitectura

```
                     Red local del aula
 ┌──────────────────────────────────────────────────────┐
 │  PC del grupo (Docker)                               │
 │                                                      │
 │  ┌────────────────────┐      ┌────────────────────┐  │
 │  │ gestion_escolar_app│      │ gestion_escolar_db │  │
 │  │ Laravel / PHP 8.4  │─────▶│ MySQL 8.4          │  │
 │  │ imagen:            │ 3306 │ BD: escolar_docker │  │
 │  │ gestion-escolar:   │      │ volumen: db_data   │  │
 │  │ error418           │      └────────────────────┘  │
 │  │ puerto 8000        │                              │
 │  └─────────┬──────────┘                              │
 └────────────┼─────────────────────────────────────────┘
              │ conexión "institucional" (3306)
              ▼
   ┌──────────────────────────┐
   │ Servidor MySQL del       │
   │ docente (red local)      │
   │ BD: pro413_418           │
   └──────────────────────────┘
```

1. **Contenedor Laravel (`gestion_escolar_app`):** imagen propia `gestion-escolar:error418` basada en `php:8.4-cli`. Sirve la aplicación en el puerto `8000`, escuchando en `0.0.0.0` para que sea accesible desde otros equipos de la red.
2. **Contenedor MySQL (`gestion_escolar_db`):** `mysql:8.4` en un contenedor **independiente** del de Laravel. Persiste sus datos en el volumen `db_data` y carga `database/sql/docker_db.sql` la primera vez que se crea.
3. **Servidor MySQL institucional:** servidor externo proporcionado por el docente, al que Laravel se conecta mediante la conexión `institucional`, definida en `config/database.php`.

### Estructura relevante del código

```
app/
├── Http/
│   ├── Controllers/EstudianteController.php
│   └── Requests/EstudianteRequest.php        # validaciones
├── Models/
│   ├── Estudiante.php, Asignatura.php        # conexión mysql (Docker)
│   └── Docente.php, Aula.php,
│       AsignacionDocente.php                 # conexión institucional
└── Services/
    ├── ReporteAcademicoInterface.php         # abstracción
    └── ReporteAcademicoService.php           # cruza ambas BD
config/database.php                           # conexiones mysql e institucional
database/sql/
├── docker_db.sql                             # tablas de la BD Docker
└── institucional_db.sql                      # tablas del servidor institucional
Dockerfile
docker-compose.yml
.env.example
```

## 5. Requisitos previos

| Herramienta | Versión recomendada |
|---|---|
| Docker (Docker Desktop en Windows, Docker Engine en Linux) | 24 o superior |
| Docker Compose | v2 (comando `docker compose`) |
| Git | 2.30 o superior |
| Acceso a la red local del aula | Necesario para el servidor institucional |

PHP y Composer **no hacen falta** en el equipo anfitrión: se instalan dentro de la imagen (PHP 8.4, Composer 2, extensiones `pdo_mysql` y `zip`; Laravel 13).

## 6. Clonación

```bash
git clone https://github.com/FabricioCanchilla/PRO413_2_2026_HITO3_error418.git
cd PRO413_2_2026_HITO3_error418
```

## 7. Configuración (variables de entorno)

Las credenciales **nunca** se incluyen en el código, el Dockerfile, los archivos SQL ni el historial de Git. Todo se configura con un archivo `.env` local, que está excluido por `.gitignore` y `.dockerignore`. El repositorio solo trae `.env.example`, con valores ficticios.

### Paso 1: crear el `.env`

**Windows (CMD):**
```bat
copy .env.example .env
notepad .env
```

**Windows (PowerShell):**
```powershell
Copy-Item .env.example .env
notepad .env
```

**GNU/Linux o macOS:**
```bash
cp .env.example .env
nano .env
```

### Paso 2: completar los valores

| Variable | Descripción |
|---|---|
| `DB_CONNECTION` | Conexión principal (`mysql`) |
| `DB_HOST` | Nombre del servicio MySQL en Docker (`db_docker`). No cambiar |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | Base de datos Docker (`escolar_docker`) |
| `DB_USERNAME` / `DB_PASSWORD` | Usuario y contraseña de la BD Docker. **Los elige cada persona** |
| `MYSQL_ROOT_PASSWORD` | Contraseña root del MySQL de Docker. **La elige cada persona** |
| `DB2_HOST` | IP del servidor MySQL del docente |
| `DB2_PORT` | Puerto del servidor del docente (normalmente `3306`) |
| `DB2_DATABASE` | Base de datos asignada al grupo (`pro413_418`) |
| `DB2_USERNAME` / `DB2_PASSWORD` | Credenciales proporcionadas por el docente |
| `SESSION_DRIVER` | `file`. Evita depender de una tabla `sessions` |
| `CACHE_STORE` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `APP_KEY` | Se genera en el paso 3 |

> **Importante:** define `DB_USERNAME`, `DB_PASSWORD` y `MYSQL_ROOT_PASSWORD` **antes** de levantar el contenedor MySQL por primera vez. MySQL crea el usuario solo al inicializar el volumen. Si luego cambias esos valores, hay que recrear el volumen con `docker compose down -v` (ver sección 13).

### Paso 3: generar la `APP_KEY`

**Windows y GNU/Linux:**
```bash
docker compose run --rm --no-deps app php artisan key:generate --show
```

Copia el resultado (`base64:...`) en la variable `APP_KEY` del `.env`.

### Cómo se usan las variables

El `docker-compose.yml` carga el `.env` con `env_file` en el contenedor de Laravel, y usa las variables `DB_*` y `MYSQL_*` para crear el usuario y la base del contenedor MySQL. En `config/database.php`, la conexión `mysql` lee `DB_*` y la conexión `institucional` lee `DB2_*`, sin ningún valor sensible escrito en el código.

> Las variables se leen **al crear** el contenedor. Después de modificar el `.env`, ejecuta `docker compose up -d --force-recreate app`.

## 8. Construcción de la imagen

La imagen se llama exactamente `gestion-escolar:error418`.

Con Docker Compose (recomendado, usa el nombre definido en `docker-compose.yml`):
```bash
docker compose build
```

O directamente con Docker:
```bash
docker build -t gestion-escolar:error418 .
```

Verificación:
```bash
docker images gestion-escolar
```

## 9. Descarga y ejecución

La imagen **no está publicada** en Docker Hub, por lo que se construye localmente desde el repositorio (sección 8).

Crear y ejecutar los contenedores (aplicación y MySQL):
```bash
docker compose up -d
```

Verificar que ambos estén activos:
```bash
docker compose ps
```

Acceder al servicio web:

- Desde el mismo equipo: <http://localhost:8000>
- Desde otro equipo de la red local: `http://<IP_DEL_EQUIPO>:8000`

Para conocer la IP del equipo:

| Sistema | Comando |
|---|---|
| Windows | `ipconfig` (usar la dirección IPv4) |
| GNU/Linux | `ip a` o `hostname -I` |

Si otro equipo no logra conectarse, probablemente el firewall bloquea el puerto 8000:

**Windows** (CMD como administrador):
```bat
netsh advfirewall firewall add rule name="Laravel 8000" dir=in action=allow protocol=TCP localport=8000
```

**GNU/Linux (ufw):**
```bash
sudo ufw allow 8000/tcp
```

Comandos útiles:
```bash
docker compose logs app          # ver logs de la aplicación
docker compose logs db_docker    # ver logs de MySQL
docker compose down              # detener y eliminar contenedores (conserva datos)
docker compose down -v           # además elimina el volumen de datos
```

## 10. Base de datos Docker

El contenedor MySQL (`gestion_escolar_db`) se ejecuta de forma independiente al de Laravel. El archivo `database/sql/docker_db.sql` se monta en `/docker-entrypoint-initdb.d/`, por lo que **se importa automáticamente** la primera vez que se crea el volumen `db_data`.

Verificar las tablas (reemplaza `<usuario>` y `<password>` por los valores de tu `.env`):

```bash
docker exec -it gestion_escolar_db mysql -u<usuario> -p<password> escolar_docker -e "SHOW TABLES;"
```

Resultado esperado: `asignaturas`, `calificaciones`, `estudiantes`, `inscripciones`.

Si hace falta importar el SQL manualmente (por ejemplo, el volumen ya existía):

**Windows (CMD) y GNU/Linux:**
```bash
docker exec -i gestion_escolar_db mysql -u<usuario> -p<password> escolar_docker < database/sql/docker_db.sql
```

Para reiniciar la base desde cero y que se vuelva a cargar el SQL:
```bash
docker compose down -v
docker compose up -d
```

## 11. Base de datos institucional

El servidor MySQL del docente aloja las tablas `docentes`, `aulas`, `gestion_academica` y `asignacion_docentes`. **Estas tablas no se duplican en la BD Docker.**

1. Obtén del docente: IP, puerto, nombre de la base de datos (`pro413_418`), usuario y contraseña.
2. Colócalos **únicamente en tu `.env`** (variables `DB2_*`), nunca en el código ni en `.env.example`.
3. Recrea el contenedor para que tome las variables:
   ```bash
   docker compose up -d --force-recreate app
   ```
4. La estructura de las tablas está documentada en `database/sql/institucional_db.sql`. Se ejecuta **en el servidor del docente** (con MySQL Workbench, DBeaver o el cliente `mysql`), no en el contenedor Docker.
5. Verifica la conexión:
   ```bash
   docker compose exec app php artisan tinker --execute="dump(DB::connection('institucional')->select('select 1 as ok'));"
   ```
   Resultado esperado: `ok => 1`.

> El equipo debe estar conectado a la **misma red local** que el servidor del docente. Con otra red (por ejemplo datos móviles) la conexión expira.

## 12. Verificación

1. **Contenedores:** `docker compose ps` muestra `gestion_escolar_app` y `gestion_escolar_db` en estado `Up`.
2. **Aplicación en el contenedor:** abrir `http://localhost:8000/estudiantes` y ver el listado.
3. **Conexión Docker:** crear un estudiante y comprobar que aparece en el listado y en la base:
   ```bash
   docker exec -it gestion_escolar_db mysql -u<usuario> -p<password> escolar_docker -e "SELECT id, ci, nombres FROM estudiantes;"
   ```
4. **Conexión institucional:** ejecutar el comando `tinker` de la sección 11 y abrir `http://localhost:8000/reporte`, que debe devolver datos combinados de ambos servidores.
5. **Separación de datos:** confirmar que `SHOW TABLES` en la BD Docker no contiene `docentes`, `aulas`, `gestion_academica` ni `asignacion_docentes`, y que el servidor institucional no contiene `estudiantes`, `asignaturas`, `inscripciones` ni `calificaciones`.
6. **Datos inválidos:**
   - Crear un estudiante con un CI ya registrado: debe mostrar el error de validación.
   - Usar un email con formato incorrecto: debe mostrar el error de validación.
   - Detener el servidor institucional o usar credenciales incorrectas en `DB2_*`: la aplicación responde con un mensaje genérico, sin exponer credenciales ni trazas.
7. **Acceso desde otro equipo:** abrir `http://<IP_DEL_EQUIPO>:8000` desde otro PC de la red local.
8. **Credenciales no expuestas:** `git ls-files | findstr .env` (Windows) o `git ls-files | grep .env` (Linux) solo debe listar `.env.example`.
9. **Reproducibilidad:** `docker compose down -v`, luego `docker compose up -d --build`; las tablas deben recrearse solas.

## 13. Solución de problemas

| Problema | Causa probable | Solución |
|---|---|---|
| `Access denied for user ...` al abrir la app | El volumen de MySQL se creó con otras credenciales | Verificar el `.env`; luego `docker compose down -v` y `docker compose up -d` |
| `The "DB_USERNAME" variable is not set` (WARN) | Falta el archivo `.env` | Crear el `.env` (sección 7) |
| `Table 'escolar_docker.sessions' doesn't exist` | Laravel usa sesiones en base de datos por defecto | Poner `SESSION_DRIVER=file`, `CACHE_STORE=file` y `QUEUE_CONNECTION=sync`, y ejecutar `docker compose up -d --force-recreate app` |
| El contenedor `gestion_escolar_db` se detiene con `Can't initialize batch_readline` | `database/sql/docker_db.sql` no existía y Docker creó una **carpeta** con ese nombre | Eliminar esa carpeta, crear el archivo real (se versiona en el repo), y repetir `docker compose down -v` y `up -d` |
| `404 Not Found` en `/estudiantes` | La imagen contiene una versión antigua del código | `docker compose up -d --build` |
| `file_get_contents(.env)` al ejecutar `key:generate` | El `.env` no se copia a la imagen (por `.dockerignore`) | Usar `key:generate --show` y pegar la clave en el `.env` (sección 7) |
| `Connection timed out` en la conexión `institucional` | Equipo fuera de la red del docente, IP o puerto incorrectos, o firewall | Conectarse a la red del aula, revisar `DB2_HOST` y `DB2_PORT`; probar `Test-NetConnection <IP> -Port 3306` (Windows) o `nc -zv <IP> 3306` (Linux) |
| `Access denied` en la conexión `institucional` | Usuario o contraseña incorrectos, o el usuario no permite conectar desde la IP del equipo | Revisar `DB2_USERNAME` y `DB2_PASSWORD`; pedir al docente que autorice la IP |
| `Unknown database` en `institucional` | `DB2_DATABASE` mal escrito | Usar el nombre asignado (`pro413_418`) |
| Falla al descargar imágenes (`no such host`) | Problema de DNS o red | Cambiar de red, o configurar DNS `8.8.8.8` y `1.1.1.1` en Docker Desktop (Settings → Docker Engine → `"dns"`) |
| Otro equipo no accede a `http://<IP>:8000` | Firewall o red distinta | Abrir el puerto 8000 (sección 9) y verificar que ambos equipos estén en la misma red |
| El puerto 8000 está ocupado | Otro servicio lo usa | Cambiar el mapeo en `docker-compose.yml` (por ejemplo `"8080:8000"`) |
| `strict_types declaration must be the very first statement` | Código antes de `declare(strict_types=1);` | `declare(strict_types=1);` debe ir inmediatamente después de `<?php` |
| Errores de permisos en `storage` (Linux) | Permisos del volumen montado | `docker compose exec app chmod -R 775 storage bootstrap/cache` |

Diagnóstico básico:
```bash
docker compose ps
docker compose logs app
docker compose logs db_docker
docker compose exec app php artisan route:list
docker compose exec app php artisan optimize:clear
```

---

## Aspectos técnicos aplicados

- **Programación orientada a objetos (3 pilares):**
  - *Herencia:* los modelos extienden `Model` y los controladores extienden `Controller`.
  - *Encapsulación:* atributos protegidos (`$fillable`, `$table`, `$connection`) y métodos que ocultan la lógica interna (por ejemplo `nombreCompleto()`).
  - *Abstracción y polimorfismo:* `ReporteAcademicoInterface` define el contrato, `ReporteAcademicoService` lo implementa y se inyecta por el contenedor de servicios de Laravel.
- **Tipado estricto:** `declare(strict_types=1);` en controladores, modelos, requests, servicios y rutas.
- **Estructura de datos:** los registros de las bases de datos se transforman en **arreglos asociativos** (indexados por CI o por `id`) antes de presentarse en HTML o JSON. En el reporte, las asignaturas se indexan por `id` con `keyBy` para cruzarlas en memoria con las asignaciones del otro servidor, en una sola consulta por servidor.
- **Validaciones:** Form Requests (`EstudianteRequest`) y restricciones SQL (`UNIQUE`, `FOREIGN KEY`, `CHECK`).
- **Manejo de errores:** bloques `try/catch` con registro en log y respuestas genéricas al usuario.
- **Seguridad:** credenciales solo en `.env`; `.env` excluido por `.gitignore` y `.dockerignore`; el repositorio incluye únicamente `.env.example`.