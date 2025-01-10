<nav class="navbar navbar-expand-sm navbar-dark bg-transparent">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                 class="d-inline-block align-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar"
                aria-controls="mynavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mynavbar">
            <ul class="navbar-nav me-auto gap-4">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">عنا</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('achievements')}}">الإنجازات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('organizations') }}">المؤسسات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('achievers') }}">المنجزين</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">الإعلام</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact-us') }}">تواصل معنا</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<style>
@media (max-width: 576px) {
    .navbar-collapse {
        background-color: rgba(0, 0, 0, 0.9);
        padding: 1rem;
        border-radius: 8px;
        margin-top: 10px;
    }

    .navbar-nav {
        text-align: center;
    }

    .nav-item {
        margin: 10px 0;
    }

    .nav-link {
        padding: 10px !important;
        display: block;
        width: 100%;
    }

    .navbar-toggler {
        border: none;
        padding: 0.5rem;
    }

    .navbar-toggler:focus {
        box-shadow: none;
        outline: none;
    }

    .navbar-toggler-icon {
        width: 1.5em;
        height: 1.5em;
    }

    .gap-4 {
        gap: 0.5rem !important;
    }
}
</style>
