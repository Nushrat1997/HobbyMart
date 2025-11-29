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

## Registration and Logout
- register.php and logout.php created
- register.php uses form validation within HTML and JavaScript to verify entered data according to constraints
- Accepted data is added to Users table in MySQL after parameter binding and password hashing

### To test register.php and logout.php
Follow the above instructions for login.php but include the entirety of the auth folder, and start both Apache and MySQL in XAMPP, then go to http://localhost/HobbyMart/auth/kimdemo.php. This effectively acts as a 'front page' to use register.php and logout.php. Use the 'Reset' button to ensure the database tables are created.

### Test 1: Register a user and Attempt to reregister the same email
After Resetting the database, click 'Register New User'.
The form will only allow submissions that have:
An email containing a local-part, @, and domain name, including top level domain, eg. test@example.com
A password that is at least 8 characters, with at least 1 uppercase letter, 1 lowercase letter, a number, and a special character.
A reentered password that matches the previously entered password.
A name. (This does not currently validate for a valid name).
After a sucessful submission, trying again with the same email address will fail to register the user.

### Test 2: Attempting to register while already logged in.
Click 'Fake Login'. This creates a false session.
Attempting to click 'Register New User' will recognize that you are already logged in and does not allow the form to be shown.

### Test 3: Logout
Create the false session with 'Fake Login' if you haven't already. The 'Logout' button should result in a success.

### Test 4: Attempting to logout without login
Without a false session created, click 'Logout'. The resulting message should recognize that there is no currently logged in user.

---

