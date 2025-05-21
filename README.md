#  CSE348 Database Management Systems Term Project: Music Player Web Application

This is a simple music streaming web application built with **PHP**, **MySQL**, and **HTML/CSS**. It was developed as part of a term project and demonstrates functionalities similar to those in real-world music platforms such as Spotify.

---

##  Features

- 🔐 User Authentication (Login system)
- 📁 Playlist Management (create, view, add songs)
- 🔎 Smart Search (songs, artists, playlists)
- 🎵 Song Details with Play History Tracking
- 🎨 Artist Profiles with Follow Feature
- 🌍 Top Artists by Country
- 📈 Genre & Country-Based Analytics
- 🧩 Custom SQL Input Page for Advanced Queries

---

##  Technologies Used

- **PHP** – Server-side scripting
- **MySQL** – Relational database
- **HTML/CSS** – Frontend structure and design
- **AMPPS** – Local server for development (can also use XAMPP)

---

##  Setup Instructions

###  Prerequisites

- Install [AMPPS](https://ampps.com/download) or [XAMPP](https://www.apachefriends.org/index.html)
- PHP & MySQL must be enabled

###  File Placement

Place all project files inside:

```
C:\Program Files (x86)\Ampps\www\musicplayer\
```

###  Database Installation

1. Start Apache and MySQL via AMPPS
2. Visit: [http://localhost/musicplayer/index.html](http://localhost/musicplayer/index.html)
3. Once you've clicked on "Initialize Database", you will be redirected to http://localhost/musicplayer/install.php.
4. This will create all the required tables.
5. You will then have to visit http://localhost/musicplayer/generate_data.php, so that you can create the data for the tables.
6. You will then have to copy and paste all the randomly generated data into the tables.
7. If you want to use the images, you will also have to visit http://localhost/musicplayer/generate_images.php

###  Login

Use one of the generated users from the USERS table in your database.
For example:  
**Email:** `bahar903@example.com`  
**Password:** `pass9608`

---

##  Project Structure

```
musicplayer/
├── index.html
├── install.php
├── generate_data.php
├── generate_images.php
|
├── login.html / login.php
|
├── homepage.html
├── homepage.php
|
├── playlistpage.php
├── playlistpage.html
|
├── artistpage.php
├── artistpage.html
|
├── songpage.php
|
├── currentmusic.php
├── currentmusic.html
|
├── search.php / search_song.php / search_artist.php
|
├── generalSQL.php
├── generalSQL.html
|
├── PDFs/
|   └── MehmetAlphanSari_20220702127_ActionFlow.pdf
|   └── MehmetAlphanSari_20220702127_ER.pdf
|
├── data/
|   └── countries.txt
|   └── names.txt
|
├── sql/
|   └── insert_albums.sql
|   └── insert_artists.sql
|   └── insert_countries.sql
|   └── insert_history.sql
|   └── insert_playlists.sql
|   └── insert_playlistsongs.sql
|   └── insert_songs.sql
|   └── insert_users.sql
|   └── update_images.sql
|   
├── README.md
```

---

##  Notes

- You must insert the data in this order: 
    insert_countries.sql
    insert_users.sql
    insert_artists.sql
    insert_albums.sql
    insert_songs.sql
    insert_playlists.sql
    insert_playlistsongs.sql
    insert_history.sql
- Images are linked via external URLs or reused across entities.
- Foreign keys and constraints are defined in `install.php` for clean schema generation.
- You can use `generate_data.php` to re-generate insert queries for sample data.
- The app is kept modular and scalable for future additions.

---

##  Author

**Mehmet Alphan SARI**  
Student ID: 20220702127  
Database Management Systems  
[Yeditepe University, Spring 2025]