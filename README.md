# Capy Ventas

Sistema de punto de venta (POS) para gestión de inventario, ventas y créditos.

---

## Cambios realizados mediante IA

| Modelo | Fecha y Hora | Cambio realizado |
|--------|-------------|-----------------|
| GEMINI ANTIGRAVITY | 2026-03-12 23:26 | Fix responsivo en la leyenda de colores del inventario (`Views/App/POS/Inventory/inventory.php` L41-L66): se eliminó `fs-6` de los badges, se añadió `flex-wrap`, el separador `vr` se oculta en móvil con `d-none d-sm-inline`, y los umbrales de stock/vencimiento se muestran en línea separada en móvil usando `w-100 d-sm-none` con fuente reducida a 0.78rem. |
| GEMINI ANTIGRAVITY | 2026-03-12 23:27 | Fix adicional: se añadió `overflow-x: auto` y `flex-wrap: wrap` al contenedor `div.row` de la leyenda como capa de seguridad para pantallas muy pequeñas donde el contenido aún pudiera desbordarse. |
