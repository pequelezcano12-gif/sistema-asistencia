-- ============================================================
-- AsistenciaEdu — Schema PostgreSQL
-- Ejecutar en pgAdmin 4: Query Tool sobre la DB sistema_asistencia
-- ============================================================

-- Crear la base desde pgAdmin y luego ejecutar este script

CREATE TYPE rol_usuario AS ENUM ('admin','directivo','profesor','alumno');
CREATE TYPE turno_tipo  AS ENUM ('mañana','tarde');
CREATE TYPE estado_asistencia AS ENUM ('presente','ausente','tarde','justificado');

CREATE TABLE usuarios (
    id         SERIAL PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    apellido   VARCHAR(100) NOT NULL,
    email      VARCHAR(150) UNIQUE NOT NULL,
    password   VARCHAR(255) NOT NULL,
    rol        rol_usuario NOT NULL DEFAULT 'alumno',
    activo     BOOLEAN DEFAULT TRUE,
    foto       VARCHAR(255),
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE cursos (
    id           SERIAL PRIMARY KEY,
    nombre       VARCHAR(50) NOT NULL,
    turno        turno_tipo NOT NULL,
    anio_lectivo SMALLINT NOT NULL,
    activo       BOOLEAN DEFAULT TRUE
);

CREATE TABLE materias (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE curso_materia (
    id          SERIAL PRIMARY KEY,
    curso_id    INT NOT NULL REFERENCES cursos(id)   ON DELETE CASCADE,
    materia_id  INT NOT NULL REFERENCES materias(id) ON DELETE CASCADE,
    profesor_id INT REFERENCES usuarios(id) ON DELETE SET NULL,
    UNIQUE(curso_id, materia_id)
);

CREATE TABLE alumnos (
    id               SERIAL PRIMARY KEY,
    usuario_id       INT REFERENCES usuarios(id) ON DELETE SET NULL,
    nombre           VARCHAR(100) NOT NULL,
    apellido         VARCHAR(100) NOT NULL,
    dni              VARCHAR(20) UNIQUE NOT NULL,
    fecha_nacimiento DATE,
    email            VARCHAR(150),
    telefono         VARCHAR(30),
    direccion        VARCHAR(200),
    foto             VARCHAR(255),
    activo           BOOLEAN DEFAULT TRUE,
    created_at       TIMESTAMP DEFAULT NOW()
);

CREATE TABLE inscripciones (
    id               SERIAL PRIMARY KEY,
    alumno_id        INT NOT NULL REFERENCES alumnos(id)  ON DELETE CASCADE,
    curso_id         INT NOT NULL REFERENCES cursos(id)   ON DELETE CASCADE,
    materia_id       INT REFERENCES materias(id) ON DELETE SET NULL,
    anio_lectivo     SMALLINT NOT NULL,
    fecha_inscripcion DATE DEFAULT CURRENT_DATE,
    UNIQUE(alumno_id, curso_id, anio_lectivo)
);

CREATE TABLE asistencia (
    id           SERIAL PRIMARY KEY,
    alumno_id    INT NOT NULL REFERENCES alumnos(id)  ON DELETE CASCADE,
    curso_id     INT NOT NULL REFERENCES cursos(id)   ON DELETE CASCADE,
    materia_id   INT REFERENCES materias(id) ON DELETE SET NULL,
    fecha        DATE NOT NULL,
    estado       estado_asistencia NOT NULL,
    observaciones TEXT,
    profesor_id  INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    created_at   TIMESTAMP DEFAULT NOW(),
    UNIQUE(alumno_id, curso_id, fecha, materia_id)
);

-- ============================================================
-- Datos iniciales
-- password = 'password' (bcrypt)
-- ============================================================
INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES
('Admin',    'Sistema',  'admin@sistema.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Director', 'General',  'director@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'directivo');

INSERT INTO materias (nombre) VALUES
('Matemática'),('Lengua y Literatura'),('Historia'),('Geografía'),
('Ciencias Naturales'),('Educación Física'),('Inglés'),('Informática');

INSERT INTO cursos (nombre, turno, anio_lectivo) VALUES
('1°A','mañana',2026),('1°B','tarde',2026),
('2°A','mañana',2026),('2°B','tarde',2026),
('3°A','mañana',2026);
