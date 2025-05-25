import requests

# Flask server URL
BASE_URL = "http://127.0.0.1:5000"

# Test text journal submission
def test_text_submission():
    url = f"{BASE_URL}/predict_text"
    data = {"text": "I am feeling very happy today!"}
    response = requests.post(url, json=data)
    
    print("Text Prediction Response:")
    print(response.json())

# Run tests
if __name__ == "__main__":
    test_text_submission()
