CREATE TABLE estudiantes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    ci VARCHAR(20) NOT NULL UNIQUE,
    correo VARCHAR(150) UNIQUE,
    fecha_nacimiento DATE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE asignaturas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE inscripciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudiante_id BIGINT UNSIGNED NOT NULL,
    asignatura_id BIGINT UNSIGNED NOT NULL,
    gestion VARCHAR(20) NOT NULL,
    fecha_inscripcion DATE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_inscripcion_estudiante
        FOREIGN KEY (estudiante_id)
        REFERENCES estudiantes(id),

    CONSTRAINT fk_inscripcion_asignatura
        FOREIGN KEY (asignatura_id)
        REFERENCES asignaturas(id),

    UNIQUE (estudiante_id, asignatura_id, gestion)
) ENGINE=InnoDB;

CREATE TABLE calificaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inscripcion_id BIGINT UNSIGNED NOT NULL,
    nota DECIMAL(5,2) NOT NULL,
    observacion VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_calificacion_inscripcion
        FOREIGN KEY (inscripcion_id)
        REFERENCES inscripciones(id),

    CONSTRAINT chk_nota
        CHECK (nota BETWEEN 0 AND 100)
) ENGINE=InnoDB;

-- DATOS DE PRUEBA PARA GESTION ESCOLAR

INSERT INTO estudiantes
(nombres, apellidos, ci, correo)
VALUES
('Carlos', 'Mamani', '1234567', 'carlos@example.com'),
('Ana', 'Quispe', '7654321', 'ana@example.com'),
('Pedro', 'Flores', '4567890', 'pedro@example.com');

INSERT INTO asignaturas
(codigo, nombre, descripcion)
VALUES
('MAT101', 'Matematicas', 'Matematica basica'),
('FIS101', 'Fisica', 'Introduccion a la fisica'),
('PRO101', 'Programacion', 'Fundamentos de programacion');

INSERT INTO inscripciones
(estudiante_id, asignatura_id, gestion, fecha_inscripcion)
VALUES
(1, 1, '2026', '2026-10-24'),
(2, 2, '2026', '2026-10-24'),
(3, 3, '2026', '2026-10-24');

INSERT INTO calificaciones
(inscripcion_id, nota, observacion)
VALUES
(1, 85.00, 'Aprobado'),
(2, 90.00, 'Aprobado'),
(3, 75.00, 'Aprobado');

-- CONSULTAS DE VERIFICACION

SELECT * FROM estudiantes;
SELECT * FROM asignaturas;
SELECT * FROM inscripciones;
SELECT * FROM calificaciones;