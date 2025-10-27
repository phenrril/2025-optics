@echo off
echo ========================================
echo  Sistema Optica - Iniciando Docker
echo ========================================
echo.

REM Verificar si Docker está corriendo
docker info >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Docker no está corriendo. Por favor, inicia Docker Desktop.
    pause
    exit /b 1
)

echo [1/4] Construyendo contenedores...
docker-compose build

echo [2/4] Iniciando contenedores...
docker-compose up -d

echo.
echo [3/4] Esperando a que MySQL se inicialice...
timeout /t 10 /nobreak >nul

echo.
echo [4/4] Verificando estado...
docker-compose ps

echo.
echo ========================================
echo  Sistema iniciado correctamente!
echo  Accede a: http://localhost:8000
echo ========================================
echo.
echo Para ver logs: docker-compose logs -f
echo Para detener: docker-compose down
echo.
pause

