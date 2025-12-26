Projekuas/
├── 📂 includes/                    # File PHP global
│   ├── koneksi.php                # Koneksi database
│   ├── header.php                 # Header semua halaman
│   └── footer.php                 # Footer semua halaman
│
├── 📂 assets/                     # Assets website
│   ├── 📂 css/                    # File stylesheet
│   │   ├── style.css             # CSS global
│   │   ├── dashboard.css         # Styling dashboard
│   │   ├── tasks.css             # Styling halaman tugas
│   │   ├── form.css              # Styling form input/edit
│   │   ├── completed.css         # Styling halaman selesai
│   │   └── responsive.css        # Media queries responsif
│   │
│   ├── 📂 js/                     # File JavaScript
│   │   ├── main.js               # JS global
│   │   ├── dashboard.js          # Fungsi dashboard
│   │   ├── tasks.js              # Fungsi kelola tugas
│   │   ├── form.js               # Validasi form
│   │   ├── completed.js          # Fungsi halaman selesai
│   │   └── validation.js         # Validasi umum
│   │
│   └── 📂 images/                 # Gambar & foto
│       ├── profile.jpg           # Foto profil default
│       └── uploads/              # Folder upload foto
│
├── 📂 pages/                      # Halaman utama
│   ├── index.php                 # Dashboard utama
│   ├── semua-tugas.php           # Semua tugas
│   ├── tambah-tugas.php          # Form tambah tugas
│   ├── tugas-pending.php         # Tugas pending
│   ├── tugas-selesai.php         # Tugas selesai
│   ├── search.php                # Hasil pencarian
│   └── upload-profile.php        # Upload foto profil
│
├── proses.php                    # Proses CRUD database
├── debug.php                     # Debugging tools
└── database.sql                  # Skema database
