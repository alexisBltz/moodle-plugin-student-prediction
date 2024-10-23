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
    # Procesar los datos (asegúrate de que estén en el formato correcto)
    predictions = []

    if 'students' in data:
        students = data['students']
        predictions = []
    for student in students:
        # Manejo de casos donde 'finalgrade' es None
        finalgrade = student['finalgrade']
        if finalgrade is None:
            finalgrade = 0  # o algún valor que tenga sentido en tu contexto

        # Segunda característica derivada: binaria si la nota es mayor a 70
        binary_feature = 1 if finalgrade > 70 else 0

        # Preparar los datos de entrada para la predicción
        input_data = np.array([[finalgrade, binary_feature]])

        # Hacer la predicción
        prediction = model.predict(input_data)
        predictions.append(int(prediction[0]))

    return jsonify(predictions)

if __name__ == '__main__':
    app.run(debug=True)
