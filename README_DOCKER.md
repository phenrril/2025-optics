# Sistema Óptica - Docker

Este sistema puede ejecutarse localmente usando Docker y Docker Compose.

## Requisitos Previos

- Docker Desktop instalado
- Docker Compose instalado

## Instrucciones de Instalación

### 1. Iniciar el sistema

Ejecuta el siguiente comando en la raíz del proyecto:

```bash
docker-compose up -d
```

Este comando:
- Creará y levantará el contenedor de MySQL con la base de datos
- Creará y levantará el contenedor de PHP/Apache
- Importará automáticamente el archivo SQL inicial

### 2. Acceder al sistema

Una vez que los contenedores estén corriendo, accede a:

```
http://localhost:8000
```

### 3. Credenciales de acceso

**Usuario por defecto (si ya existe en la BD):**
Consulta el archivo SQL para obtener las credenciales de usuario inicial, o crea uno nuevo mediante la base de datos.

**Configuración de base de datos en Docker:**
- Host: `db`
- Usuario: `opticauser`
- Contraseña: `Optica2024`
- Base de datos: `u375391241_sis_ventas`

## Comandos Útiles

### Ver logs de los contenedores
```bash
docker-compose logs -f
```

### Detener el sistema
```bash
docker-compose down
```

### Detener y eliminar volúmenes (resetear base de datos)
```bash
docker-compose down -v
```

### Reiniciar el sistema
```bash
docker-compose restart
```

### Acceder al contenedor de base de datos
```bash
docker exec -it optica_db mysql -u opticauser -p
```

### Acceder al contenedor web
```bash
docker exec -it optica_web bash
```

## Configuración

### Variables de entorno

Puedes modificar la configuración de la base de datos editando el archivo `docker-compose.yml`:

```yaml
environment:
  MYSQL_ROOT_PASSWORD: tu_password_root
  MYSQL_DATABASE: nombre_base_datos
  MYSQL_USER: tu_usuario
  MYSQL_PASSWORD: tu_password
```

### Volúmenes persistentes

Los datos de la base de datos se guardan en la carpeta `mysql-data` para que persistan entre reinicios.

## Solución de Problemas

### El sistema no se conecta a la base de datos

Verifica que el contenedor de base de datos esté saludable:
```bash
docker-compose ps
```

### Resetear completamente el sistema

```bash
docker-compose down -v
docker-compose up -d
```

### Ver logs de un servicio específico

```bash
docker-compose logs db
docker-compose logs web
```

## Estructura de puertos

- **8000**: Aplicación web (PHP/Apache)
- **3307**: Base de datos MySQL (mapeado para evitar conflictos con MySQL local)

## Notas

- El archivo SQL se importa automáticamente al crear el contenedor por primera vez
- Los cambios en archivos PHP se reflejan inmediatamente gracias al volumen compartido
- Para cambios en la estructura de la base de datos, necesitarás reiniciar con `-v` para resetear

