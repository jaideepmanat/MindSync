from flask import Flask, request, jsonify
import pandas as pd
import speech_recognition as sr
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.preprocessing import LabelEncoder
from sklearn.svm import SVC
from flask_cors import CORS, cross_origin
import os
import traceback
import ffmpeg

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": ["http://localhost", "http://127.0.0.1"]}})

# Ensure the audio directory exists
AUDIO_DIR = "audio"
os.makedirs(AUDIO_DIR, exist_ok=True)

# Load dataset and train model
def load_and_train_model():
    try:
        print("Loading dataset...")
        df = pd.read_csv('Mindsync_journal_balanced.csv')
        df.drop(columns=['Timestamp'], inplace=True)
        
        # Encode target labels
        print("Encoding target labels...")
        label_encoder = LabelEncoder()
        df['Emotion'] = label_encoder.fit_transform(df['Emotion'])

        # Split data
        print("Splitting dataset...")
        X_train, X_test, y_train, y_test = train_test_split(
            df['How do you feel today?'].astype(str), df['Emotion'], test_size=0.2, random_state=42
        )

        # Convert text to numerical data using TF-IDF
        print("Applying TF-IDF vectorization...")
        vectorizer = TfidfVectorizer()
        X_train_tfidf = vectorizer.fit_transform(X_train)

        # Train SVM model
        print("Training SVM model...")
        model = SVC(kernel='linear', random_state=42)
        model.fit(X_train_tfidf, y_train)
        print("Model training complete.")

        return model, vectorizer, label_encoder
    except Exception as e:
        print(f"Error loading dataset or training model: {e}")
        traceback.print_exc()
        return None, None, None

# Initialize model
model, vectorizer, label_encoder = load_and_train_model()

def predict_emotion(text):
    if model and vectorizer and label_encoder:
        text_tfidf = vectorizer.transform([text])
        prediction = model.predict(text_tfidf)
        emotion = label_encoder.inverse_transform(prediction)[0]
        print(f"Predicted emotion for '{text}': {emotion}")
        return emotion
    return "Error: Model not initialized"

# ==================== 🔹 PREDICT TEXT ROUTE 🔹 ====================
@app.route('/predict_text', methods=['OPTIONS', 'POST'])
@cross_origin()
def predict_text():
    if request.method == 'OPTIONS':
        return jsonify({'message': 'CORS preflight successful'}), 200  # ✅ Handle CORS preflight

    try:
        data = request.get_json()
        text = data.get('text', '').strip()

        if not text:
            return jsonify({"error": "No text provided"}), 400

        predicted_emotion = predict_emotion(text)
        return jsonify({"text": text, "predicted_emotion": predicted_emotion})

    except Exception as e:
        traceback.print_exc()
        return jsonify({"error": f"Error processing text: {e}"}), 500

# ==================== 🔹 PREDICT AUDIO ROUTE 🔹 ====================
@app.route('/predict_audio', methods=['OPTIONS', 'POST'])
@cross_origin()
def predict_audio():
    if request.method == 'OPTIONS':
        return jsonify({'message': 'CORS preflight successful'}), 200  # ✅ Handle CORS preflight

    if 'audio' not in request.files:
        return jsonify({"error": "No audio file provided"}), 400

    audio_file = request.files['audio']
    filename = os.path.join(AUDIO_DIR, "uploaded_audio.wav")
    audio_file.save(filename)
    print(f"Audio file saved: {filename}")

    try:
        converted_audio = os.path.join(AUDIO_DIR, "converted_audio.wav")
        print("Converting audio using FFmpeg...")
        ffmpeg.input(filename).output(converted_audio, format="wav").run(overwrite_output=True)

        recognizer = sr.Recognizer()
        with sr.AudioFile(converted_audio) as source:
            audio_data = recognizer.record(source)
            text = recognizer.recognize_google(audio_data)
        print(f"Recognized text: {text}")

        predicted_emotion = predict_emotion(text)
        return jsonify({"text": text, "predicted_emotion": predicted_emotion})
    
    except sr.UnknownValueError:
        return jsonify({"error": "Could not understand the audio"}), 400
    except sr.RequestError as e:
        return jsonify({"error": f"Speech recognition service error: {e}"}), 500
    except Exception as e:
        traceback.print_exc()
        return jsonify({"error": f"Error processing audio file: {e}"}), 500

# ==================== 🔹 RUN FLASK APP 🔹 ====================
if __name__ == '__main__':
    print("Starting Flask server...")
    app.run(debug=True)
