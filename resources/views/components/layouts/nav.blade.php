<nav class="navbar navbar-expand-sm navbar-dark bg-transparent">
    <div class="container">
        <a class="navbar-brand" href="{{ route('welcome') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                 class="d-inline-block align-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mynavbar">
            <ul class="navbar-nav me-auto gap-4">
                <li class="nav-item">
                    <a class="nav-link" href="#">تواصل معنا</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">الإعلام</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">المؤسسات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('achievers') }}">المنجزين</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">الإنجازات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">عنا</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
