import requests
import json

# Define the URL for the Flask app
url = "http://127.0.0.1:5000/predict"

# Endpoint to check training and test accuracy
accuracy_url = "http://127.0.0.1:5000/accuracy"

# Fetch and display training and test accuracy
accuracy_response = requests.get(accuracy_url)
if accuracy_response.status_code == 200:
    accuracies = accuracy_response.json()
    print("Train Accuracy:", accuracies.get("train_accuracy", "N/A"))
    print("Test Accuracy:", accuracies.get("test_accuracy", "N/A"))
else:
    print("Error fetching accuracies:", accuracy_response.status_code, accuracy_response.text)

# Example input data for "Mid"
input_data_mid = {
    "Age": 22,
    "gender": "Male",
    "How often do you feel stressed?": 3,
    "How often have you felt anxious or worried in the past month?": 3,
    "How often have you felt down, depressed, or hopeless?": 2,
    "How often do you engage in self-care activities?": 3,
    "How would you rate your sleep quality over the past week?": 3,
    "How often do you exercise?": 3,
    "How balanced do you consider your diet?": 3,
    "How often do you consume alcohol or use recreational substances?": 2,
    "How satisfied are you with your academic performance?": 3,
    "How pressured do you feel by your academic responsibilities?": 3,
    "How often do you procrastinate?": 3,
    "How balanced do you feel between work/study and personal life?": 3,
    "How resilient do you consider yourself in the face of challenges?": 3,
    "How optimistic are you about your future?": 3,
    "How aware are you of your emotions and feelings?": 3,
    "How effective are your coping mechanisms?": 3,
    "How often do you feel lonely?": 3,
    "How would you rate your relationship with your family?": 3,
    "How satisfied are you with your romantic relationship (if applicable)?": 3,
    "How anxious do you feel in social situations?": 3,
    "How confident are you in your communication skills?": 3,
    "Have you ever been diagnosed with a mental health condition?": "No",
    "Have you ever attended therapy or counseling sessions?": "No",
    "How would you rate your physical health over the past month?": 3
}

# Example input data for "High"
input_data_high = {
    "Age": 30,
    "gender": "Female",
    "How often do you feel stressed?": 5,
    "How often have you felt anxious or worried in the past month?": 5,
    "How often have you felt down, depressed, or hopeless?": 5,
    "How often do you engage in self-care activities?": 1,
    "How would you rate your sleep quality over the past week?": 1,
    "How often do you exercise?": 1,
    "How balanced do you consider your diet?": 1,
    "How often do you consume alcohol or use recreational substances?": 4,
    "How satisfied are you with your academic performance?": 2,
    "How pressured do you feel by your academic responsibilities?": 5,
    "How often do you procrastinate?": 5,
    "How balanced do you feel between work/study and personal life?": 1,
    "How resilient do you consider yourself in the face of challenges?": 1,
    "How optimistic are you about your future?": 1,
    "How aware are you of your emotions and feelings?": 1,
    "How effective are your coping mechanisms?": 1,
    "How often do you feel lonely?": 5,
    "How would you rate your relationship with your family?": 1,
    "How satisfied are you with your romantic relationship (if applicable)?": 1,
    "How anxious do you feel in social situations?": 5,
    "How confident are you in your communication skills?": 1,
    "Have you ever been diagnosed with a mental health condition?": "Yes",
    "Have you ever attended therapy or counseling sessions?": "Yes",
    "How would you rate your physical health over the past month?": 1
}

# Example input data for "Low"
input_data_low = {
    "Age": 28,
    "gender": "Other",
    "How often do you feel stressed?": 1,
    "How often have you felt anxious or worried in the past month?": 1,
    "How often have you felt down, depressed, or hopeless?": 1,
    "How often do you engage in self-care activities?": 5,
    "How would you rate your sleep quality over the past week?": 5,
    "How often do you exercise?": 5,
    "How balanced do you consider your diet?": 5,
    "How often do you consume alcohol or use recreational substances?": 1,
    "How satisfied are you with your academic performance?": 5,
    "How pressured do you feel by your academic responsibilities?": 1,
    "How often do you procrastinate?": 1,
    "How balanced do you feel between work/study and personal life?": 5,
    "How resilient do you consider yourself in the face of challenges?": 5,
    "How optimistic are you about your future?": 5,
    "How aware are you of your emotions and feelings?": 5,
    "How effective are your coping mechanisms?": 5,
    "How often do you feel lonely?": 1,
    "How would you rate your relationship with your family?": 5,
    "How satisfied are you with your romantic relationship (if applicable)?": 5,
    "How anxious do you feel in social situations?": 1,
    "How confident are you in your communication skills?": 5,
    "Have you ever been diagnosed with a mental health condition?": "No",
    "Have you ever attended therapy or counseling sessions?": "No",
    "How would you rate your physical health over the past month?": 5
}

# Send POST requests to the Flask app for each case
for case_name, input_data in zip(["Mid", "High", "Low"], [input_data_mid, input_data_high, input_data_low]):
    response = requests.post(url, json=input_data)
    print(f"Case: {case_name}")
    if response.status_code == 200:
        print("Prediction:", response.json())
    else:
        print("Error:", response.status_code, response.text)
    print("-")
