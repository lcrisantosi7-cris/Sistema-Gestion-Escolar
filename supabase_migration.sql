--EduCore — Script de PostgreSQL (Supabase)
-- Pega este contenido en: Supabase → SQL Editor → New query

-- TABLAS BASE

CREATE TABLE IF NOT EXISTS persona (
    idPersona SERIAL PRIMARY KEY,
    dni VARCHAR(15) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidoPaterno VARCHAR(80) NOT NULL,
    apellidoMaterno VARCHAR(80) NOT NULL,
    genero VARCHAR(15),
    direccion VARCHAR(200),
    fechaNacimiento DATE
);

CREATE TABLE IF NOT EXISTS rol (
    idRol SERIAL PRIMARY KEY,
    nombreRol VARCHAR(50) NOT NULL
);

-- Roles iniciales
INSERT INTO
    rol (nombreRol)
VALUES ('Director'),
    ('Secretaria'),
    ('Docente') ON CONFLICT DO NOTHING;

CREATE TABLE IF NOT EXISTS personal (
    idPersonal SERIAL PRIMARY KEY,
    idRol INT NOT NULL REFERENCES rol (idRol),
    idPersona INT NOT NULL REFERENCES persona (idPersona) ON DELETE CASCADE,
    fechaContrato DATE,
    correo VARCHAR(120),
    telefono VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS usuario (
    idUsuario SERIAL PRIMARY KEY,
    idPersonal INT NOT NULL REFERENCES personal (idPersonal) ON DELETE CASCADE,
    username VARCHAR(60) NOT NULL UNIQUE,
    password VARCHAR(300) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo'
);

-- ─── ESTRUCTURA ACADÉMICA ─────────────────────────────────────

CREATE TABLE IF NOT EXISTS nivel (
    idNivel SERIAL PRIMARY KEY,
    nombreNivel VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS grado (
    idGrado SERIAL PRIMARY KEY,
    nombreGrado VARCHAR(60) NOT NULL,
    nivel VARCHAR(50) NOT NULL -- 'Primaria' | 'Secundaria'
);

CREATE TABLE IF NOT EXISTS periodoacademico (
    idPeriodo SERIAL PRIMARY KEY,
    anio INT NOT NULL,
    fechaInicio DATE NOT NULL,
    fechaFin DATE NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo'
);

CREATE TABLE IF NOT EXISTS bimestre (
    idBimestre SERIAL PRIMARY KEY,
    nombreBimestre VARCHAR(50) NOT NULL,
    fechaInicio DATE NOT NULL,
    fechaFin DATE NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
    idPeriodo INT NOT NULL REFERENCES periodoacademico (idPeriodo) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS seccion (
    idSeccion SERIAL PRIMARY KEY,
    nombreSeccion VARCHAR(10) NOT NULL,
    vacantes INT NOT NULL DEFAULT 30,
    idPersonal INT NOT NULL REFERENCES personal (idPersonal),
    idGrado INT NOT NULL REFERENCES grado (idGrado),
    idPeriodo INT NOT NULL REFERENCES periodoacademico (idPeriodo) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS curso (
    idCurso SERIAL PRIMARY KEY,
    nombreCurso VARCHAR(100) NOT NULL,
    idNivel INT REFERENCES nivel (idNivel)
);

CREATE TABLE IF NOT EXISTS competenciacurso (
    idCompetenciaCurso SERIAL PRIMARY KEY,
    textCompetencia TEXT NOT NULL,
    idCurso INT NOT NULL REFERENCES curso (idCurso) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS competenciatransversal (
    idCompetenciaTransversal SERIAL PRIMARY KEY,
    textCompetencia TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS asignaciondocente (
    idAsignacion SERIAL PRIMARY KEY,
    horaInicio TIME NOT NULL,
    horaFin TIME NOT NULL,
    diaSemana VARCHAR(15) NOT NULL,
    idPersonal INT NOT NULL REFERENCES personal (idPersonal),
    idCurso INT NOT NULL REFERENCES curso (idCurso),
    idSeccion INT NOT NULL REFERENCES seccion (idSeccion) ON DELETE CASCADE
);

-- ESTUDIANTES

CREATE TABLE IF NOT EXISTS apoderado (
    idApoderado SERIAL PRIMARY KEY,
    idPersona INT NOT NULL REFERENCES persona (idPersona) ON DELETE CASCADE,
    ocupacion VARCHAR(100),
    correo VARCHAR(120),
    telefono VARCHAR(20),
    direccion VARCHAR(200)
);

CREATE TABLE IF NOT EXISTS estudiante (
    idEstudiante SERIAL PRIMARY KEY,
    edad INT,
    idPersona INT NOT NULL REFERENCES persona (idPersona) ON DELETE CASCADE,
    idApoderado INT REFERENCES apoderado (idApoderado)
);

CREATE TABLE IF NOT EXISTS matricula (
    idMatricula SERIAL PRIMARY KEY,
    fecha TIMESTAMP NOT NULL DEFAULT NOW(),
    estado VARCHAR(20) NOT NULL DEFAULT 'Matriculado',
    idSeccion INT NOT NULL REFERENCES seccion (idSeccion),
    idEstudiante INT NOT NULL REFERENCES estudiante (idEstudiante),
    doc_ficha_matricula BOOLEAN DEFAULT FALSE,
    doc_copia_dni BOOLEAN DEFAULT FALSE,
    doc_certificado_estudios BOOLEAN DEFAULT FALSE,
    doc_partida_nacimiento BOOLEAN DEFAULT FALSE
);

--CALIFICACIONES Y ASISTENCIA

CREATE TABLE IF NOT EXISTS calificacioncurso (
    idCalificacion SERIAL PRIMARY KEY,
    idMatricula INT NOT NULL REFERENCES matricula (idMatricula) ON DELETE CASCADE,
    idCompetenciaCurso INT NOT NULL REFERENCES competenciacurso (idCompetenciaCurso),
    idBimestre INT NOT NULL REFERENCES bimestre (idBimestre),
    nota NUMERIC(5, 2),
    -- Clave única para el ON CONFLICT del upsert de notas
    UNIQUE (
        idMatricula,
        idCompetenciaCurso,
        idBimestre
    )
);

CREATE TABLE IF NOT EXISTS calificaciontransversal (
    idCalificacion SERIAL PRIMARY KEY,
    idMatricula INT NOT NULL REFERENCES matricula (idMatricula) ON DELETE CASCADE,
    idCompetenciaTransversal INT NOT NULL REFERENCES competenciatransversal (idCompetenciaTransversal),
    idBimestre INT NOT NULL REFERENCES bimestre (idBimestre),
    nota NUMERIC(5, 2),
    UNIQUE (
        idMatricula,
        idCompetenciaTransversal,
        idBimestre
    )
);

CREATE TABLE IF NOT EXISTS asistencia (
    idAsistencia SERIAL PRIMARY KEY,
    fechaHora TIMESTAMP NOT NULL DEFAULT NOW(),
    estado VARCHAR(20) NOT NULL, -- 'Asistio' | 'Falto' | 'Justifico'
    idAsignacion INT NOT NULL REFERENCES asignaciondocente (idAsignacion) ON DELETE CASCADE,
    idMatricula INT NOT NULL REFERENCES matricula (idMatricula) ON DELETE CASCADE
);

-- ─── ÍNDICES ÚTILES ───────────────────────────────────────────

CREATE INDEX IF NOT EXISTS idx_matricula_seccion ON matricula (idSeccion);

CREATE INDEX IF NOT EXISTS idx_matricula_estudiante ON matricula (idEstudiante);

CREATE INDEX IF NOT EXISTS idx_asignacion_personal ON asignaciondocente (idPersonal);

CREATE INDEX IF NOT EXISTS idx_asignacion_seccion ON asignaciondocente (idSeccion);

CREATE INDEX IF NOT EXISTS idx_calificacion_mat ON calificacioncurso (idMatricula);

CREATE INDEX IF NOT EXISTS idx_asistencia_asig ON asistencia (idAsignacion);

-- ─── FIN ──────────────────────────────────────────────────────
-- Ahora puedes insertar tus datos con INSERT INTO ...