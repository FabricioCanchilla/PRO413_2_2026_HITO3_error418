-- BASE DE DATOS INSTITUCIONAL
-- HITO 3 - GESTION ESCOLAR
-- Tablas correspondientes al servidor del docente

CREATE TABLE IF NOT EXISTS docentes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    ci VARCHAR(20) NOT NULL UNIQUE,
    especialidad VARCHAR(100),
    correo VARCHAR(150) UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS gestiones_academicas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado VARCHAR(20) NOT NULL,
    CHECK (fecha_fin >= fecha_inicio)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS aulas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    capacidad INT UNSIGNED NOT NULL,
    CHECK (capacidad > 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS asignaciones_docentes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    docente_id BIGINT UNSIGNED NOT NULL,
    gestion_id BIGINT UNSIGNED NOT NULL,
    aula_id BIGINT UNSIGNED NOT NULL,
    asignatura_id BIGINT UNSIGNED NOT NULL,
    UNIQUE (docente_id, gestion_id, aula_id, asignatura_id)
) ENGINE=InnoDB;