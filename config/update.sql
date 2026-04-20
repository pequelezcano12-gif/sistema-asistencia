-- Nivel del curso
ALTER TABLE cursos ADD COLUMN IF NOT EXISTS nivel VARCHAR(20) DEFAULT 'basica';
ALTER TABLE cursos ADD COLUMN IF NOT EXISTS especialidad VARCHAR(20) DEFAULT 'ninguna';

-- Asignatura del profesor
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS materia_id INT REFERENCES materias(id) ON DELETE SET NULL;

-- Notas de ausencia
CREATE TABLE IF NOT EXISTS notas_ausencia (
    id            SERIAL PRIMARY KEY,
    asistencia_id INT NOT NULL REFERENCES asistencia(id) ON DELETE CASCADE,
    profesor_id   INT NOT NULL REFERENCES usuarios(id),
    nota          TEXT NOT NULL,
    visto_director BOOLEAN DEFAULT FALSE,
    created_at    TIMESTAMP DEFAULT NOW()
);

-- Justificativos de padres
CREATE TABLE IF NOT EXISTS justificativos (
    id            SERIAL PRIMARY KEY,
    alumno_id     INT NOT NULL REFERENCES alumnos(id) ON DELETE CASCADE,
    asistencia_id INT REFERENCES asistencia(id) ON DELETE SET NULL,
    fecha_ausencia DATE NOT NULL,
    motivo        TEXT NOT NULL,
    archivo       VARCHAR(255),
    estado        VARCHAR(20) DEFAULT 'pendiente',
    visto_director BOOLEAN DEFAULT FALSE,
    padre_usuario_id INT REFERENCES usuarios(id),
    created_at    TIMESTAMP DEFAULT NOW()
);

-- Notificaciones internas
CREATE TABLE IF NOT EXISTS notificaciones (
    id           SERIAL PRIMARY KEY,
    usuario_id   INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    tipo         VARCHAR(50) NOT NULL,
    titulo       VARCHAR(200) NOT NULL,
    mensaje      TEXT,
    leida        BOOLEAN DEFAULT FALSE,
    referencia_id INT,
    created_at   TIMESTAMP DEFAULT NOW()
);

-- Actualizar cursos existentes con nivel
UPDATE cursos SET nivel = 'basica' WHERE nivel IS NULL;
