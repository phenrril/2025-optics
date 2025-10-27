# 🚀 Inicio Rápido - Sistema Óptica

## Pasos para iniciar el sistema

### 1. Verificar requisitos
```bash
docker --version
docker-compose --version
```

### 2. Levantar el sistema
```bash
docker-compose up -d
```

### 3. Verificar que esté corriendo
```bash
docker-compose ps
```

Deberías ver dos contenedores:
- `optica_db` (MySQL)
- `optica_web` (PHP/Apache)

### 4. Acceder al sistema
Abre tu navegador en: **http://localhost:8000**

---

## 🔥 Comandos Esenciales

```bash
# Iniciar
docker-compose up -d

# Detener
docker-compose down

# Ver logs
docker-compose logs -f

# Reiniciar
docker-compose restart

# Resetear todo (CUIDADO: borra datos)
docker-compose down -v
docker-compose up -d
```

---

## ⚠️ Solución de Problemas

**Error de conexión a la base de datos:**
```bash
docker-compose down
docker-compose up -d
# Esperar 30 segundos para que MySQL se inicialice
```

**Puerto 8000 ya en uso:**
Edita `docker-compose.yml` y cambia `"8000:80"` por `"8080:80"` (o el puerto que prefieras)

**Error al importar SQL:**
```bash
# Ver logs de MySQL
docker-compose logs db

# Acceder manualmente al contenedor
docker exec -it optica_db bash
mysql -u opticauser -p
# Contraseña: Optica2024
```

---

## 📝 Credenciales

**Base de datos:**
- Host: `db` (dentro de Docker) o `localhost:3307` (desde el host)
- Usuario: `opticauser`
- Contraseña: `Optica2024`
- Base de datos: `u375391241_sis_ventas`

**Aplicación web:**
- URL: http://localhost:8000
- Las credenciales de usuario dependen de lo que haya en la base de datos

---

## 🔧 Desarrollo

Los archivos PHP están montados como volumen, por lo que los cambios se reflejan inmediatamente.

Para reconstruir la imagen después de cambios en el Dockerfile:
```bash
docker-compose build
docker-compose up -d
```

