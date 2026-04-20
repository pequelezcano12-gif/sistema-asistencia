<?php
require_once __DIR__ . '/../../config/database.php';

class Asistencia {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    public function registrar(int $alumno_id, int $curso_id, ?int $materia_id, string $fecha,
                              string $estado, string $observaciones, int $profesor_id): bool {
        $st = $this->db->prepare(
            "INSERT INTO asistencia (alumno_id, curso_id, materia_id, fecha, estado, observaciones, profesor_id)
             VALUES (:al, :cu, :ma, :fe, :es, :ob, :pr)
             ON CONFLICT (alumno_id, curso_id, fecha, materia_id)
             DO UPDATE SET estado=EXCLUDED.estado, observaciones=EXCLUDED.observaciones, profesor_id=EXCLUDED.profesor_id"
        );
        return $st->execute([':al'=>$alumno_id,':cu'=>$curso_id,':ma'=>$materia_id,
                             ':fe'=>$fecha,':es'=>$estado,':ob'=>$observaciones,':pr'=>$profesor_id]);
    }

    public function getPorFechaYCurso(int $curso_id, string $fecha, ?int $materia_id = null): array {
        $sql = "SELECT a.*, al.nombre, al.apellido, al.foto
                FROM asistencia a
                JOIN alumnos al ON a.alumno_id = al.id
                WHERE a.curso_id = :cid AND a.fecha = :fe";
        $params = [':cid'=>$curso_id, ':fe'=>$fecha];
        if ($materia_id) { $sql .= " AND a.materia_id = :mid"; $params[':mid'] = $materia_id; }
        $sql .= " ORDER BY al.apellido, al.nombre";
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public function getPorAlumno(int $alumno_id, ?string $desde = null, ?string $hasta = null): array {
        $sql = "SELECT a.*, c.nombre AS curso_nombre, m.nombre AS materia_nombre
                FROM asistencia a
                JOIN cursos c ON a.curso_id = c.id
                LEFT JOIN materias m ON a.materia_id = m.id
                WHERE a.alumno_id = :al";
        $params = [':al' => $alumno_id];
        if ($desde) { $sql .= " AND a.fecha >= :de"; $params[':de'] = $desde; }
        if ($hasta) { $sql .= " AND a.fecha <= :ha"; $params[':ha'] = $hasta; }
        $sql .= " ORDER BY a.fecha DESC";
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public function getResumenAlumno(int $alumno_id, ?int $anio = null): array {
        $anio = $anio ?? (int)date('Y');
        $st = $this->db->prepare(
            "SELECT estado, COUNT(*) AS total FROM asistencia
             WHERE alumno_id = :al AND EXTRACT(YEAR FROM fecha) = :an
             GROUP BY estado"
        );
        $st->execute([':al'=>$alumno_id, ':an'=>$anio]);
        $resumen = ['presente'=>0,'ausente'=>0,'tarde'=>0,'justificado'=>0];
        foreach ($st->fetchAll() as $r) $resumen[$r['estado']] = (int)$r['total'];
        return $resumen;
    }

    public function getResumenCurso(int $curso_id, ?int $mes = null, ?int $anio = null): array {
        $anio = $anio ?? (int)date('Y');
        $sql = "SELECT al.id, al.nombre, al.apellido,
                SUM(CASE WHEN a.estado='presente'    THEN 1 ELSE 0 END) AS presentes,
                SUM(CASE WHEN a.estado='ausente'     THEN 1 ELSE 0 END) AS ausentes,
                SUM(CASE WHEN a.estado='tarde'       THEN 1 ELSE 0 END) AS tardes,
                SUM(CASE WHEN a.estado='justificado' THEN 1 ELSE 0 END) AS justificados,
                COUNT(*) AS total
                FROM asistencia a
                JOIN alumnos al ON a.alumno_id = al.id
                WHERE a.curso_id = :cid AND EXTRACT(YEAR FROM a.fecha) = :an";
        $params = [':cid'=>$curso_id, ':an'=>$anio];
        if ($mes) { $sql .= " AND EXTRACT(MONTH FROM a.fecha) = :me"; $params[':me'] = $mes; }
        $sql .= " GROUP BY al.id, al.nombre, al.apellido ORDER BY al.apellido, al.nombre";
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public function getIdPorAlumnoCursoFecha(int $alumno_id, int $curso_id, string $fecha, ?int $materia_id): ?int {
        $sql = "SELECT id FROM asistencia WHERE alumno_id=:al AND curso_id=:cu AND fecha=:fe";
        $params = [':al'=>$alumno_id, ':cu'=>$curso_id, ':fe'=>$fecha];
        if ($materia_id) { $sql .= " AND materia_id=:ma"; $params[':ma'] = $materia_id; }
        else { $sql .= " AND materia_id IS NULL"; }
        $st = $this->db->prepare($sql);
        $st->execute($params);
        $id = $st->fetchColumn();
        return $id ? (int)$id : null;
    }

    public function getReporteMensual(int $curso_id, int $mes, int $anio): array {
        $st = $this->db->prepare(
            "SELECT a.fecha, al.nombre, al.apellido, a.estado, a.observaciones
             FROM asistencia a
             JOIN alumnos al ON a.alumno_id = al.id
             WHERE a.curso_id = :cid AND EXTRACT(MONTH FROM a.fecha) = :me AND EXTRACT(YEAR FROM a.fecha) = :an
             ORDER BY a.fecha, al.apellido"
        );
        $st->execute([':cid'=>$curso_id, ':me'=>$mes, ':an'=>$anio]);
        return $st->fetchAll();
    }
}
