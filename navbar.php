<nav class="navbar navbar-expand-lg navbar-light bg-light" style="margin: 0; padding-right: 0; padding-left: 0">
    <a class="navbar-brand brand col-2" href="index.php">SPK</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link font-navbar" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-navbar" href="peserta.php">Data Peserta</a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-navbar" href="kriteria.php">Data Kriteria</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle font-navbar" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Data Perhitungan
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="knn.php">Knn</a>
                    <a class="dropdown-item" href="waspass.php">Waspass</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link font-navbar" href="hasil.php">Hasil</a>
            </li>
        </ul>
    </div>
    <form method="POST" class="form">
        <button class="btn btn-danger" name="submit_logout">Logout</button>
    </form>
</nav>