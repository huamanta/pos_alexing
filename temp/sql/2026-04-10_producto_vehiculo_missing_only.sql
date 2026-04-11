-- Agrega SOLO columnas faltantes para soportar vehiculos en tabla producto
-- Compatible con MySQL/MariaDB usando informacion de INFORMATION_SCHEMA.

SET @db := DATABASE();

-- stock_maximo
SET @exists := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db
    AND TABLE_NAME = 'producto'
    AND COLUMN_NAME = 'stock_maximo'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN stock_maximo DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER stock_minimo',
  'SELECT "stock_maximo ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- placa
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'placa'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN placa VARCHAR(20) NULL AFTER numserie',
  'SELECT "placa ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- color
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'color'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN color VARCHAR(50) NULL AFTER placa',
  'SELECT "color ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- motor
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'motor'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN motor VARCHAR(80) NULL AFTER color',
  'SELECT "motor ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- permiso_circulacion
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'permiso_circulacion'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN permiso_circulacion VARCHAR(100) NULL AFTER motor',
  'SELECT "permiso_circulacion ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- anio_fabricacion
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'anio_fabricacion'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN anio_fabricacion INT NULL AFTER permiso_circulacion',
  'SELECT "anio_fabricacion ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tipo_vehiculo
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'tipo_vehiculo'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN tipo_vehiculo VARCHAR(30) NULL AFTER anio_fabricacion',
  'SELECT "tipo_vehiculo ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- clase_vehiculo
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'clase_vehiculo'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN clase_vehiculo VARCHAR(20) NULL AFTER tipo_vehiculo',
  'SELECT "clase_vehiculo ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- propietario_vehiculo
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'propietario_vehiculo'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN propietario_vehiculo VARCHAR(50) NULL AFTER clase_vehiculo',
  'SELECT "propietario_vehiculo ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- controla_stock
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'controla_stock'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN controla_stock ENUM(''Si'',''No'') NOT NULL DEFAULT ''Si'' AFTER propietario_vehiculo',
  'SELECT "controla_stock ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- alerta_stock
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'alerta_stock'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE producto ADD COLUMN alerta_stock ENUM(''Si'',''No'') NOT NULL DEFAULT ''Si'' AFTER controla_stock',
  'SELECT "alerta_stock ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Categoria VEHICULO (si no existe)
INSERT INTO categoria (nombre, condicion)
SELECT 'VEHICULO', '1'
WHERE NOT EXISTS (
  SELECT 1 FROM categoria WHERE UPPER(nombre) = 'VEHICULO'
);
