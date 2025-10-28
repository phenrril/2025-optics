bvggb-- =====================================================
-- SCRIPT PARA CREAR TABLA DE HISTORIA CLÍNICA SIMPLIFICADA
-- =====================================================
-- Solo incluye: Cliente, Fecha, Tipo de Lente, Graduaciones
-- =====================================================

-- Crear tabla historia_clinica (versión simplificada)
CREATE TABLE IF NOT EXISTS `historia_clinica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_lente` varchar(50) DEFAULT NULL COMMENT 'Simples, Bifocales, Multifocales, Sol, etc.',
  `tipo_consulta` varchar(50) DEFAULT NULL COMMENT 'Nueva, Control, Revisión',
  
  -- Graduación Ojo Derecho (OD)
  `nue_od_esfera` varchar(20) DEFAULT NULL,
  `nue_od_cilindro` varchar(20) DEFAULT NULL,
  `nue_od_eje` varchar(20) DEFAULT NULL,
  
  -- Graduación Ojo Izquierdo (OI)
  `nue_oi_esfera` varchar(20) DEFAULT NULL,
  `nue_oi_cilindro` varchar(20) DEFAULT NULL,
  `nue_oi_eje` varchar(20) DEFAULT NULL,
  `nue_adicion` varchar(20) DEFAULT NULL COMMENT 'Para lentes bifocales/multifocales',
  
  `observaciones` text DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  
  PRIMARY KEY (`id`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_fecha` (`fecha`),
  CONSTRAINT `fk_historia_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- SHOW TABLES LIKE 'historia_clinica';
-- DESCRIBE historia_clinica;
-- =====================================================
