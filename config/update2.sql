-- Modo dios para admin
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS modo_dios BOOLEAN DEFAULT FALSE;
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS modo_dios_rol VARCHAR(20) DEFAULT NULL;

-- Perfil incompleto: campos que el usuario completa al primer login
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS perfil_completo BOOLEAN DEFAULT FALSE;
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS telefono VARCHAR(30);
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS direccion VARCHAR(200);
ALTER TABLE alumnos  ADD COLUMN IF NOT EXISTS email VARCHAR(150);

-- Marcar admin como perfil completo
UPDATE usuarios SET perfil_completo=TRUE, modo_dios=TRUE WHERE cedula='admin';
UPDATE usuarios SET perfil_completo=TRUE WHERE rol='directivo';
