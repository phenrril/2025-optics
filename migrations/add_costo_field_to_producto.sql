-- =====================================================
-- MIGRACIÓN: Agregar campo 'costo' a la tabla 'producto'
-- =====================================================
-- Descripción: Agrega un campo booleano 'costo' para 
-- indicar si el producto es un costo directo
-- Fecha: 2025
-- =====================================================


-- Agregar columna 'costo' a la tabla producto
ALTER TABLE producto ADD COLUMN costo TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si el producto es un costo directo (1=SI, 0=NO)';

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- SHOW COLUMNS FROM producto WHERE Field = 'costo';
-- =====================================================
