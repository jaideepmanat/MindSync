# 🌟 MindSync: Mental Health Monitoring Website 🌟

Welcome to **MindSync**, a mental health monitoring platform designed to help students connect with mental health experts and engage with a supportive community. This project integrates a user-friendly interface with Logistic regression to provide tailored mental health recommendations. 

---

## 🖥️ Features

### 👨‍🎓 Student
- **Take Survey**: Fill out a form to assess mental health status:
  - **Low**: Option to return to the homepage.
  - **Mid or High**: Suggests registered consultants to book, rate, or message.
- **Consult an Expert**: View all registered consultants and interact with them.
- **View Messages**: Check messages from consultants.
- **Community Forum**: Post questions anonymously and interact with consultants.

### 🧑‍⚕️ Consultant
- **Registration**: Upload proof documents for verification by the admin.
- **Dashboard**: 
  - View booked users.
  - Close cases or send messages to registered users.
- **Community Forum**: Answer anonymous student questions.

### 🛠️ Admin
- **Manage Consultant Requests**: Accept or reject registration requests after verifying proof documents.
- **View Proof Documents**: Ensure the authenticity of consultant registrations.

---

## 🔧 Tech Stack

- **Frontend**: HTML, CSS
- **Backend**: PHP (using XAMPP for local server)
- **Machine Learning Integration**: Flask
- **Database**: MySQL (`mindsync.sql` provided in the repository)

---

## 🚀 Getting Started

### 🛠 Prerequisites
1. Install **XAMPP**:
   - [Download XAMPP](https://www.apachefriends.org/index.html) and install it.
   - Start the Apache and MySQL modules in the XAMPP Control Panel.
2. Install **Python and Flask**:
   - Download Python from [Python's official website](https://www.python.org/downloads/).
   - Install Flask using pip:
     ```bash
     pip install flask
     ```
3. Clone this repository:
   ```bash
   git clone https://github.com/jaideepmanat/MindSync.git
   cd mindsync
## 🏗️ Setting Up the Project

### 🛠 Database Setup
1. Open **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Create a new database named **mindsync**.
3. Import the `mindsync.sql` file:
   - Click on the database name → Go to **Import** → Choose `mindsync.sql` → Click **Go**.

### 🌐 Start the Local Server
1. Move the project folder (`mindsync`) to the `htdocs` directory of XAMPP.
2. Access the project at [http://localhost/mindsync](http://localhost/mindsync).

### 🐍 Run the Flask Server
1. Navigate to the folder containing your Flask application.
2. Start the Flask server:
   ```bash
   python app.py
## 🤖 Machine Learning Model Integration
The machine learning model predicts mental health status based on survey responses. **Flask** is used to communicate between the frontend and the model.

- Ensure the Flask server is running as described in the setup.
- Survey submissions are processed via Flask, and results are displayed on the frontend.

---

## 🧪 Testing the Features

### Students:
- Register as a student, take a survey, and explore the features.

### Consultants:
- Register and wait for admin approval. Post-login, explore the consultant-specific options.

### Admins:
- Log in as an admin to manage consultant requests and view proof documents.

### Testing with `flask_api_test.py`:
- Use the `flask_api_test.py` file to test the Flask API integration and ensure the machine learning model is working correctly.


---

## 📂 Project Structure

```plaintext
mindsync/
├── app.py                   # Flask app for ML integration
├── mindsync.sql             # Database structure
├── accept_consultant.php     # Page for accepting consultant registration
├── admin.css                # CSS for admin pages
├── admin.js                 # JS for admin functionalities
├── admin.php                # Admin page
├── book_consultation.php    # Book consultation page
├── check_user.php           # Check user credentials functionality
├── close_case.php           # Close case page functionality
├── CommunityConsult.css     # CSS for consultant community pages
├── CommunityConsult.js      # JS for consultant community functionalities
├── CommunityConsult.php     # Consultant community page
├── CommunityUser.css        # CSS for student community pages
├── CommunityUser.js         # JS for student community functionalities
├── CommunityUser.php        # Student community page
├── config.php               # Configuration file
├── consult.css              # CSS for consultant pages
├── consult.js               # JS for consultant functionalities
├── consult.php              # Consultant page
├── fetch_users.php          # Fetch users for admin/consultant purposes
├── flask_api_test.py        # Test file for Flask API integration
├── form.css                 # CSS for survey form
├── form.js                  # JS for survey form
├── form.php                 # Survey form page
├── home1.css                # Student's homepage CSS
├── home1.js                 # Student's homepage JS
├── home1.php                # Student's homepage
├── home2.css                # Consultant's homepage CSS
├── home2.js                 # Consultant's homepage JS
├── home2.php                # Consultant's homepage
├── icon.png                 # Project logo
├── image1.jpg               # Image for project
├── image2.jpg               # Image for project
├── image3.jpg               # Image for project
├── login.css                # CSS for login page
├── login.html               # HTML login page
├── login.js                 # JS for login page
├── login.php                # PHP login logic
├── logout.php               # Logout page
├── mail.css                 # CSS for mail pages
├── mail.js                  # JS for mail functionalities
├── mail.php                 # Mail-related PHP logic
├── Outcome.css              # CSS for outcome prediction (Low risk)
├── Outcome.js               # JS for outcome prediction (Low risk)
├── Outcome.php              # Outcome prediction page (Low risk)
├── Outcome2.css             # CSS for alternative outcome prediction (Mid/High risk)
├── Outcome2.js              # JS for alternative outcome prediction (Mid/High risk)
├── Outcome2.php             # Alternative outcome prediction page (Mid/High risk)
├── prediction.css           # CSS for prediction page
├── prediction.js            # JS for prediction page
├── prediction.php           # PHP prediction logic
├── Preprocessed_Mind_Sync_Mental_Health_Survey.csv  # Data for survey
├── Profile.css              # CSS for user profile page
├── Profile.js               # JS for user profile page
├── Profile.php              # User profile page
├── register.php             # Registration page
├── reject_consultant.php    # Reject consultant registration
├── submit_feedback.php      # Submit feedback functionality
├── submit_message.php       # Submit message to consultant functionality
├── submit_question.php      # Submit question to community functionality
├── submit_rating.php        # Submit rating for consultant functionality
├── submit_reply.php         # Submit reply in community functionality
├── table.css                # CSS for tables
├── table.js                 # JS for table functionalities
├── table.php                # Table page
├── table2.php               # Another table page
├── update_status.php        # Update user or consultant status
└── upload_test.php          # Upload test files functionality

├── uploads/                 # Folder for uploaded files
│   ├── 67768ae9e3d25_WhatsApp Image 2025-01-02.jpg # Example uploaded image
│   ├── 67768b836275c_WhatsApp Image 2025-01-02.jpg
│   ├── 6776b5366caeb_WhatsApp Image 2025-01-02.jpg
│   └── 6776b560c5af2_WhatsApp Image 2025-01-02.jpg

```
---

## 🛠️ Tools and Resources
- **XAMPP**: Local server environment.
- **Flask**: Lightweight Python framework for ML integration.
- **phpMyAdmin**: MySQL database management.
- **HTML & CSS**: Frontend design.

---

## 📄 License
This project is licensed under the **MIT License**.

---

## 🖤 Thank You!
Thank you for exploring **MindSync**! Contributions and suggestions are always welcome. Feel free to open issues or submit pull requests. 😊

