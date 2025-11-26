# HobbyMart – Group Project Repository

## 👤 Nushrat Nourin – User Authentication (Login Feature)

### ✅ Feature Implemented
- Created the `login.php` prototype  
- Added form validation using PHP  
- Displayed error messages for empty fields  
- Displayed success message for valid input  
- Designed user interface using HTML + CSS  
---
## 🚀 How to Run the Login Prototype

### 1. Install XAMPP  
Download from the official site:  
https://www.apachefriends.org/download.html

### 2. Go to your `htdocs` directory  
C:\xampp\htdocs\

### 3. Create a project folder named:

HobbyMart

### 4. Inside the folder, place the `auth` directory:

HobbyMart/auth/login.php

### 5. Start Apache in XAMPP  
Open **XAMPP Control Panel** → click **Start** next to **Apache**.

### 6. Run the login page in your browser  
Type the following URL:

http://localhost/HobbyMart/auth/login.php
---

## 🧪 Testing Performed
### ✔ Test Case 1: Both fields empty  
- Email: ""  
- Password: ""  
- Expected: Error message  
- Result: ✅ Pass  

### ✔ Test Case 2: Email only  
- Email entered, password empty  
- Expected: Error message  
- Result: ✅ Pass  

### ✔ Test Case 3: Password only  
- Password entered, email empty  
- Expected: Error message  
- Result: ✅ Pass  

### ✔ Test Case 4: Valid email + password  
- Expected: Success message  
- Result: ✅ Pass  

---
