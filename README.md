# Daily TODO Tracker — Setup Guide

A minimal PHP + MySQL daily task tracker. 6 fixed tasks auto-appear every day
(University Study, AI/ML Learning, Web Development, GYM, Reading Books,
One Day One Thing), plus you can add your own custom tasks. Each task has
Start / End buttons; ending a task records the duration.

## Files
```
todo-app/
├── schema.sql       (run once to create the database)
├── db.php           (database connection — edit this for live hosting)
├── index.php        (main page)
├── add_task.php     (adds a custom task)
├── start_task.php   (marks a task as started)
├── end_task.php     (marks a task as completed)
├── delete_task.php  (deletes a custom task)
├── style.css
└── script.js        (just the live clock — no task logic)
```

---

## Part 1 — Run it locally on XAMPP

1. **Install XAMPP** (if not already): https://www.apachefriends.org
2. **Copy the folder**: put the whole `todo-app` folder inside:
   - Windows: `C:\xampp\htdocs\todo-app`
   - Mac: `/Applications/XAMPP/htdocs/todo-app`
3. **Start services**: open the XAMPP Control Panel → start **Apache** and **MySQL**.
4. **Create the database**:
   - Open `http://localhost/phpmyadmin`
   - Click **SQL** tab → paste the contents of `schema.sql` → click **Go**
   - This creates the `todo_app` database and the `tasks` table.
5. **Open the site**: go to `http://localhost/todo-app/index.php`

You should see today's date, a live clock, the 6 fixed tasks, and the
"Add a new task" box. Click **Start** on a task, come back later, click
**End** — it'll show start time, end time, and total duration, and mark
itself Completed.

`db.php` already has the correct defaults for XAMPP (`root` user, no
password), so no edits are needed for local use.

---

## Part 2 — Put it live on a free PHP + MySQL host

Vercel and Netlify only serve static files (HTML/CSS/JS) — they cannot run
PHP or MySQL. To get a real live version with a working database, use a
free host that supports both, such as **InfinityFree** (used below;
000webhost works similarly).

### Step 1: Create a free hosting account
1. Go to https://www.infinityfree.net and sign up.
2. Create a new hosting account (you'll get a free subdomain like
   `yourname.infinityfreeapp.com`, or you can connect your own domain).

### Step 2: Create the MySQL database
1. In your hosting control panel (it looks like cPanel), open **MySQL Databases**.
2. Create a new database — note down the **database name, username,
   password, and host** it gives you (the host is usually something like
   `sqlXXX.infinityfree.com`, NOT `localhost`).
3. Open **phpMyAdmin** from the control panel, select your new database,
   go to the **SQL** tab, and run the contents of `schema.sql` (skip the
   `CREATE DATABASE` line since it already exists — just run the
   `CREATE TABLE` part).

### Step 3: Update db.php with your live database details
Open `db.php` and replace the 4 values with the ones from Step 2:
```php
$host   = "sqlXXX.infinityfree.com";  // from your control panel
$user   = "epiz_xxxxxxx_yourdbuser";
$pass   = "your_db_password";
$dbname = "epiz_xxxxxxx_todo_app";
```

### Step 4: Upload the files
1. In the control panel, open **File Manager** (or use an FTP client like
   FileZilla with the FTP details provided).
2. Go into the `htdocs` folder.
3. Upload every file from `todo-app/` directly into `htdocs`
   (not inside a subfolder, unless you want the site at `yoursite.com/todo-app`).

### Step 5: Visit your live site
Open `http://yourname.infinityfreeapp.com` in a browser — your tracker is
now live with a real database, reachable from any device.

---

## Optional: Why not Vercel/Netlify at all?
If you still want something on Vercel/Netlify too, the only way is to
rebuild the backend as serverless functions (Node.js) with a cloud
database like PlanetScale or Supabase instead of PHP/MySQL — that's a
different stack, not what was built here. The InfinityFree route above
keeps your exact PHP/MySQL code working as-is.
