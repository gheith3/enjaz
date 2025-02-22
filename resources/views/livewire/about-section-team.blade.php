<div class="team-section">
    <div class="section-title">
        <img src="{{ asset('images/app/categorize.svg') }}" alt="Team Icon" class="title-icon">
        <h2>فريق العمل</h2>
    </div>

    <div class="team-grid">
        @foreach($teemSettings->members as $member)
        <div class="team-member" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            <img src="{{ Storage::url($member['image']) }}" alt="{{ $member['name'] }}" class="member-image">
            <div class="member-info">
                <h3 class="member-name">{{ $member['name'] }}</h3>
                <p class="member-title">{{ $member['position'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('styles')
<style>
.team-section {
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

.team-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem; /* Reduced from 3rem to 1.5rem */
    justify-items: center;
    padding: 2rem 0;
}

.team-member {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: transform 0.3s ease;
}

.team-member:hover {
    transform: translateY(-5px);
}

.member-image {
    width: 200px;
    height: 200px;
    object-fit: cover;
    margin-bottom: 1rem;

}

.member-info {
    text-align: center;
}

.member-name {
    font-size: 1.2rem;
    color: #0B626A;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

.member-title {
    color: #707070;
}


/* Update responsive design */
@media (max-width: 1200px) {
    .team-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem; /* Slightly smaller gap on medium screens */
    }
}

@media (max-width: 992px) {
    .team-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem; /* Even smaller gap on smaller screens */
    }
}

@media (max-width: 576px) {
    .team-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>
@endpush
