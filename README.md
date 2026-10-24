# PR0413_2_2026_HITO3_error418
**Sistema de Gestión Escolar con PHP, Laravel y Doble Conexión MySQL**

## 1. Descripción del Problema
La institución educativa requiere un sistema centralizado para administrar la información académica. El desafío principal es la distribución de los datos: la información transaccional (estudiantes, asignaturas, notas) se maneja en un servidor local (Docker), mientras que los datos maestros (docentes, aulas, gestión) residen en el servidor institucional (red local). 

La solución implementa una arquitectura de conexión dual que evita la duplicación de tablas, garantizando la integridad referencial desde la aplicación mediante validaciones y programación orientada a objetos (POO).

## 2. Descripción Funcional
La aplicación permite:
- **Gestión Local (Docker):** Registrar, consultar, modificar y eliminar Estudiantes, Asignaturas, Inscripciones y Calificaciones.
- **Consulta Institucional (Red Local):** Visualizar Docentes, Aulas y Gestión Académica.
- **Relación Cruzada:** Asignar docentes a materias consultando ambas bases de datos simultáneamente.
- **Seguridad:** Uso de tipado estricto (`declare(strict_types=1)`), validaciones y manejo de excepciones.

## 3. Integrantes y Responsabilidades
| Integrante | Rol / Responsabilidades | Usuario de GitHub |
|---|---|---|
| Aranda Silva Alan Cristhofer | Backend Laravel | [@alanhack777](https://github.com/alanhack777) |
| Quispe Mamani Rosmery | Funcionalidad web | [@rosmeryqm2020-afk](https://github.com/rosmeryqm2020-afk) |
| Copa Pérez Neyda Milena | Bases de datos e integración | [@Milenacopa120-0409](https://github.com/Milenacopa120-0409) |
| Canchilla Mamani Fabricio Isaac | Docker, despliegue y documentación | [@FabricioCanchilla](https://github.com/FabricioCanchilla) |

## 4. Arquitectura
El sistema se compone de tres capas principales:
1. **Contenedor App (Laravel):** Servidor web en PHP 8.4. Expone la aplicación en el puerto `8000`.
2. **Contenedor DB (MySQL Docker):** Base de datos relacional local para datos académicos. Utiliza un volumen persistente (`dbdata`).
3. **Servidor MySQL Institucional:** Base de datos externa proporcionada por el docente.

*Flujo de datos:* El usuario interactúa con Laravel, el cual utiliza múltiples conexiones de Eloquent para consultar y escribir dinámicamente en la base de datos correspondiente.

## 5. Requisitos Previos
- **Docker** y **Docker Compose**.
- **Git**.
- **PHP 8.4** y **Composer** (solo si se ejecuta sin Docker).
- Acceso a la red local (para la base de datos institucional).

## 6. Clonación
```bash
git clone https://github.com/FabricioCanchilla/PRO413_2_2026_HITO3_error418.git
cd PRO413_2_2026_HITO3_error418

## 7. Configuración (Variables de Entorno)
Copia el archivo de ejemplo:
```bash
# Windows
copy .env.example .env

# Linux / Mac
cp .env.example .env

## 8. Construcción de la Imagen
Para construir la imagen con el nombre exacto requerido por la rúbrica:
```bash
docker build -t gestion-escolar:error418 .

## 9. Descarga y Ejecución
Levanta los servicios (App y DB Docker) en segundo plano:
```bash
docker compose up -d --build

## 10. Base de Datos Docker (Local)
El contenedor MySQL se inicia automáticamente. Para crear las tablas e importar los datos de prueba:
```bash
# Reemplaza <nombre_contenedor> por el nombre de tu contenedor MySQL
docker exec -i <nombre_contenedor> mysql -u root -p < database/docker.sql

## 11. Base de Datos Institucional (Red Local)
Para conectarse al servidor del docente, configura la IP y credenciales en las variables `DB_INSTITUCIONAL_*` del `.env`. La aplicación está programada para consultar esta base de datos sin modificar las tablas locales.

## 12. Verificación del Funcionamiento
1. Accede a `http://localhost:8000`.
2. Registra un estudiante y verifica que aparezca en el listado (Prueba DB Docker).
3. Abre el módulo de docentes y verifica que carguen los datos desde la base institucional (Prueba de doble conexión).
4. Intenta enviar un formulario con datos inválidos para comprobar las validaciones.

## 13. Solución de Problemas
| Problema | Causa / Solución |
|---|---|
| Error de conexión a MySQL | Ejecuta `docker compose ps` y verifica que el contenedor de la DB esté arriba. |
| No conecta a la base institucional | Revisa la IP, el puerto y las credenciales en `DB_INSTITUCIONAL_*`. |
| Error de permisos en Linux | Ejecuta `chmod -R 777 storage bootstrap/cache`. |
| El puerto 8000 está ocupado | Cambia el mapeo de puertos en `docker-compose.yml`. |
| Laravel no encuentra las tablas | Ejecuta las migraciones: `docker compose exec app php artisan migrate`. |