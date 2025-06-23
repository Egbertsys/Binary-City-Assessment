# Binary-City-Assessment


# 🧩 Binary City Contact Manager (PHP MVC + AJAX)

This is a simple contact management system built using core PHP, MVC architecture**, and fully powered by AJAX. It allows you to create users, link/unlink them, and view user relationships — all in real-time, with no page reloads.

---

## ✨ Features

- ✅ Create user with auto-generated unique code (`TOM001`, `JAM002`, etc.)
- ✅ View list of all users with child count
- ✅ Link one user to many others (parent → multiple children)
- ✅ Unlink users with confirmation
- ✅ AJAX-powered for all operations
- ✅ Tab-based UI 
- ✅ External CSS for styling 

---

## 📁 Folder Structure
BinaryCity/
├── app/
│ ├── controllers/
│ ├── models/
│ └── views/
│ └── users/
├── core/
│ └── Database.php
├── public/
│ ├── index.php
│ ├── .htaccess
│ └── assets/
│ └── style.css


---

## 🛠️ Setup Instructions

### 1. Clone this Repo

```bash
git clone https://github.com/your-username/BinaryCity.git

Move to XAMPP's htdocs folder
C:/xampp/htdocs/BinaryCity/

Create the database in phpMyAdmin:
CREATE DATABASE binary_city;

Import these tables:

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  surname VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  code VARCHAR(10)
);

CREATE TABLE user_links (
  id INT AUTO_INCREMENT PRIMARY KEY,
  parent_id INT NOT NULL,
  child_id INT NOT NULL,
  UNIQUE KEY unique_link (parent_id, child_id),
  FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (child_id) REFERENCES users(id) ON DELETE CASCADE
);

🚀 Run the App
Open in browser:
http://localhost/BinaryCity/public/

⚙️ Technologies Used
PHP (no frameworks)
AJAX (vanilla JavaScript)
MySQL (via PDO)
MVC design
External CSS


🙌 Author
Built by Egbert for Binary City.



---

Let me know if you want me to:
Include screenshot/image badges  
