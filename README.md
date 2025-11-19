# sis-roles
Sistema de gestión de predios
=====================================
Esta aplicacion es para gestionar los roles de los usuarios, los permisos, faltan modulos por construir.
#Modulos que faltan:
-recuperar contraseña
-gestion de notificaciones

#Usuario y contraseña por defecto:
-usuario: administrador
-contraseña: administrador
#Modulo a construir:
-gestion de notificaciones
-gestion de recuperacion de contraseña

#Librerias que deberian habilitarse
-extension=gd
-extension=intl

#El sistema soporte la version de php 8.2 arriba
#Es necesario crear las siguientes carpetas dentro del sistema en la carpeta Storage
->data
 |->root

## Registro de cambios recientes
- CHATGPT - 2025-11-17 04:30 UTC - Modal POS para registrar nuevos negocios, listarlos en el selector y activar el negocio elegido desde el panel lateral.
- CHATGPT - 2025-11-17 03:54 UTC - Botones de categorías populares dinámicos en ventas POS con filtro "Todos" y top 5 por ventas.
- CHATGPT - 2025-11-17 03:44 UTC - Búsqueda en la sección de productos de ventas POS por nombre, proveedor, categoría y datos clave.
- CHATGPT - 2025-11-17 03:34 UTC - Descuento de stock al registrar ventas POS permitiendo inventario negativo cuando no hay unidades disponibles.
- CHATGPT - 2025-11-17 03:27 UTC - Botón para guardar el nombre del voucher generado tras finalizar venta POS.
- CHATGPT - 2025-11-17 03:17 UTC - Reubicación de los modales de cobro y voucher antes del pie de página para activar el botón Finalizar venta.
- CHATGPT - 2025-11-17 02:58 UTC - Registro de ventas en voucher_header y voucher_detail al finalizar ventas POS.
- CHATGPT - 2025-11-17 02:46 UTC - Apertura automática del modal de comprobante al finalizar la venta en POS.
- CHATGPT - 2025-11-17 02:40 UTC - Carga automática de clientes del negocio en el selector de ventas POS.
- CHATGPT - 2025-11-17 02:28 UTC - Unificación de alturas en las tarjetas de productos de ventas POS para alinear todas según el tamaño mayor.
- CHATGPT - 2025-11-17 02:13 UTC - Edición manual de cantidades en canasta POS con eliminación al ingresar cero y respeto de stock.
- CHATGPT - 2025-11-17 02:06 UTC - Se limpian los indicadores de productos al eliminar o vaciar la canasta en ventas POS.
- CHATGPT - 2025-11-17 01:51 UTC - Canasta de ventas POS con control de cantidades, eliminación, bloqueo de precios y vaciado rápido.
- CHATGPT - 2025-11-16 14:49 UTC - Transiciones suaves entre pasos POS y contador visible de productos seleccionados en la grilla de ventas.
- CHATGPT - 2025-11-16 14:38 UTC - Se fijaron las tarjetas de ventas POS a la altura de la pantalla usando overflow interno para los listados.
- CHATGPT - 2025-11-16 14:32 UTC - Se agregó desplazamiento interno a las tarjetas de ventas POS para mantener visibles los botones de navegación en las tres secciones.
- CHATGPT - 2025-11-16 04:45 UTC - Mejora del flujo de ventas POS con transición escritorio entre canasta y pago, alturas unificadas y datos de demostración ampliados.
- CHATGPT - 2025-11-14 22:40 UTC - Ajuste de los estilos del login POS para emplear las variables de color definidas en el main.css y mantener la coherencia cromática.
- CHATGPT - 2025-11-14 22:15 UTC - Eliminación de llamadas a registerLog en el sistema POS para evitar registros innecesarios en las operaciones de clientes, productos y proveedores.
- CHATGPT - 2025-11-14 21:51 UTC - Ajuste del menú móvil del home para apilar enlaces y botones con fondo propio evitando traslape sobre la sección principal en pantallas pequeñas.
- CHATGPT - 2025-11-14 21:31 UTC - Optimización del home con menú móvil y ajustes de estilos responsivos para evitar rupturas en pantallas pequeñas.
- Equipo Capy Ventas - 2025-11-13 16:14 UTC - Actualización de la página de inicio con la sección de planes y precios en soles.
- Equipo Capy Ventas - 2025-11-13 17:30 UTC - Vinculación de la sección de planes con la base de datos para mostrar precios y periodos reales.
- Equipo Capy Ventas - 2025-11-13 16:38 UTC - Se reemplazó la carga dinámica por una versión estática en HTML, CSS y JS, segmentando los planes mensuales y anuales con un nuevo selector interactivo.
- Equipo Capy Ventas - 2025-11-13 16:47 UTC - Se simplificó la sección de planes del home para mostrar solo los nombres con botones de contacto vía WhatsApp por modalidad mensual y anual.
- Equipo Capy Ventas - 2025-11-13 16:58 UTC - Se refinó la sección de planes del home con un diseño más visual, agrupando mensualidades y anualidades en tarjetas estilizadas y retirando el plan Basic Anual.
- Equipo Capy Ventas - 2025-11-13 17:04 UTC - Se añadieron botones de acceso para clientes y se actualizaron los enlaces de WhatsApp para crear cuenta o solicitar demos según cada plan.
- Equipo Capy Ventas - 2025-11-13 17:18 UTC - Se reubicó el acceso "Soy cliente" al encabezado y se mantuvieron los planes solo con acciones hacia WhatsApp.
- Equipo Capy Ventas - 2025-11-13 17:20 UTC - Se actualizó el home con mensajes de inicio gratis en CTA de WhatsApp y se incorporó la sección de clientes destacados.
- Equipo Capy Ventas - 2025-11-13 17:35 UTC - Se realzó el home para resaltar la creación y la administración de diferentes negocios en toda la plataforma.

