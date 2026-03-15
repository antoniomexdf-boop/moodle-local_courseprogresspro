<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

$string['pluginname'] = 'Progreso de Curso Pro';
$string['progresslabel'] = 'Tu avance en el curso';
$string['progresssummary'] = 'Completado: {$a}%';
$string['pendingcountsummary'] = 'Lo que te falta: {$a} acciones pendientes';
$string['progressbarlabel'] = 'Progreso del curso: {$a}%';
$string['settingsgeneral'] = 'Configuración general';
$string['settingsenabled'] = 'Habilitar plugin';
$string['settingsenabled_desc'] = 'Si está deshabilitado, la interfaz de progreso no se muestra al estudiante.';
$string['settingsmainprogresssource'] = 'Fuente principal del progreso';
$string['settingsmainprogresssource_desc'] = 'Define si la barra principal sigue la finalización oficial de Moodle o el cálculo personalizado del plugin.';
$string['settingsmainprogresssource_moodle'] = 'Finalización del curso en Moodle';
$string['settingsmainprogresssource_custom'] = 'Progreso personalizado del plugin';
$string['settingscountresources'] = 'Contar recursos';
$string['settingscountresources_desc'] = 'Incluye recursos como página, URL, archivo, libro y carpeta en el cálculo del progreso.';
$string['settingsresourcesrequirecompletion'] = 'Requerir finalización para recursos';
$string['settingsresourcesrequirecompletion_desc'] = 'Cuando está habilitado, los recursos solo cuentan si la actividad tiene configurado el seguimiento de finalización de Moodle. Esto evita usar detección basada en logs.';
$string['settingsquizmode'] = 'Modo de cálculo para cuestionarios';
$string['settingsquizmode_desc'] = 'Define si un cuestionario cuenta por preguntas respondidas o como una sola actividad.';
$string['settingsquizmode_questions'] = 'Contar por preguntas';
$string['settingsquizmode_activity'] = 'Contar como una actividad';
$string['settingsshowpercentage'] = 'Mostrar porcentaje numérico';
$string['settingsshowpercentage_desc'] = 'Muestra el porcentaje actual de avance junto a la barra.';
$string['settingsshowpendingbutton'] = 'Mostrar botón de acciones pendientes';
$string['settingsshowpendingbutton_desc'] = 'Muestra un botón que abre una ventana con el listado de acciones pendientes.';
$string['settingsheadertext'] = 'Texto del encabezado';
$string['settingsheadertext_desc'] = 'Texto que se muestra arriba de la barra.';
$string['pendingbuttonlabel'] = 'Ver lo que te falta';
$string['pendingactivities'] = 'Te faltan {$a} actividades en este curso';
$string['allactivitiesdone'] = 'Estas al dia en este curso';
$string['pendingmodaltitle'] = 'Lo que te falta en este curso';
$string['closemodal'] = 'Cerrar ventana';
$string['nopendingitems'] = 'No hay actividades pendientes por ahora.';
$string['noactivitiesdetected'] = 'No se detectaron actividades visibles en este curso.';
$string['pendingactivityitem'] = '{$a->name}';
$string['pendingquizitem'] = '{$a->name} (cuestionario en progreso: {$a->count} preguntas por responder)';
$string['pendingstatusavailable'] = 'Disponible ahora';
$string['pendingstatuslocked'] = 'Aun no disponible';
$string['pendingopenactivity'] = 'Abrir actividad';
$string['pendinggotosection'] = 'Ir a seccion';
$string['privacy:metadata'] = 'El plugin local_courseprogresspro no almacena datos personales.';
