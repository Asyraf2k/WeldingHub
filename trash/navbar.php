<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar dengan Pencarian Pengguna</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column; /* Mengatur body untuk kolom */
            min-height: 100vh; /* Memastikan body penuh secara vertikal */
        }

        /* Navbar */
        .navbar {
            background-color: #333;
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between; /* Rata kanan dan kiri */
        }

        .search-input {
            background-color: #fff;
            border: none;
            border-radius: 5px;
            padding: 0.5rem;
            width: 200px; /* Lebar input */
        }

        .search-button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            cursor: pointer;
        }

        /* Hasil pencarian */
        .search-results {
            position: absolute;
            background: white;
            width: 250px; /* Lebar hasil pencarian */
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ccc;
            display: none; /* Sembunyikan hasil pencarian */
            z-index: 1000; /* Pastikan di atas konten lainnya */
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .search-results li {
            padding: 0.75rem;
            cursor: pointer;
            transition: background 0.2s; /* Efek transisi */
        }

        .search-results li:hover {
            background: #f0f0f0; /* Warna latar belakang saat hover */
        }

        .search-results li a {
            text-decoration: none; /* Menghilangkan garis bawah */
            color: black; /* Warna teks */
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">Logo</div>
        <div style="display: flex; align-items: center;">
            <input type="text" class="search-input" placeholder="Cari pengguna" id="search">
            <button class="search-button" id="search-button">Cari</button> <!-- Tombol Pencarian -->
        </div>
    </div>

    <ul class="search-results" id="search-results"></ul>

    <script>
        const searchInput = document.getElementById('search');
        const searchResults = document.getElementById('search-results');
        const searchButton = document.getElementById('search-button');

        const performSearch = async () => {
            const query = searchInput.value;

            if (query) {
                // Lakukan fetch ke search.php dengan query
                const response = await fetch(`search.php?query=${query}`);
                const results = await response.json(); // Ambil hasil sebagai JSON

                // Tampilkan hasil pencarian
                searchResults.innerHTML = results.map(user => 
                    `<li><a href="frontend/user/profile.php?username=${user.username}">${user.username} - ${user.email} - ${user.specialization_name} - ${user.job_status_name}</a></li>`).join('');
                searchResults.style.display = 'block'; // Tampilkan hasil
            } else {
                searchResults.style.display = 'none'; // Sembunyikan jika input kosong
            }
        };

        // Panggil fungsi pencarian saat input atau tombol diklik
        searchInput.addEventListener('input', performSearch);
        searchButton.addEventListener('click', performSearch);

        // Menyembunyikan hasil pencarian ketika klik di luar
        document.addEventListener('click', (event) => {
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.style.display = 'none';
            }
        });
    </script>
</body>
</html>
