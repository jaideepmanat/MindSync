const recordBtn = document.getElementById('record-btn');
const cancelBtn = document.getElementById('cancel-record');
const submitRecordingBtn = document.getElementById('submit-recording');
const audioPreview = document.getElementById('audio-preview');
const submitJournalBtn = document.getElementById('submit-journal');
const journalText = document.getElementById('journal-text');

let mediaRecorder;
let audioChunks = [];

const userIcon = document.getElementById('user-icon');
const dropdownMenu = document.getElementById('dropdown-menu');

// Toggle dropdown menu
userIcon.addEventListener('click', function (event) {
    event.stopPropagation();
    dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
});

// Close dropdown if clicked outside
window.addEventListener('click', function (event) {
    if (!userIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.style.display = "none";
    }
});

// Set initial record button state
recordBtn.style.backgroundColor = "green";
recordBtn.innerHTML = `<img src="https://cdn-icons-png.flaticon.com/512/724/724927.png" width="24" height="24">`; 

recordBtn.addEventListener('click', async () => {
    if (!mediaRecorder || mediaRecorder.state === 'inactive') {
        startRecording();
    } else {
        stopRecording();
    }
});

cancelBtn.addEventListener('click', () => {
    audioChunks = [];
    audioPreview.src = "";
    audioPreview.style.display = 'none';
    submitRecordingBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    recordBtn.style.backgroundColor = "green";
    recordBtn.innerHTML = `<img src="https://cdn-icons-png.flaticon.com/512/724/724927.png" width="24" height="24">`; 
});

// Submit journal text for emotion analysis
submitJournalBtn.addEventListener('click', () => {
    const text = journalText.value.trim();
    if (text === "") {
        alert("Please write something before submitting.");
        return;
    }

    fetch("http://127.0.0.1:5000/predict_text", {  
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ text })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Text Response:", data);
        if (data.predicted_emotion) {
            alert(`Predicted Emotion: ${data.predicted_emotion}`);
            journalText.value = ""; 
        } else {
            alert("Error: " + (data.error || "Unknown error occurred"));
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Failed to submit journal entry.");
    });
});

// Submit audio recording for emotion analysis
submitRecordingBtn.addEventListener('click', () => {
    if (audioChunks.length === 0) {
        alert("No recorded audio found.");
        return;
    }

    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' }); // Using webm for better browser support
    console.log("Audio Blob Created:", audioBlob);

    const formData = new FormData();
    formData.append('audio', audioBlob, 'journal_audio.webm');
    
    // Debugging: Log FormData entries
    for (let pair of formData.entries()) {
        console.log(pair[0], pair[1]);
    }

    fetch("http://127.0.0.1:5000/predict_audio", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Response from Flask:", data);
        if (data.predicted_emotion) {
            alert(`Predicted Emotion from Audio: ${data.predicted_emotion}`);
        } else {
            alert("Error: " + (data.error || "Unknown error occurred"));
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Failed to process audio.");
    });
});

async function startRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream, { mimeType: "audio/webm" });
        audioChunks = [];

        mediaRecorder.ondataavailable = event => audioChunks.push(event.data);

        mediaRecorder.onstop = () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const audioUrl = URL.createObjectURL(audioBlob);
            audioPreview.src = audioUrl;
            audioPreview.style.display = 'block';
            submitRecordingBtn.style.display = 'block';
            cancelBtn.style.display = 'block';
        };

        mediaRecorder.start();
        recordBtn.style.backgroundColor = "red";
        recordBtn.innerHTML = `<img src="https://cdn-icons-png.flaticon.com/512/61/61238.png" width="24" height="24">`; 
    } catch (error) {
        console.error("Recording failed: ", error);
        alert("Microphone access denied or unavailable.");
    }
}

function stopRecording() {
    if (!mediaRecorder || mediaRecorder.state !== 'recording') {
        alert("No active recording found.");
        return;
    }

    mediaRecorder.stop();
    recordBtn.style.backgroundColor = "green";
    recordBtn.innerHTML = `<img src="https://cdn-icons-png.flaticon.com/512/724/724927.png" width="24" height="24">`; 
}
