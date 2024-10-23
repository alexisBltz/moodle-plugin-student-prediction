import joblib
from sklearn.ensemble import RandomForestClassifier
import numpy as np

# Datos de ejemplo
X = np.array([[85, 1], [70, 0], [90, 1], [60, 0], [75, 1]])
y = np.array([1, 0, 1, 0, 1])

# Crear y entrenar el modelo
model = RandomForestClassifier()
model.fit(X, y)

# Guardar el modelo
joblib.dump(model, 'modelo.pkl')  # Guarda el modelo en un archivo
print("Modelo entrenado y guardado como 'modelo.pkl'.")
