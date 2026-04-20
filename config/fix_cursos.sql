SET client_encoding = 'UTF8';

DELETE FROM asistencia;
DELETE FROM inscripciones;
DELETE FROM cursos;

INSERT INTO cursos (nombre, turno, anio_lectivo, nivel, especialidad) VALUES
('7mo - A',             'mañana', 2026, 'basica_superior',          'ninguna'),
('7mo - B',             'mañana', 2026, 'basica_superior',          'ninguna'),
('8vo - A',             'mañana', 2026, 'basica_superior',          'ninguna'),
('8vo - B',             'mañana', 2026, 'basica_superior',          'ninguna'),
('9no - A',             'mañana', 2026, 'basica_superior',          'ninguna'),
('9no - B',             'mañana', 2026, 'basica_superior',          'ninguna'),
('1ro Informática - A', 'mañana', 2026, 'bachillerato_informatica', 'informatica'),
('1ro Informática - B', 'tarde',  2026, 'bachillerato_informatica', 'informatica'),
('2do Informática - A', 'mañana', 2026, 'bachillerato_informatica', 'informatica'),
('3ro Informática - A', 'mañana', 2026, 'bachillerato_informatica', 'informatica'),
('1ro Diseño - A',      'mañana', 2026, 'bachillerato_diseno',      'diseno'),
('2do Diseño - A',      'mañana', 2026, 'bachillerato_diseno',      'diseno'),
('3ro Diseño - A',      'mañana', 2026, 'bachillerato_diseno',      'diseno'),
('1ro Sociales - A',    'mañana', 2026, 'bachillerato_sociales',    'sociales'),
('2do Sociales - A',    'mañana', 2026, 'bachillerato_sociales',    'sociales'),
('3ro Sociales - A',    'mañana', 2026, 'bachillerato_sociales',    'sociales');
