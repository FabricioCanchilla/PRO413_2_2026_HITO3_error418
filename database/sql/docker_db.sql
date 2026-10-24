-- BD DOCKER: escolar_docker
CREATE TABLE IF NOT EXISTS estudiantes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ci VARCHAR(15) NOT NULL UNIQUE,
  nombres VARCHAR(80) NOT NULL,
  apellidos VARCHAR(80) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  fecha_nacimiento DATE NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS asignaturas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(10) NOT NULL UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  creditos TINYINT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS inscripciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  estudiante_id INT NOT NULL,
  asignatura_id INT NOT NULL,
  gestion VARCHAR(10) NOT NULL,
  -- curso_id vive en la BD del docente: solo se guarda el ID, SIN FK
  curso_id INT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE (estudiante_id, asignatura_id, gestion),
  FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE CASCADE,
  FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS calificaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  inscripcion_id INT NOT NULL,
  nota DECIMAL(5,2) NOT NULL,
  tipo VARCHAR(30) NOT NULL DEFAULT 'parcial',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CHECK (nota BETWEEN 0 AND 100),
  FOREIGN KEY (inscripcion_id) REFERENCES inscripciones(id) ON DELETE CASCADE
);

INSERT INTO estudiantes (ci, nombres, apellidos, email) VALUES
('1234567','Ana','Quispe','ana@test.com'),
('7654321','Luis','Mamani','luis@test.com');

INSERT INTO asignaturas (codigo, nombre, creditos) VALUES
('PRO-413','Programación III',4),
('BDD-301','Base de Datos',4);