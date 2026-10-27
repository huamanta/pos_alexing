ALTER TABLE orden_trabajo_mecanico
    ADD COLUMN horas DECIMAL(10,2) NOT NULL DEFAULT 0,
    ADD COLUMN costo_hora DECIMAL(10,2) NOT NULL DEFAULT 0,
    ADD COLUMN subtotal DECIMAL(12,2) NOT NULL DEFAULT 0;

UPDATE orden_trabajo_mecanico otm
INNER JOIN orden_trabajo ot ON ot.idorden = otm.idorden
LEFT JOIN personal p ON p.idpersonal = otm.idpersonal
SET otm.horas = GREATEST(
        1,
        DATEDIFF(DATE(COALESCE(ot.fecha_fin, ot.fecha_inicio)), DATE(ot.fecha_inicio)) + 1
    ) * 8,
    otm.costo_hora = COALESCE(p.salario, 0) / 240,
    otm.subtotal = GREATEST(
        1,
        DATEDIFF(DATE(COALESCE(ot.fecha_fin, ot.fecha_inicio)), DATE(ot.fecha_inicio)) + 1
    ) * 8 * COALESCE(p.salario, 0) / 240;
