# 🐛 Bugs Detectados en ventas.php y ajax.php

## CRÍTICOS - VULNERABILIDADES DE SEGURIDAD

### 1. SQL Injection - MUY GRAVE ⚠️
**Archivos afectados:** `src/ajax.php` (TODO el archivo)
**Problema:** Todas las variables de $_GET y $_POST se insertan directamente en queries sin sanitizar
**Líneas afectadas:** 8, 23, 37, 51, 72-92, 101-132, 152-165, 182, 230-250

**Ejemplos:**
```php
$nombre = $_GET['q'];
$cliente = mysqli_query($conexion, "SELECT * FROM cliente WHERE nombre LIKE '%$nombre%'");
```

**Solución:** Usar `mysqli_real_escape_string()` o prepared statements

---

### 2. Falta de validación de stock ⚠️
**Archivo:** `src/ajax.php` línea 102-112
**Problema:** No verifica si hay suficiente stock antes de procesar la venta
**Riesgo:** Ventas con productos sin stock, números negativos en BD

**Ejemplo:**
```php
$stockTotal = $stockNuevo['existencia'] - $cantidad;
// No verifica si stockTotal es negativo
```

---

### 3. Inserción duplicada en postpagos ⚠️
**Archivo:** `src/ajax.php` línea 108
**Problema:** Se inserta en tabla postpagos POR CADA PRODUCTO de la venta
**Riesgo:** Datos duplicados, inconsistencias en postpagos

**Código actual:**
```php
while ($row = mysqli_fetch_assoc($consultaDetalle)) {
    // ... inserta detalle venta
    $postpagos = mysqli_query($conexion, "INSERT INTO postpagos(...)"); // ❌ DENTRO del loop
}
```

**Solución:** Mover insert de postpagos FUERA del loop

---

### 4. Falta de transacciones ⚠️
**Archivo:** `src/ajax.php` líneas 92-138
**Problema:** No usa transacciones SQL, si algo falla queda inconsistente
**Riesgo:** Venta parcial registrada, stock desactualizado, dinero perdido

**Escenario:**
- Se inserta venta en BD
- Se inserta detalle_venta
- Falla al actualizar stock
- Resultado: Venta registrada pero sin descontar stock

---

### 5. Validación insuficiente de montos ⚠️
**Archivos:** `src/ajax.php` línea 88-90, `assets/js/funciones.js` línea 93-101
**Problema:** Valida que abona > total pero no valida valores negativos ni nulls
**Riesgo:** Valores negativos o null en base de datos

---

### 6. Información sensible expuesta ⚠️
**Archivo:** `src/ajax.php` línea 89, 141
**Problema:** Usa `die()` sin mensaje cuando falla validación
**Riesgo:** No informa error al usuario

---

## MEDIOS - LÓGICA Y DATOS

### 7. Lógica de cálculo de descuento incorrecta
**Archivo:** `src/ajax.php` línea 86
**Problema:** Aplica descuento al total MENOS obra social, pero no debería
**Código:**
```php
$total = (($result['total_pagar'] - $obrasocial) * $descuento);
```

**Debería ser:**
```php
$subtotal = $result['total_pagar'] - $obrasocial;
$total = $subtotal * $descuento;
$total_con_obra = $total; // Ya está descontado
```

---

### 8. Cálculo de total por producto incorrecto en detalle
**Archivo:** `assets/js/funciones.js` línea 389-392
**Problema:** En calculadora (línea 398) usa `.value` en lugar de `.textContent` para total
**Código:**
```javascript
document.querySelector("#btn_parcial").addEventListener("click", function (total) {
    // 'total' es el parámetro pero se compara con 'total.value' que no existe
```

---

### 9. Missing index 'id' en respuesta JSON
**Archivo:** `src/ajax.php` línea 138
**Problema:** Devuelve 'id_cliente' y 'id_venta' pero JavaScript espera 'id' (línea 117)
**Riesgo:** JS no puede parsear la respuesta correctamente

---

### 10. Validación de sesión incorrecta
**Archivo:** `src/ajax.php` línea 3
**Problema:** Incluye session_start() pero no valida que exista sesión activa
**Riesgo:** Acceso sin autenticación

---

## MENORES - FORMATO E INTERFAZ

### 11. Typos en HTML
- **Archivo:** `src/ventas.php` línea 36
- **Problema:** "Dirreción" debería ser "Dirección"
- **Archivo:** `src/ventas.php` línea 59
- **Problema:** "Graduacion" debería ser "Graduación"

### 12. Campos disabled pero required
**Archivo:** `src/ventas.php` líneas 31, 37, 43
**Problema:** Campos con `disabled` y `required` no funcionan en HTML5

### 13. Event listener duplicado en calcular()
**Archivo:** `assets/js/funciones.js` línea 398-443
**Problema:** Se crea un listener cada vez que se llama calcular(), acumula listeners

### 14. Form anidados inválidos
**Archivo:** `src/ventas.php` líneas 65, 153
**Problema:** Form dentro de otro form es HTML inválido

---

## RESUMEN DE PRIORIDADES

🔴 **CRÍTICO - Arreglar inmediatamente:**
1. SQL Injection en TODO ajax.php
2. Falta de transacciones
3. Falta validación de stock

🟡 **IMPORTANTE - Arreglar pronto:**
4. Inserción duplicada postpagos
5. Validación de montos
6. Error en cálculo de descuento

🟢 **MENOR - Mejorar cuando sea posible:**
7. Typos
8. Campos disabled
9. Event listeners duplicados
10. Forms anidados

