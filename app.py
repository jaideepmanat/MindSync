from flask import Flask, request, jsonify
import pickle
import numpy as np

# Initialize Flask app
app = Flask(__name__)

# Load the saved model
with open('voting_classifier_model.pkl', 'rb') as f:
    model = pickle.load(f)

# Define categorical mappings
gender_map = {
    "Female": 0,
    "Male": 1,
    "Other": 2,
    "Prefer not to say": 3
}

yes_no_map = {
    "Yes": 1,
    "No": 0
}

# Define risk level mapping for prediction output
risk_level_map = {
    0: "High",
    1: "Low",
    2: "Mid"
}

# Define the predict route
@app.route('/predict', methods=['POST'])
def predict():
    # Get JSON data from the POST request
    data = request.json
    print("Received data:", data)  # Debugging line to print incoming JSON

    # Ensure all expected keys are present in `data` and convert categorical values
    try:
        features = [
            data['Age'],
            gender_map.get(data['gender'], 3),  # Default to "Prefer not to say" if not found
            data['How often do you feel stressed?'],
            data['How often have you felt anxious or worried in the past month?'],
            data['How often have you felt down, depressed, or hopeless?'],
            data['How often do you engage in self-care activities?'],
            data['How would you rate your sleep quality over the past week?'],
            data['How often do you exercise?'],
            data['How balanced do you consider your diet?'],
            data['How often do you consume alcohol or use recreational substances?'],
            data['How satisfied are you with your academic performance?'],
            data['How pressured do you feel by your academic responsibilities?'],
            data['How often do you procrastinate?'],
            data['How balanced do you feel between work/study and personal life?'],
            data['How resilient do you consider yourself in the face of challenges?'],
            data['How optimistic are you about your future?'],
            data['How aware are you of your emotions and feelings?'],
            data['How effective are your coping mechanisms?'],
            data['How often do you feel lonely?'],
            data['How would you rate your relationship with your family?'],
            data['How satisfied are you with your romantic relationship (if applicable)?'],
            data['How anxious do you feel in social situations?'],
            data['How confident are you in your communication skills?'],
            yes_no_map.get(data['Have you ever been diagnosed with a mental health condition?'], 0),
            yes_no_map.get(data['Have you ever attended therapy or counseling sessions?'], 0),
            data['How would you rate your physical health over the past month?']
        ]
    except KeyError as e:
        return jsonify({"error": f"Missing key in input data: {e}"}), 400

    # Convert features to a 2D array for prediction
    features_array = np.array([features])
    
    # Get prediction from model
    prediction = model.predict(features_array)[0]

    # Map prediction to risk level
    risk_level = risk_level_map.get(prediction, "Unknown")

    # Return the risk level as a string
    return jsonify({'prediction': risk_level})

# Run the app
if __name__ == '__main__':
    app.run(debug=True)
