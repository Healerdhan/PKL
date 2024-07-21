<!-- resources/views/layouts/navbar.blade.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
      <a class="navbar-brand" href="#">Dashboard</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
          {{-- <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                  <a class="nav-link" href="#">Home</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="#">Category</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="#">Siswa</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="#">Dudi</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="#">Pembimbing</a>
              </li>
          </ul> --}}
          <ul class="navbar-nav ms-auto">
              <li class="nav-item">
                  <a class="nav-link" href="#"
                      onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                      Logout
                  </a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                      @csrf
                  </form>
              </li>
          </ul>
      </div>
  </div>
</nav>
