# Course Progress Pro - Manual de Usuario y Administrador (Español)

## Resumen

`local_courseprogresspro` ofrece una interfaz avanzada de progreso en páginas de curso.

## Repositorio

- GitHub: https://github.com/antoniomexdf-boop/moodle-local_courseprogresspro

## Autor y Contacto

- Autor: Jesus Antonio Jimenez Avina
- Email: antoniomexdf@gmail.com
- Email (secundario): antoniojamx@gmail.com

## Funciones

- Barra de progreso y porcentaje
- Fuente principal del progreso alineada con Moodle por defecto
- Resumen de acciones pendientes
- Botón de acciones pendientes
- Timeline/modal de pendientes con enlaces y estado de disponibilidad
- Timeline de pendientes con ligas directas visibles y fallback a seccion
- Los cuestionarios aparecen como una sola accion pendiente con detalle de preguntas por responder
- Modo de conteo de recursos con finalización
- Habilitar/deshabilitar plugin globalmente

## Experiencia del Estudiante

El estudiante ve una interfaz de progreso generica; las etiquetas de edicion no se muestran en la UI del curso.
La barra principal sigue por defecto el progreso oficial del curso en Moodle.
Los pendientes se muestran como lo que falta por completar y no como un segundo porcentaje en competencia.
El resumen de pendientes se integra dentro del boton para una vista mas limpia.

## Requisitos

- Moodle 4.1+

## Instalación

1. Copia el plugin en `moodle/local/courseprogresspro`.
2. Completa la instalación como administrador.
3. Purga cachés.

## Configuración

Ruta:
`Administración del sitio > Plugins > Plugins locales > Progreso de Curso Pro`

Ajustes disponibles:

- Habilitar plugin
- Fuente principal del progreso
- Contar recursos
- Requerir finalización para recursos
- Modo de cálculo de cuestionarios
- Mostrar porcentaje numérico
- Mostrar botón de acciones pendientes
- Texto del encabezado

## Capturas

![Barra de Progreso Pro](screenshots/courseprogresspro_01.png)
![Barra de Progreso Pro - Vista Alterna](screenshots/courseprogresspro_02.png)
![Timeline de Pendientes Pro](screenshots/courseprogresspro_03.png)

## Privacidad

No se almacenan datos personales.

## Versión

- Release: `0.7.3`
- Version: `2026031405`
