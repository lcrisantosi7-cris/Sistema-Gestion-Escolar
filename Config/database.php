<?php
/**
 * Configuración de conexión — Supabase (PostgreSQL)
 *
 * PostgreSQL normaliza los identificadores a minúsculas a menos que estén
 * entre comillas dobles. Para no tener que reescribir cada consulta con
 * aliases, usamos un mapa de normalización que convierte las claves que
 * devuelve PDO al camelCase que espera el resto del sistema.
 */
class Database {
    public $conexion;

    // ─── Singleton global: una sola conexión por request ─────────────────────
    private static ?PDONormalizer $sharedConnection = null;

    // ─── Credenciales ────────────────────────────────────────────────────────
    private $host    = 'aws-1-us-east-1.pooler.supabase.com';
    private $dbname  = 'postgres';
    private $usuario = 'postgres.hygfnzmuhoutcfadeawn';
    private $clave   = 'SistemaGestionEscolar';
    private $puerto  = '6543';
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Mapa: clave_en_minúsculas → camelCase esperado por el sistema.
     * Solo se necesitan las columnas cuyos nombres tienen mayúsculas internas.
     */
    private static $keyMap = [
        // persona
        'idpersona'               => 'idPersona',
        'apellidopaterno'         => 'apellidoPaterno',
        'apellidomaterno'         => 'apellidoMaterno',
        'fechanacimiento'         => 'fechaNacimiento',
        // personal / usuario
        'idpersonal'              => 'idPersonal',
        'fechacontrato'           => 'fechaContrato',
        'idusuario'               => 'idUsuario',
        'estadousuario'           => 'estadoUsuario',
        // rol
        'idrol'                   => 'idRol',
        'nombrerol'               => 'nombreRol',
        // periodo / bimestre
        'idperiodo'               => 'idPeriodo',
        'fechainicio'             => 'fechaInicio',
        'fechafin'                => 'fechaFin',
        'idbimestre'              => 'idBimestre',
        'nombrebimestre'          => 'nombreBimestre',
        // grado / seccion
        'idgrado'                 => 'idGrado',
        'nombregrado'             => 'nombreGrado',
        'idseccion'               => 'idSeccion',
        'nombreseccion'           => 'nombreSeccion',
        // curso / nivel / competencia
        'idcurso'                 => 'idCurso',
        'nombrecurso'             => 'nombreCurso',
        'idnivel'                 => 'idNivel',
        'nombrenivel'             => 'nombreNivel',
        'idcompetenciacurso'      => 'idCompetenciaCurso',
        'textcompetencia'         => 'textCompetencia',
        'idcompetenciatransversal'=> 'idCompetenciaTransversal',
        // asignacion
        'idasignacion'            => 'idAsignacion',
        'horainicio'              => 'horaInicio',
        'horafin'                 => 'horaFin',
        'diasemana'               => 'diaSemana',
        // matricula / estudiante / apoderado
        'idmatricula'             => 'idMatricula',
        'idestudiante'            => 'idEstudiante',
        'idapoderado'             => 'idApoderado',
        'ultimamatricula'         => 'ultimaMatricula',
        'doc_ficha_matricula'     => 'doc_ficha_matricula',   // ya en snake_case, sin cambio
        'doc_copia_dni'           => 'doc_copia_dni',
        'doc_certificado_estudios'=> 'doc_certificado_estudios',
        'doc_partida_nacimiento'  => 'doc_partida_nacimiento',
        // asistencia
        'idasistencia'            => 'idAsistencia',
        'fechahora'               => 'fechaHora',
        // boleta (aliases ya definidos en SQL, pero por si acaso)
        'nomest'                  => 'nomEst',
        'apepatest'               => 'apePatEst',
        'apematest'               => 'apeMatEst',
        'nomtut'                  => 'nomTut',
        'apepattut'               => 'apePatTut',
        'apemattut'               => 'apeMatTut',
        // conducta (typo original en BD mantenido)
        'nombrecondcuta'          => 'nombreCondcuta',
    ];

    public function conectar() {
        // Singleton global: reutiliza la misma conexión en toda la request
        if (self::$sharedConnection !== null) {
            $this->conexion = self::$sharedConnection;
            return true;
        }

        try {
            $dsn = "pgsql:host={$this->host};port={$this->puerto};dbname={$this->dbname};sslmode=require";

            self::$sharedConnection = new PDONormalizer($dsn, $this->usuario, $this->clave, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => true,
            ]);

            self::$sharedConnection->exec("SET client_encoding = 'UTF8'");
            $this->conexion = self::$sharedConnection;
            return true;
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }

    /** Normaliza un array asociativo usando el mapa de claves. */
    public static function fixKeys(array $row): array {
        $out = [];
        foreach ($row as $k => $v) {
            $lower = strtolower($k);
            $out[self::$keyMap[$lower] ?? $k] = $v;
        }
        return $out;
    }
}

// ─── PDO wrapper que normaliza claves automáticamente ────────────────────────
// Extiende PDO para que fetch() y fetchAll() apliquen fixKeys() de forma
// transparente. Así ningún modelo necesita cambios.

class PDONormalizer extends PDO {
    public function prepare($sql, $options = []): PDOStatementNormalizer|false {
        $stmt = parent::prepare($sql, $options);
        return $stmt ? new PDOStatementNormalizer($stmt) : false;
    }

    public function query(string $sql, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatementNormalizer|false {
        $stmt = parent::query($sql, $fetchMode, ...$fetchModeArgs);
        return $stmt ? new PDOStatementNormalizer($stmt) : false;
    }
}

class PDOStatementNormalizer {
    private PDOStatement $stmt;

    public function __construct(PDOStatement $stmt) {
        $this->stmt = $stmt;
    }

    /** Delega cualquier método no definido aquí al PDOStatement real. */
    public function __call(string $name, array $args): mixed {
        return $this->stmt->$name(...$args);
    }

    public function execute(?array $params = null): bool {
        return $params !== null ? $this->stmt->execute($params) : $this->stmt->execute();
    }

    public function fetch(int $mode = PDO::FETCH_ASSOC, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed {
        $row = $this->stmt->fetch($mode, $cursorOrientation, $cursorOffset);
        return ($row && is_array($row)) ? Database::fixKeys($row) : $row;
    }

    public function fetchAll(int $mode = PDO::FETCH_ASSOC, mixed ...$args): array {
        $rows = $this->stmt->fetchAll($mode, ...$args);
        return array_map(fn($r) => is_array($r) ? Database::fixKeys($r) : $r, $rows);
    }

    public function fetchColumn(int $column = 0): mixed {
        return $this->stmt->fetchColumn($column);
    }

    public function bindParam(string|int $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool {
        return $this->stmt->bindParam($param, $var, $type, $maxLength, $driverOptions);
    }

    public function bindValue(string|int $param, mixed $value, int $type = PDO::PARAM_STR): bool {
        return $this->stmt->bindValue($param, $value, $type);
    }

    public function rowCount(): int {
        return $this->stmt->rowCount();
    }

    public function errorInfo(): array {
        return $this->stmt->errorInfo();
    }
}
?>
