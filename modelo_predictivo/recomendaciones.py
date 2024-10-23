import google.generativeai as genai
import textwrap
from IPython.display import Markdown
import sys
from dotenv import load_dotenv
import os

load_dotenv()

# Soporte para caracteres especiales en la terminal de Python
sys.stdout.reconfigure(encoding='utf-8')

# Configuramos la API KEY
GOOGLE_API_KEY = os.getenv('GOOGLE_API_KEY')
genai.configure(api_key=GOOGLE_API_KEY)

# Usamos el modelo generativo de la IA
modelo = genai.GenerativeModel('gemini-pro')

# Rebajamos el tamaño de la respuesta de la IA
def rebajar(text):
    text = text.replace('•', '  *')
    return Markdown(textwrap.indent(text, '> ', predicate=lambda _: True))

# Función para obtener recomendaciones del curso
def obtener_recomendaciones_curso(datos_curso):
    # Generamos el texto de solicitud basado en los datos del curso
    solicitud = f"Proporciona recomendaciones para mejorar el rendimiento en el curso basado en los siguientes datos: {datos_curso}, incluyendo la identificación de los estudiantes que necesitan ayuda adicional y las áreas de contenido que requieren más atención."
    solicitud += "Ademas proporciona links en español a recursos adicionales que puedan ser útiles para los estudiantes deben ser links accesibles actualmente pueden ser videos a youtube tambien."
    # Obtenemos la respuesta del modelo generativo
    respuesta = modelo.generate_content(solicitud)
    respuesta_texto = respuesta.text

    # Imprimimos la respuesta formateada
    print(respuesta_texto)

# Ejemplo de datos del curso
datos_curso = {
    "curso_id": "MAT101",
    "curso_nombre": "Matemáticas I",
    "tema": "Introduccion a la algebra",
    "estudiantes": [
        {"id": "123", "nombre": "Jose Martinez", "nota": "B", "interacciones_quiz": 5, "respuestas_foro": 3},
        {"id": "456","nombre": "Jose Dominguez", "nota": "C", "interacciones_quiz": 2, "respuestas_foro": 1},
    ]
}

# Obtenemos las recomendaciones para el curso
obtener_recomendaciones_curso(datos_curso)
