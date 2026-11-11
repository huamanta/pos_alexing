INSERT INTO modulos
(nombre, ruta, icono, modulo_padre_id, orden, menu, estado)
SELECT
    'Conciliaciones bancarias',
    'conciliaciones-bancarias',
    'fas fa-balance-scale',
    23,
    8,
    1,
    1
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM modulos WHERE ruta = 'conciliaciones-bancarias'
);

INSERT INTO permisos (modulo_id, codigo, nombre)
SELECT id, 'conciliaciones.ver', 'Ver conciliaciones bancarias'
FROM modulos
WHERE ruta = 'conciliaciones-bancarias'
AND NOT EXISTS (
    SELECT 1 FROM permisos WHERE codigo = 'conciliaciones.ver'
);

INSERT INTO permisos (modulo_id, codigo, nombre)
SELECT id, 'conciliaciones.crear', 'Crear conciliaciones bancarias'
FROM modulos
WHERE ruta = 'conciliaciones-bancarias'
AND NOT EXISTS (
    SELECT 1 FROM permisos WHERE codigo = 'conciliaciones.crear'
);

INSERT INTO permisos (modulo_id, codigo, nombre)
SELECT id, 'conciliaciones.importar', 'Importar estados de cuenta'
FROM modulos
WHERE ruta = 'conciliaciones-bancarias'
AND NOT EXISTS (
    SELECT 1 FROM permisos WHERE codigo = 'conciliaciones.importar'
);

INSERT INTO permisos (modulo_id, codigo, nombre)
SELECT id, 'conciliaciones.conciliar', 'Conciliar movimientos manualmente'
FROM modulos
WHERE ruta = 'conciliaciones-bancarias'
AND NOT EXISTS (
    SELECT 1 FROM permisos WHERE codigo = 'conciliaciones.conciliar'
);

INSERT INTO permisos (modulo_id, codigo, nombre)
SELECT id, 'conciliaciones.cerrar', 'Cerrar conciliaciones bancarias'
FROM modulos
WHERE ruta = 'conciliaciones-bancarias'
AND NOT EXISTS (
    SELECT 1 FROM permisos WHERE codigo = 'conciliaciones.cerrar'
);

INSERT INTO permisos (modulo_id, codigo, nombre)
SELECT id, 'conciliaciones.reabrir', 'Reabrir conciliaciones bancarias cerradas'
FROM modulos
WHERE ruta = 'conciliaciones-bancarias'
AND NOT EXISTS (
    SELECT 1 FROM permisos WHERE codigo = 'conciliaciones.reabrir'
);

CREATE TABLE IF NOT EXISTS conciliaciones_bancarias (
    idconciliacion BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    idbanco INT NOT NULL,
    idsucursal INT NOT NULL,
    idusuario_creador INT NOT NULL,
    periodo_inicio DATE NOT NULL,
    periodo_fin DATE NOT NULL,
    saldo_inicial DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    saldo_final_banco DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    estado ENUM('borrador','cerrada','reabierta','anulada') NOT NULL DEFAULT 'borrador',
    observaciones TEXT NULL,
    fecha_cierre DATETIME NULL,
    idusuario_cierre INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (idconciliacion),
    KEY idx_conciliacion_banco_periodo (idbanco, periodo_inicio, periodo_fin),
    KEY idx_conciliacion_sucursal_estado (idsucursal, estado),
    CONSTRAINT fk_conciliacion_banco
        FOREIGN KEY (idbanco) REFERENCES bancos(idbanco),
    CONSTRAINT fk_conciliacion_sucursal
        FOREIGN KEY (idsucursal) REFERENCES sucursal(idsucursal),
    CONSTRAINT fk_conciliacion_usuario
        FOREIGN KEY (idusuario_creador) REFERENCES usuario(idusuario),
    CONSTRAINT fk_conciliacion_usuario_cierre
        FOREIGN KEY (idusuario_cierre) REFERENCES usuario(idusuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conciliacion_importaciones (
    idimportacion BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    idconciliacion BIGINT UNSIGNED NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    tipo_archivo VARCHAR(20) NOT NULL,
    hash_archivo CHAR(64) NOT NULL,
    tamano_archivo INT UNSIGNED NOT NULL DEFAULT 0,
    saldo_inicial DECIMAL(14,2) NULL,
    saldo_final DECIMAL(14,2) NULL,
    total_registros INT UNSIGNED NOT NULL DEFAULT 0,
    idusuario INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idimportacion),
    UNIQUE KEY uq_conciliacion_archivo (idconciliacion, hash_archivo),
    KEY idx_importacion_conciliacion (idconciliacion),
    CONSTRAINT fk_importacion_conciliacion
        FOREIGN KEY (idconciliacion) REFERENCES conciliaciones_bancarias(idconciliacion)
        ON DELETE CASCADE,
    CONSTRAINT fk_importacion_usuario
        FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conciliacion_movimientos_bancarios (
    idmovimiento_bancario BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    idimportacion BIGINT UNSIGNED NOT NULL,
    fecha_operacion DATETIME NOT NULL,
    descripcion VARCHAR(1000) NOT NULL DEFAULT '',
    numero_operacion VARCHAR(150) NULL,
    referencia VARCHAR(150) NULL,
    tipo ENUM('cargo','abono') NOT NULL,
    importe DECIMAL(14,2) NOT NULL,
    saldo DECIMAL(14,2) NULL,
    estado ENUM('pendiente','conciliado_auto','conciliado_manual','diferencia','no_identificado') NOT NULL DEFAULT 'pendiente',
    hash_fila CHAR(64) NOT NULL,
    datos_originales JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idmovimiento_bancario),
    UNIQUE KEY uq_importacion_hash_fila (idimportacion, hash_fila),
    KEY idx_movimiento_banco_fecha (fecha_operacion, tipo, estado),
    CONSTRAINT fk_movimiento_bancario_importacion
        FOREIGN KEY (idimportacion) REFERENCES conciliacion_importaciones(idimportacion)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conciliacion_relaciones (
    idrelacion BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    idmovimiento_bancario BIGINT UNSIGNED NOT NULL,
    origen_sistema ENUM('movimiento','detalle_cuentas_por_cobrar','detalle_cuentas_por_pagar','venta_pago') NOT NULL,
    idmovimiento_sistema BIGINT UNSIGNED NOT NULL,
    importe_sistema DECIMAL(14,2) NOT NULL,
    tipo_relacion ENUM('exacta','probable','manual','diferencia') NOT NULL,
    idusuario INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idrelacion),
    UNIQUE KEY uq_relacion_bancario_sistema
        (idmovimiento_bancario, origen_sistema, idmovimiento_sistema),
    KEY idx_relacion_movimiento_sistema (origen_sistema, idmovimiento_sistema),
    CONSTRAINT fk_relacion_movimiento_bancario
        FOREIGN KEY (idmovimiento_bancario) REFERENCES conciliacion_movimientos_bancarios(idmovimiento_bancario)
        ON DELETE CASCADE,
    CONSTRAINT fk_relacion_usuario
        FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conciliacion_ajustes (
    idajuste BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    idconciliacion BIGINT UNSIGNED NOT NULL,
    tipo ENUM('comision','mantenimiento','itf','interes','otro_cargo','otro_abono') NOT NULL,
    importe DECIMAL(14,2) NOT NULL,
    fecha DATETIME NOT NULL,
    descripcion VARCHAR(1000) NOT NULL,
    origen_movimiento ENUM('movimiento','detalle_cuentas_por_cobrar','detalle_cuentas_por_pagar','venta_pago') NULL,
    idmovimiento_generado BIGINT UNSIGNED NULL,
    estado ENUM('borrador','registrado','anulado') NOT NULL DEFAULT 'borrador',
    idusuario INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idajuste),
    KEY idx_ajuste_conciliacion_estado (idconciliacion, estado),
    CONSTRAINT fk_ajuste_conciliacion
        FOREIGN KEY (idconciliacion) REFERENCES conciliaciones_bancarias(idconciliacion)
        ON DELETE CASCADE,
    CONSTRAINT fk_ajuste_usuario
        FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conciliacion_auditoria (
    idaudit BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    idconciliacion BIGINT UNSIGNED NOT NULL,
    idusuario INT NOT NULL,
    accion VARCHAR(100) NOT NULL,
    detalle JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idaudit),
    KEY idx_auditoria_conciliacion_fecha (idconciliacion, created_at),
    CONSTRAINT fk_auditoria_conciliacion
        FOREIGN KEY (idconciliacion) REFERENCES conciliaciones_bancarias(idconciliacion)
        ON DELETE CASCADE,
    CONSTRAINT fk_auditoria_usuario
        FOREIGN KEY (idusuario) REFERENCES usuario(idusuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
