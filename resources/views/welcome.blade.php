<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Landing Page | Apotek Parakan Muncang</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('https://source.unsplash.com/1600x900/?medicine,pharmacy') no-repeat center center fixed;
      background-size: cover;
    }
    .glass {
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 1rem;
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      padding: 3rem;
      color: #fff;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    }
  </style>
</head>
<body>
    <div class="bg-dark text-white">
  <div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="glass text-center">
      <h1 class="mb-3">Selamat Datang</h1>
      <p >di Sistem Informasi Apotek Parakan Muncang <br> silahkan login untuk melanjutkan</p>
      <p class="mb-4"> </p>
      <div class="d-grid gap-2 d-md-flex justify-content-md-center">
        <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4 me-md-2">Login</a>
       
      </div>
    </div>
  </div>
  </div>
</body>
</html>
