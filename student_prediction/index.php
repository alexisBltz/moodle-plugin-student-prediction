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

/**
 * Página principal del plugin de predicción de deserción estudiantil
 *
 * @package    local_student_prediction
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/local/student_prediction/index.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();

// Notificación de bienvenida
\core\notification::add('Bienvenido al plugin de predicción de deserción estudiantil', 'success');

// Obtener datos de los estudiantes (nombres y notas)
$students = $DB->get_records_sql('
    SELECT u.id, u.firstname, u.lastname, g.finalgrade
    FROM {user} u
    LEFT JOIN {grade_grades} g ON u.id = g.userid
');

// Preparar los datos para enviar al modelo en Python
$data_to_send = [];
if ($students) {
echo '<table border="1">
        <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Nota Final</th>
        </tr>';
foreach ($students as $student) {
        echo '<tr>
            <td>' . $student->firstname . '</td>
            <td>' . $student->lastname . '</td>
            <td>' . (!is_null($student->finalgrade) ? round($student->finalgrade, 2) : 'Sin nota') . '</td>
        </tr>';
    }
    
    echo '</table>';
    foreach ($students as $student) {
        $data_to_send[] = [
            'firstname' => $student->firstname,
            'lastname' => $student->lastname,
            'finalgrade' => !is_null($student->finalgrade) ? round($student->finalgrade, 2) : null,
        ];
    }

    // Enviar datos al modelo en Python
    $url = 'http://127.0.0.1:5000/predict'; // Cambia esto a la URL de tu servidor Flask
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode(['students' => $data_to_send]), // Asegúrate de enviar una lista bajo la clave 'students'
        ],
    ];
    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context); // Uso de @ para suprimir errores
    
    // Manejo de la respuesta del modelo
    if ($result === FALSE) {
        echo 'Error al enviar datos al modelo.';
    } else {
        $predictions = json_decode($result, true);
        // Mostrar los datos en una tabla
        echo '<table border="1">
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Nota Final</th>
                <th>Predicción</th>
            </tr>';
        
        foreach ($students as $index => $student) {
            echo '<tr>
                <td>' . $student->firstname . '</td>
                <td>' . $student->lastname . '</td>
                <td>' . (!is_null($student->finalgrade) ? round($student->finalgrade, 2) : 'Sin nota') . '</td>
                <td>' . (isset($predictions[$index]) ? $predictions[$index] : 'Sin predicción') . '</td>
            </tr>';
        }
        
        echo '</table>';
    }
} else {
    echo 'No se encontraron registros de estudiantes.';
}

echo $OUTPUT->footer();
