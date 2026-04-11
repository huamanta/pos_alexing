-- Migracion: campos para alquiler/venta de vehiculos en tabla producto
-- Ejecutar en la base de datos del sistema.

ALTER TABLE producto
  ADD COLUMN stock_maximo DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER stock_minimo,
  ADD COLUMN placa VARCHAR(20) NULL AFTER numserie,
  ADD COLUMN color VARCHAR(50) NULL AFTER placa,
  ADD COLUMN motor VARCHAR(80) NULL AFTER color,
  ADD COLUMN permiso_circulacion VARCHAR(100) NULL AFTER motor,
  ADD COLUMN anio_fabricacion INT NULL AFTER permiso_circulacion,
  ADD COLUMN tipo_vehiculo VARCHAR(30) NULL AFTER anio_fabricacion,
  ADD COLUMN clase_vehiculo VARCHAR(20) NULL AFTER tipo_vehiculo,
  ADD COLUMN propietario_vehiculo VARCHAR(50) NULL AFTER clase_vehiculo,
  ADD COLUMN controla_stock ENUM('Si','No') NOT NULL DEFAULT 'Si' AFTER propietario_vehiculo,
  ADD COLUMN alerta_stock ENUM('Si','No') NOT NULL DEFAULT 'Si' AFTER controla_stock;

-- Categoria para vehiculos (si no existe)
INSERT INTO categoria (nombre, condicion)
SELECT 'VEHICULO', '1'
FROM dual
WHERE NOT EXISTS (
  SELECT 1 FROM categoria WHERE UPPER(nombre) = 'VEHICULO'
);
