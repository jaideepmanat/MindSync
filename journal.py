from flask import Flask, request, jsonify
import torch
from transformers import BertTokenizer, BertForSequenceClassification
import os
import logging
from flask_cors import CORS
import speech_recognition as sr
from pydub import AudioSegment

# Set up logging
logging.basicConfig(level=logging.DEBUG, format="%(asctime)s - %(levelname)s - %(message)s")

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Enable CORS
print("Flask app initialized.")

# Load BERT model
MODEL_PATH = "./fine_tuned_bert"
print(f"Loading BERT model from {MODEL_PATH}...")

try:
    tokenizer = BertTokenizer.from_pretrained(MODEL_PATH)
    model = BertForSequenceClassification.from_pretrained(MODEL_PATH)
    model.eval()
    print("BERT model loaded successfully.")
except Exception as e:
    print(f"Error loading BERT model: {e}")

# Emotion labels
LABEL_MAP = {0: "Happy", 1: "Sad", 2: "Frustrated", 3: "Anxious"}

# Function to predict emotion from text
def predict_text_emotion(text):
    print(f"Predicting emotion for text: {text}")
    inputs = tokenizer(text, return_tensors="pt", truncation=True, padding="max_length", max_length=128)
    with torch.no_grad():
        outputs = model(**inputs)
    prediction = torch.argmax(outputs.logits, dim=-1).item()
    predicted_emotion = LABEL_MAP.get(prediction, "Unknown")
    print(f"Predicted emotion: {predicted_emotion}")
    return predicted_emotion

# Convert WebM to WAV
def convert_webm_to_wav(webm_path, wav_path):
    try:
        print(f"Converting {webm_path} to WAV format...")
        audio = AudioSegment.from_file(webm_path, format="webm")
        audio = audio.set_channels(1).set_frame_rate(16000)  # Convert to mono & 16kHz
        audio.export(wav_path, format="wav")
        print(f"Conversion successful: {wav_path}")
        return wav_path
    except Exception as e:
        print(f"Error converting WebM to WAV: {e}")
        return None

# Function to transcribe audio using Google Speech-to-Text API
def transcribe_audio(audio_path):
    print(f"Transcribing audio from: {audio_path}")
    recognizer = sr.Recognizer()

    try:
        with sr.AudioFile(audio_path) as source:
            audio = recognizer.record(source)
        transcribed_text = recognizer.recognize_google(audio)
        print(f"Transcribed text: {transcribed_text}")
        return transcribed_text
    except sr.UnknownValueError:
        print("Google Speech-to-Text could not understand the audio.")
        return ""
    except sr.RequestError as e:
        print(f"Google Speech-to-Text API request failed: {e}")
        return ""

@app.route("/predict_audio", methods=["POST"])
def predict_audio():
    try:
        print("Received audio prediction request.")

        if "audio" not in request.files:
            print("No audio file provided in request.")
            return jsonify({"error": "No audio file provided."}), 400

        audio_file = request.files["audio"]
        os.makedirs("uploads", exist_ok=True)
        webm_path = os.path.join("uploads", "journal_audio.webm")
        wav_path = os.path.join("uploads", "journal_audio.wav")

        audio_file.save(webm_path)
        print(f"Audio file saved at: {webm_path}")

        # Convert WebM to WAV
        converted_wav = convert_webm_to_wav(webm_path, wav_path)
        if not converted_wav:
            return jsonify({"error": "Failed to convert WebM to WAV."}), 500

        # Transcribe the audio
        transcribed_text = transcribe_audio(converted_wav)

        if not transcribed_text:
            print("No transcribed text from audio.")
            return jsonify({"error": "Could not transcribe audio."}), 400

        # Predict emotion from transcribed text
        predicted_emotion = predict_text_emotion(transcribed_text)

        return jsonify({
            "predicted_emotion": predicted_emotion,
            "transcribed_text": transcribed_text
        })

    except Exception as e:
        print(f"Error processing audio: {e}")
        return jsonify({"error": str(e)}), 500

@app.route("/predict_text", methods=["POST"])
def predict_text():
    try:
        print("Received text prediction request")
        data = request.json
        print("Request Data:", data)

        text = data.get("text", "").strip()
        if not text:
            print("No text provided.")
            return jsonify({"error": "No text provided."}), 400

        predicted_emotion = predict_text_emotion(text)

        print("Predicted Emotion:", predicted_emotion)
        return jsonify({"predicted_emotion": predicted_emotion})

    except Exception as e:
        print("Error in /predict_text:", str(e))
        return jsonify({"error": str(e)}), 500

if __name__ == "__main__":
    print("Starting Flask server...")
    app.run(debug=True)