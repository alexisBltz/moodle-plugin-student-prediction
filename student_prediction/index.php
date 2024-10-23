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
require(__DIR__ .'/classes/student_service.php');
require(__DIR__ .'/classes/api_client.php');

require_login();


// Configuración de la página
$url = new moodle_url('/local/student_prediction/index.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();

// Notificación de bienvenida
\core\notification::add('Bienvenido al plugin de predicción de deserción estudiantil', 'success');

// Obtener datos de los estudiantes
$student_service = new \local_student_prediction\student_service();
$students = $student_service->get_students();

$data_to_send = [];
foreach ($students as $student) {
    $data_to_send[] = [
        'firstname' => $student->firstname,
        'lastname' => $student->lastname,
        'finalgrade' => !is_null($student->finalgrade) ? round($student->finalgrade, 2) : null,
        'total_assignments_submitted' => $student->total_assignments_submitted, // Nuevo campo
        'total_forum_discussions' => $student->total_forum_discussions, // Nuevo campo
        'total_submitted' => $student->total_submitted, // Nuevo campo
        'avg_submission_time' => intval($student->avg_submission_time) // Nuevo campo
    ];
}

// Enviar datos al modelo en Python
$api_client = new \local_student_prediction\api_client('http://127.0.0.1:5000/predict');
try {
    $predictions = $api_client->send_data($data_to_send);
    //print_r($predictions);
} catch (Exception $e) {
    echo $e->getMessage();
}

$students_with_index = [];
foreach ($students as $index => $student) {
    $students_with_index[] = [
        'index' => $index, // Asignamos el índice para luego usarlo en la plantilla
        'firstname' => $student->firstname,
        'lastname' => $student->lastname,
        'finalgrade' => !is_null($student->finalgrade) ? round($student->finalgrade, 2) : 'Sin nota',
        'total_assignments_submitted' => $student->total_assignments_submitted, 
        'total_forum_discussions' => $student->total_forum_discussions, 
        'total_submitted' => $student->total_submitted, 
        'avg_submission_time' => intval($student->avg_submission_time), 
        'prediction' => isset($predictions[$index]) ? $predictions[$index] : null
    ];
}
// Renderizar la plantilla
$templatecontext = [
    'students' => $students_with_index,
    'predictions' => $predictions,
];
echo '<pre>';
print_r($students);
print_r($predictions);
echo '</pre>';
var_dump($predictions);
echo $OUTPUT->render_from_template('local_student_prediction/index', $templatecontext);

echo $OUTPUT->footer();
