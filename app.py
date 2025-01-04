from flask import Flask, request, jsonify
import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.impute import SimpleImputer
from sklearn.metrics import accuracy_score
from sklearn.model_selection import GridSearchCV

# Initialize Flask app
app = Flask(__name__)

# Define mappings for categorical values
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

# Load dataset and preprocess
def load_and_preprocess_data():
    df = pd.read_csv('Preprocessed_Mind_Sync_Mental_Health_Survey.csv')
    X = df.drop(columns=['Risk Levels'])
    y = df['Risk Levels']

    imputer = SimpleImputer(strategy='mean')
    X_imputed = imputer.fit_transform(X)

    return X_imputed, y

# Train model
def train_model():
    X, y = load_and_preprocess_data()
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.3, random_state=42, stratify=y
    )

    param_grid = {
        'n_estimators': [50, 100],
        'max_depth': [10, 20, None],
        'min_samples_split': [2, 5],
        'min_samples_leaf': [1, 2],
        'criterion': ['gini']
    }

    random_forest = RandomForestClassifier(random_state=42)
    grid_search = GridSearchCV(
        random_forest, param_grid, cv=3, scoring='accuracy', n_jobs=-1
    )
    grid_search.fit(X_train, y_train)

    best_model = grid_search.best_estimator_

    train_accuracy = accuracy_score(y_train, best_model.predict(X_train))
    test_accuracy = accuracy_score(y_test, best_model.predict(X_test))

    return best_model, train_accuracy, test_accuracy, X_train, X_test, y_train, y_test

# Train the model and store results globally
model, train_accuracy, test_accuracy, X_train, X_test, y_train, y_test = train_model()

@app.route('/predict', methods=['POST'])
def predict():
    data = request.json

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

        features_array = np.array([features])
        prediction = model.predict(features_array)[0]

        # Map prediction to risk level
        risk_level_map = {0: "High", 1: "Low", 2: "Mid"}
        risk_level = risk_level_map.get(prediction, "Unknown")

        return jsonify({'prediction': risk_level})
    except Exception as e:
        return jsonify({'error': str(e)}), 400

@app.route('/accuracy', methods=['GET'])
def get_accuracy():
    return jsonify({
        'train_accuracy': train_accuracy,
        'test_accuracy': test_accuracy
    })

if __name__ == '__main__':
    app.run(debug=True)
