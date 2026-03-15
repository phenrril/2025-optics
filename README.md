# 🏥 Sistema de Gestión para Óptica

Sistema completo de gestión de ventas para ópticas con soporte para facturación electrónica ARCA (ex AFIP).

## ✨ Características

### Gestión de Ventas
- ✅ Punto de venta moderno y responsive
- ✅ Búsqueda rápida de productos
- ✅ Gestión de clientes
- ✅ Historia clínica con graduaciones
- ✅ Múltiples métodos de pago
- ✅ Descuentos y obra social
- ✅ Reportes de ventas

### Facturación Electrónica
- ✅ **Instalación automática con 1 click** 🆕
- ✅ Integración con ARCA (ex AFIP)
- ✅ Generación automática de Facturas A, B y C
- ✅ Obtención de CAE automático
- ✅ PDF oficial con código QR
- ✅ Modo Testing y Producción
- ✅ Almacenamiento seguro de comprobantes

### Gestión de Inventario
- ✅ Control de stock
- ✅ Productos con código de barras
- ✅ Alertas de stock mínimo
- ✅ Costo y precio de venta

### Administración
- ✅ Sistema de usuarios y permisos
- ✅ Múltiples roles
- ✅ Estadísticas y reportes
- ✅ Calendario de turnos
- ✅ Control de ingresos y egresos

## 🚀 Instalación Rápida

### Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Composer
- Servidor web (Apache/Nginx)

### Pasos

1. **Clonar repositorio**
```bash
git clone https://github.com/tu-usuario/2025-optics.git
cd 2025-optics
```

2. **Configurar base de datos**
```bash
mysql -u root -p < u375391241_sis_ventas.sql
```

3. **Configurar conexión**
Editar `conexion.php` con tus credenciales:
```php
$host = "localhost";
$user = "tu_usuario";
$clave = "tu_contraseña";
$bd = "c2880275_ventas";
```

4. **Instalar dependencias** (si usás facturación electrónica)
```bash
composer install
```

5. **Configurar facturación electrónica** (opcional)

**Opción A - Automática (Recomendado):**
```
1. Acceder como administrador
2. Ir a: Configuración
3. Click en botón "Instalar Ahora"
4. ¡Listo! Todo se configura automáticamente
```

**Opción B - Manual:**
```bash
mysql -u root -p c2880275_ventas < sql/setup_facturacion_electronica.sql
composer install
```

6. **Acceder al sistema**
```
URL: http://localhost/2025-optics/
Usuario: admin
Contraseña: (configurar en primer acceso)
```

## 📋 Documentación

- [**Guía de Implementación de Facturación Electrónica**](IMPLEMENTACION_FACTURACION_ELECTRONICA.md) - Guía completa para configurar facturación con ARCA

## 🛠️ Tecnologías

- **Backend:** PHP 7.4+
- **Base de Datos:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript, jQuery
- **Framework CSS:** Bootstrap 4
- **Librerías:**
  - DataTables - Tablas interactivas
  - Chart.js - Gráficos
  - SweetAlert2 - Alertas modernas
  - jQuery UI - Autocompletado
  - FPDF - Generación de PDFs

## 📁 Estructura del Proyecto

```
2025-optics/
├── assets/                 # Recursos estáticos
│   ├── css/               # Hojas de estilo
│   ├── js/                # JavaScript
│   └── img/               # Imágenes
├── src/                   # Código fuente PHP
│   ├── classes/           # Clases PHP
│   │   ├── FacturacionElectronica.php
│   │   └── FacturacionElectronicaAfipSDK.php
│   ├── includes/          # Headers y footers
│   ├── pdf/               # Generación de PDFs
│   ├── ventas.php         # Punto de venta
│   ├── lista_ventas.php   # Historial de ventas
│   ├── clientes.php       # Gestión de clientes
│   ├── productos.php      # Gestión de productos
│   ├── configuracion_facturacion.php
│   └── ...
├── sql/                   # Scripts SQL
│   └── setup_facturacion_electronica.sql
├── conexion.php           # Configuración de BD
├── composer.json          # Dependencias PHP
└── README.md
```

## 🔐 Usuarios y Permisos

El sistema incluye un sistema de roles y permisos:

- **Administrador:** Acceso completo
- **Vendedor:** Ventas, clientes, productos
- **Usuario:** Consultas y reportes

Los permisos se configuran desde el panel de administración.

## 📊 Base de Datos

### Tablas Principales

- `ventas` - Registro de ventas
- `detalle_venta` - Items de cada venta
- `cliente` - Datos de clientes
- `producto` - Inventario de productos
- `usuario` - Usuarios del sistema
- `facturas_electronicas` - Facturación electrónica
- `facturacion_config` - Configuración AFIP

## 🎨 Capturas de Pantalla

(Agregar capturas de pantalla aquí)

## 🐛 Reporte de Errores

Si encontrás algún error:

1. Verificá los logs del servidor
2. Revisá la consola del navegador
3. Creá un issue en GitHub con:
   - Descripción del problema
   - Pasos para reproducirlo
   - Logs relevantes

## 📝 Licencia

Ver archivo [LICENSE](LICENSE)

## 👥 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Creá tu feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la branch (`git push origin feature/AmazingFeature`)
5. Abrí un Pull Request

## 📞 Soporte

Para soporte y consultas:
- Email: soporte@tudominio.com
- Issues: GitHub Issues

## 🎯 Roadmap

- [x] Sistema de ventas básico
- [x] Gestión de inventario
- [x] Historia clínica
- [x] Facturación electrónica AFIP
- [ ] App móvil
- [ ] Integración con MercadoPago
- [ ] Multi-sucursal
- [ ] API REST

## ⚙️ Configuración Adicional

### Para Producción

1. Deshabilitar errores en pantalla:
```php
// conexion.php
ini_set('display_errors', 0);
error_reporting(0);
```

2. Configurar HTTPS
3. Proteger directorios sensibles
4. Realizar backups regulares
5. Monitorear logs

### Backup Automático

Configurá un cron job para backups diarios:

```bash
0 2 * * * mysqldump -u usuario -ppassword c2880275_ventas > /backup/optica_$(date +\%Y\%m\%d).sql
```

## 🔄 Actualizaciones

Para actualizar a la última versión:

```bash
git pull origin main
composer update
# Ejecutar scripts SQL de migración si los hay
```

---

**Desarrollado con ❤️ para facilitar la gestión de ópticas**

