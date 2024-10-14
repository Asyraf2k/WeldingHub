<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian - Pangkalan Data Pendidikan Tinggi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f4f4;
        }

        header {
            background-color: #1877f2;
            padding: 10px 20px;
            color: white;
            text-align: center;
        }

        .search-container {
            margin: 20px;
        }

        .search-container input[type="text"] {
            width: 80%;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
        }

        .search-container button {
            padding: 10px 20px;
            background-color: #4e8ef7;
            border: none;
            border-radius: 20px;
            color: white;
            cursor: pointer;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<header>
    <h1>Pangkalan Data Pendidikan Tinggi</h1>
</header>

<div class="search-container">
    <form method="GET" action="results.php">
        <input type="text" name="q" placeholder="Cari berdasarkan nama, email, spesialisasi, status pekerjaan..." required>
        <button type="submit">Cari</button>
    </form>
</div>

</body>
</html>
