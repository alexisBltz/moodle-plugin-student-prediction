import joblib
from sklearn.ensemble import RandomForestClassifier
import numpy as np
import pandas as pd

# Generación de datos aleatorios
np.random.seed(42)  # Para reproducibilidad
n_samples = 100  # Número de muestras

# Generación de datos aleatorios para las nuevas características
final_grades = np.random.randint(0, 101, size=n_samples)  # Calificaciones finales entre 0 y 100
total_assignments_submitted = np.random.randint(0, 20, size=n_samples)  # Total de tareas entregadas (0-20)
total_forum_discussions = np.random.randint(0, 10, size=n_samples)  # Total de discusiones en foros (0-10)
total_submitted = np.random.randint(0, total_assignments_submitted + 1)  # Total entregadas (0 hasta total de tareas)
avg_submission_time = np.random.randint(10, 601, size=n_samples)  # Tiempo promedio de entrega en segundos (10 a 600)

# Etiqueta que indica si aprobó (1) o no aprobó (0) basado en la calificación final
passed = (final_grades >= 70).astype(int)  # Aprobado si el puntaje es mayor o igual a 70

# Crear un DataFrame
data = pd.DataFrame({
    'final_grade': final_grades,
    'total_assignments_submitted': total_assignments_submitted,
    'total_forum_discussions': total_forum_discussions,
    'total_submitted': total_submitted,
    'avg_submission_time': avg_submission_time,
    'passed': passed
})

# Variables de entrada y salida
X = data[['final_grade', 'total_assignments_submitted',
          'total_forum_discussions', 'total_submitted',
          'avg_submission_time']].values
y = data['passed'].values

# Crear y entrenar el modelo
model = RandomForestClassifier(random_state=42)
model.fit(X, y)

# Guardar el modelo
joblib.dump(model, 'modelo.pkl')  # Guarda el modelo en un archivo
print("Modelo entrenado y guardado como 'modelo.pkl'.")

