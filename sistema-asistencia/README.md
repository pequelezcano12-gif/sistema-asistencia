# AsistenciaEdu — Sistema de Gestión Escolar

## Instalación rápida

### 1. Requisitos
- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache con mod_rewrite habilitado (XAMPP/WAMP/Laragon)

### 2. Base de datos
```sql
-- Importar el schema:
mysql -u root -p < config/schema.sql
```

### 3. Configuración
Editar `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'tu_password');
define('DB_NAME', 'sistema_asistencia');
define('BASE_URL', 'http://localhost/sistema-asistencia/public');
```

### 4. Permisos
```bash
chmod -R 755 storage/uploads/
```

### 5. Acceso
- URL: `http://localhost/sistema-asistencia/public`
- Admin: `admin@sistema.com` / `password`
- Directivo: `director@sistema.com` / `password`

## Roles y permisos

| Función              | Admin | Directivo | Profesor | Alumno |
|----------------------|:-----:|:---------:|:--------:|:------:|
| Gestionar usuarios   |  ✅   |    ❌     |    ❌    |   ❌   |
| Crear/editar alumnos |  ✅   |    ✅     |    ❌    |   ❌   |
| Ver alumnos          |  ✅   |    ✅     |    ✅    |   ❌   |
| Gestionar cursos     |  ✅   |    ✅     |    ❌    |   ❌   |
| Pasar lista          |  ✅   |    ✅     |    ✅    |   ❌   |
| Ver reportes         |  ✅   |    ✅     |    ✅    |   ❌   |
| Exportar Excel/CSV   |  ✅   |    ✅     |    ❌    |   ❌   |
| Gestionar materias   |  ✅   |    ✅     |    ❌    |   ❌   |
