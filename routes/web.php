<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/Security.php';
require_once __DIR__ . '/../app/helpers/Mailer.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/AlumnoController.php';
require_once __DIR__ . '/../app/controllers/CursoController.php';
require_once __DIR__ . '/../app/controllers/AsistenciaController.php';
require_once __DIR__ . '/../app/controllers/UsuarioController.php';
require_once __DIR__ . '/../app/controllers/JustificativoController.php';
require_once __DIR__ . '/../app/models/Materia.php';
require_once __DIR__ . '/../app/models/Notificacion.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$base = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
$path = '/' . trim(substr($uri, strlen($base)), '/');

// Auth
if ($path === '/' || $path === '')   { (new AuthController)->index();  return; }
if ($path === '/login')              { (new AuthController)->login();  return; }
if ($path === '/logout')             { (new AuthController)->logout(); return; }
if ($path === '/registrarse')        { (new AuthController)->registrarse(); return; }
if ($path === '/registro/completar') { (new AuthController)->registrarCompletar(); return; }
if ($path === '/verificar-email')    { (new AuthController)->verificarEmail(); return; }
if ($path === '/olvide-clave')       { (new AuthController)->olvideClave(); return; }
if ($path === '/reset-password')     { (new AuthController)->resetPassword(); return; }
if ($path === '/seleccionar-rol')    { (new AuthController)->seleccionarRol(); return; }

// Dashboard
if ($path === '/' || $path === '/dashboard') { (new DashboardController)->index(); return; }

// Alumnos
if ($path === '/alumnos')         { (new AlumnoController)->index();  return; }
if ($path === '/alumnos/perfil')  { (new AlumnoController)->perfil(); return; }
if ($path === '/alumnos/crear')   { (new AlumnoController)->crear();  return; }
if (preg_match('#^/alumnos/(\d+)$#', $path, $m))         { (new AlumnoController)->ver($m[1]);     return; }
if (preg_match('#^/alumnos/(\d+)/editar$#', $path, $m))  { (new AlumnoController)->editar($m[1]);  return; }
if (preg_match('#^/alumnos/(\d+)/eliminar$#', $path, $m)){ (new AlumnoController)->eliminar($m[1]);return; }

// Cursos
if ($path === '/cursos')        { (new CursoController)->index();  return; }
if ($path === '/cursos/crear')  { (new CursoController)->crear();  return; }
if (preg_match('#^/cursos/(\d+)/editar$#', $path, $m))   { (new CursoController)->editar($m[1]);   return; }
if (preg_match('#^/cursos/(\d+)/eliminar$#', $path, $m)) { (new CursoController)->eliminar($m[1]); return; }
if (preg_match('#^/cursos/(\d+)/materias$#', $path, $m)) { (new CursoController)->materias($m[1]); return; }

// Asistencia
if ($path === '/asistencia')          { (new AsistenciaController)->index();        return; }
if ($path === '/asistencia/guardar')  { (new AsistenciaController)->guardar();      return; }
if ($path === '/asistencia/exportar') { (new AsistenciaController)->exportarExcel();return; }

// Reportes
if ($path === '/reportes/curso')  { (new AsistenciaController)->reporteCurso(); return; }
if (preg_match('#^/reportes/alumno/(\d+)$#', $path, $m)) { (new AsistenciaController)->reporteAlumno($m[1]); return; }
if ($path === '/reportes/alumno') {
    AuthController::check(['admin','directivo','profesor']);
    require __DIR__ . '/../app/views/asistencia/buscar_alumno.php';
    return;
}

// Materias
if ($path === '/materias') {
    AuthController::check(['admin','directivo']);
    $materias = (new Materia)->getAll();
    require __DIR__ . '/../app/views/materias/index.php';
    return;
}
if ($path === '/materias/crear') {
    AuthController::check(['admin','directivo']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        (new Materia)->create(['nombre'=>$_POST['nombre'],'descripcion'=>$_POST['descripcion']??'']);
    }
    header('Location: ' . BASE_URL . '/materias'); exit;
}
if (preg_match('#^/materias/(\d+)/editar$#', $path, $m)) {
    AuthController::check(['admin','directivo']);
    $materia = (new Materia)->findById($m[1]);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        (new Materia)->update($m[1], ['nombre'=>$_POST['nombre'],'descripcion'=>$_POST['descripcion']??'']);
        header('Location: ' . BASE_URL . '/materias'); exit;
    }
    // inline edit redirect back
    header('Location: ' . BASE_URL . '/materias'); exit;
}
if (preg_match('#^/materias/(\d+)/eliminar$#', $path, $m)) {
    AuthController::check(['admin']);
    (new Materia)->delete($m[1]);
    header('Location: ' . BASE_URL . '/materias'); exit;
}

// Modo Dios (solo admin)
if (preg_match('#^/modo-dios/(\w+)$#', $path, $m)) {
    AuthController::check(['admin']);
    $rol = $m[1];
    if ($rol === 'reset' || $rol === 'admin') {
        unset($_SESSION['modo_dios_rol']);
    } else {
        $_SESSION['modo_dios_rol'] = $rol;
    }
    header('Location: ' . BASE_URL . '/dashboard'); exit;
}

// Justificativos
if ($path === '/justificativos')        { (new JustificativoController)->index();  return; }
if ($path === '/justificativos/crear')  { (new JustificativoController)->crear();  return; }
if (preg_match('#^/justificativos/(\d+)/aprobar$#', $path, $m))  { (new JustificativoController)->aprobar((int)$m[1]);  return; }
if (preg_match('#^/justificativos/(\d+)/rechazar$#', $path, $m)) { (new JustificativoController)->rechazar((int)$m[1]); return; }

// Notificaciones
if ($path === '/notificaciones') {
    AuthController::check();
    $notiModel = new Notificacion();
    $notificaciones = $notiModel->getTodas($_SESSION['user_id']);
    $notiModel->marcarTodasLeidas($_SESSION['user_id']);
    require __DIR__ . '/../app/views/notificaciones/index.php';
    return;
}

// Usuarios
if ($path === '/usuarios')                { (new UsuarioController)->index();             return; }
if ($path === '/usuarios/staff')          { (new UsuarioController)->staff();             return; }
if ($path === '/usuarios/crear-staff')    { (new UsuarioController)->crearStaff();        return; }
if ($path === '/usuarios/crear-profesor') { (new UsuarioController)->registrarProfesor(); return; }
if ($path === '/usuarios/crear-directivo'){ (new UsuarioController)->registrarDirectivo();return; }
if ($path === '/usuarios/crear')          { (new UsuarioController)->crear();             return; }
if (preg_match('#^/usuarios/(\d+)/editar$#',   $path, $m)) { (new UsuarioController)->editar((int)$m[1]);   return; }
if (preg_match('#^/usuarios/(\d+)/eliminar$#', $path, $m)) { (new UsuarioController)->eliminar((int)$m[1]); return; }

// 404
http_response_code(404);
echo '<div style="text-align:center;padding:4rem;font-family:sans-serif"><h2>404 — Página no encontrada</h2><a href="' . BASE_URL . '/dashboard">Volver al inicio</a></div>';
