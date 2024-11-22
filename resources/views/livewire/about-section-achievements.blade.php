<div class="achievements-section">
    <div class="section-title">
        <img src="{{ asset('images/app/categorize.svg') }}" alt="Achievements Icon" class="title-icon">
        <h2>الإنجازات</h2>
    </div>

    <div class="slider-container">
        <button class="slider-button prev" onclick="slideAchievements('prev')">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 18L9 12L15 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <div class="achievements-slider">
            <div class="slider-track">
                @foreach($achievements as $achievement)
                <div class="achievement-card">
                    <img src="{{ asset($achievement['image']) }}" alt="Achievement" class="achievement-image">
                    <div class="achievement-info">
                        <span class="achievement-date">{{ $achievement['date'] }}</span>
                        <p class="achievement-text">{{ $achievement['text'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <button class="slider-button next" onclick="slideAchievements('next')">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 18L15 12L9 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</div>

@push('styles')
<style>
.achievements-section {
    padding: 2rem;
    margin: 2rem 0;
}

.section-title {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.title-icon {
    width: 40px;
    height: 40px;
}

.section-title h2 {
    font-size: 1.5rem;
    color: #0B626A;
    font-weight: bold;
}

.slider-container {
    position: relative;
    display: flex;
    align-items: center;
    padding: 0 2rem;
}

.achievements-slider {
    overflow: hidden;
    width: 100%;
}

.slider-track {
    display: flex;
    gap: 1.5rem;
    transition: transform 0.3s ease;
}

.achievement-card {
    min-width: calc(25% - 1.125rem);
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.achievement-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.achievement-info {
    padding: 1rem;
    text-align: right;
}

.achievement-date {
    display: block;
    font-size: 0.875rem;
    color: #707070;
    margin-bottom: 0.5rem;
}

.achievement-text {
    color: #0B626A;
    line-height: 1.5;
}

.slider-button {
    background: #0B626A;
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
    position: absolute;
    z-index: 1;
}

.slider-button:hover {
    background: #063135;
}

.slider-button.prev {
    left: 0;
}

.slider-button.next {
    right: 0;
}
.slider-button svg {
    width: 24px;
    height: 24px;
}

.slider-track {
    display: flex;
    gap: 1.5rem;
    transition: transform 0.3s ease;
}

@media (max-width: 1200px) {
    .achievement-card {
        min-width: calc(33.333% - 1rem);
    }
}

@media (max-width: 992px) {
    .achievement-card {
        min-width: calc(50% - 0.75rem);
    }
}

@media (max-width: 576px) {
    .achievement-card {
        min-width: 100%;
    }
}
</style>
@endpush
@push('scripts')
<script>
let currentSlide = 0;
const totalSlides = {{ count($achievements) }};

function slideAchievements(direction) {
    const track = document.querySelector('.slider-track');
    const cards = track.querySelectorAll('.achievement-card');
    const cardWidth = cards[0].offsetWidth;
    const gap = 24; // 1.5rem converted to pixels

    if (direction === 'next') {
        currentSlide++;
        if (currentSlide >= totalSlides) {
            currentSlide = 0;
        }
    } else {
        currentSlide--;
        if (currentSlide < 0) {
            currentSlide = totalSlides - 1;
        }
    }
    
    const translateX = currentSlide * -(cardWidth + gap);
    track.style.transform = `translateX(${translateX}px)`;
}

// Initialize slider
document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.slider-track');
    const cards = track.querySelectorAll('.achievement-card');
    
    // Clone cards for smooth infinite scroll
    cards.forEach(card => {
        const clone = card.cloneNode(true);
        track.appendChild(clone);
    });
});
</script>
@endpush