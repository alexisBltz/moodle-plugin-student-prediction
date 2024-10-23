from flask import Flask, request, jsonify
import numpy as np
import joblib  # Para cargar el modelo guardado
import os

app = Flask(__name__)

# Cargar tu modelo de predicción (ajusta la ruta a tu modelo)
model_path = os.path.join(os.path.dirname(__file__), 'modelo.pkl')
model = joblib.load(model_path)

@app.route('/predict', methods=['POST'])
def predict():
    # Obtener datos JSON de la solicitud
    data = request.get_json()
    print(data)

    # Procesar los datos
    predictions = []

    if 'students' in data:
        students = data['students']
        for student in students:
            # Manejo de casos donde 'finalgrade' es None
            finalgrade = student.get('finalgrade', 0)  # Usa 0 si no hay calificación final
            total_assignments_submitted = student.get('total_assignments_submitted', 0)
            total_forum_discussions = student.get('total_forum_discussions', 0)
            total_submitted = student.get('total_submitted', 0)
            avg_submission_time = student.get('avg_submission_time', 0)

            # Preparar los datos de entrada para la predicción
            input_data = np.array([[finalgrade, total_assignments_submitted,
                                    total_forum_discussions, total_submitted,
                                    avg_submission_time]])

            # Hacer la predicción
            prediction = model.predict(input_data)
            predictions.append(int(prediction[0]))

    return jsonify(predictions)

if __name__ == '__main__':
    app.run(debug=True)
