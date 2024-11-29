<div id="hero-section">

    <div class="background-container">
        <img src="{{ asset('images/app/ABG.svg') }}" alt="" class="bg-main">
        <div class="content-wrapper">
            {{view("components.layouts.nav")}}

            <div class="hero-content">
                <h1 class="hero-title"> المؤسسات</h1>

                <div class="search-container">
                    <input type="text"
                           placeholder="ابحث بالاسم"
                           wire:model="searchKey"
                           class="search-input">
                    <button class="search-button" wire:click="search">
                        <img src="{{ asset('images\app\searchIcon.svg') }}" alt="Search">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')

<style>
#hero-section {
    position: relative;
    width: 100%;
    height: 421px;
    overflow: hidden;
}

.background-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.bg-main {
    position: absolute;
    top: 0;
    left: 0;
    width: 2409.1px;
    height: 421px;
    object-fit: cover;
    z-index: 1;
}

.content-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    z-index: 2;
}



.hero-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    max-width: 1200px;
    z-index: 3;
    text-align: center;
}

.hero-title {
    font-size: clamp(2.5rem, 8vw, 4rem);
    color: white;
    margin-bottom: 3rem;
    line-height: 1.2;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.search-container {
    width: 100%;
    max-width: 627px;
    position: relative;
    margin: 0 auto;
    padding: 0 1rem;
}

.search-input {
    width: 100%;
    padding: 1rem 4rem 1rem 1rem;
    border-radius: 8px;
    border: none;
    background-color: #F5F5F5;
    font-size: 1.2rem;
    direction: rtl;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16);
}

.search-button {
    position: absolute;
    left: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
}

.search-button img {
    width: 24px;
    height: 24px;
}

@media (max-width: 768px) {
    .hero-section {
        height: 300px;
    }

    .bg-main {
        width: 100%;
        height: 100%;
    }

    .bg-overlay {
        width: 100%;
        object-fit: contain;
    }

    .search-input {
        font-size: 1rem;
        padding: 0.8rem 3rem 0.8rem 0.8rem;
    }

    .search-button {
        left: 1.2rem;
    }

    .search-button img {
        width: 20px;
        height: 20px;
    }
}
</style>
@endpush
