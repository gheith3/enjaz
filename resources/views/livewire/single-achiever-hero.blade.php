<div>

    <livewire:page-header
        :title="$achiever->name_ar"
        :header-image="$achiever->image_path" />

    <div class="mt-5">
        <div class="social-icons" style="display: flex; justify-content: center; align-items: center; gap: 10px;">
            <div class="social-icon-container">
                <span class="social-text">@twitter</span>
                <img src="{{ asset('images/app/twitterAch.svg') }}" alt="Phone" class="social-icon">

            </div>
            <div class="social-icon-container">
                <span class="social-text">@Instagram</span>
                <img src="{{ asset('images/app/instagramAch.svg') }}" alt="Email" class="social-icon">

            </div>
            <div class="social-icon-container">
                <span class="social-text">email@email.com</span>
                <img src="{{ asset('images/app/emailAch.svg') }}" alt="Email" class="social-icon">

            </div>
            <div class="social-icon-container">
                <span class="social-text">@LinkedIn</span>
                <img src="{{ asset('images/app/linkedinAch.svg') }}" alt="Email" class="social-icon">
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="container">
            <div class="card" id="statistics-card">
                <div class="card-body">
                    <div class="row m-3 mt-5 justify-content-center">
                        <div class="story-section">
                            <h2 class="story-title">قصتي</h2>
                            <div class="story-text" id="story-text" style="margin-top: 35px;">
                                {!! $achiever->description_ar !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="container">
            <div class="card" id="statistics-card">
                <div class="card-body">
                    <div class="row m-3 mt-5 justify-content-center">
                        <div class="col-md-4 pb-2">
                            <div data-aos="fade-up" data-aos-duration="1000">
                                <div class="statistics-info">
                                    <h1 class="text-center statistics-data">{{$achievements}}</h1>
                                    <h4 class="text-center statistics-title">الإنجازات</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 pb-2">
                            <div data-aos="fade-up" data-aos-duration="1500">
                                <div class="statistics-info">
                                    <h1 class="text-center statistics-data">{{$types}}</h1>
                                    <h4 class="text-center statistics-title">المجالات</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 media-library">
        <div>
            <h2 class="story-title">المكتبة المرئية</h2>
        </div>
        <div class="media-grid">
            <!-- Interviews -->
            <div class="media-card interviews-card"
                 style="background-image: url('{{ asset('images/app/firstBookCover.png') }}');">
                <div class="card-overlay">
                    <img src="{{ asset('images/app/YTI.svg') }}" alt="Interviews" class="card-icon">
                    <h3>لقاءات مسجلة</h3>
                </div>
            </div>

            <!-- Photos -->
            <div class="media-card photos-card"
                 style="background-image: url('{{ asset('images/app/secondBookCover.png') }}');">
                <div class="card-overlay">
                    <img src="{{ asset('images/app/firstBookIcon.svg') }}" alt="Photos" class="card-icon">
                    <h3>صور متنوعة</h3>
                </div>
            </div>

            <!-- Empty Column for Layout -->
            <div class="media-card empty-card" style="background-color: transparent;"></div>
        </div>
    </div>

    <div class="mt-5 achievement-library">
        <div>
            <h2 class="story-title">إنجازاتي</h2>
        </div>
        <div class="filters-section">
            <div class="filters-row">
                <div class="filter-btn active" onclick="filterAchievements('all')">الكل</div>
                <div class="filter-btn" onclick="filterAchievements('local')">محلي</div>
                <div class="filter-btn" onclick="filterAchievements('international')">دولي</div>
            </div>
        </div>
        <div class="achievement-grid">
            <!-- Achievement Card 1 -->
            <div class="achievement-card">
                <img src="{{ asset('images/app/person.png') }}" alt="Achievement Image" class="achievement-image">
                <div class="tags-container">
                    <span class="tag">الرياضة</span>
                    <span class="tag">كرة القدم</span>
                </div>
                <div class="achievement-date">23 May 2023</div>
                <p class="achievement-description">حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية» من
                    جامعة السلطان قابوس على إنجازين مشرفين على.</p>
                <button class="read-more-btn">إقرأ المزيد</button>
            </div>

            <!-- Achievement Card 2 -->
            <div class="achievement-card">
                <img src="{{ asset('images/app/person.png') }}" alt="Achievement Image" class="achievement-image">
                <div class="tags-container">
                    <span class="tag">الرياضة</span>
                    <span class="tag">كرة القدم</span>
                </div>
                <div class="achievement-date">23 May 2023</div>
                <p class="achievement-description">حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية» من
                    جامعة السلطان قابوس على إنجازين مشرفين على المستوى الإقليمي والدولي جاء كالآتي: الميدالية الفضية
                    …</p>
                <button class="read-more-btn">إقرأ المزيد</button>
            </div>

            <div class="achievement-card">
                <img src="{{ asset('images/app/person.png') }}" alt="Achievement Image" class="achievement-image">
                <div class="tags-container">
                    <span class="tag">الرياضة</span>
                    <span class="tag">كرة القدم</span>
                </div>
                <div class="achievement-date">23 May 2023</div>
                <p class="achievement-description">حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية» من
                    جامعة السلطان قابوس على إنجازين مشرفين على المستوى الإقليمي والدولي جاء كالآتي: الميدالية الفضية
                    …</p>
                <button class="read-more-btn">إقرأ المزيد</button>
            </div>
            <div class="achievement-card">
                <img src="{{ asset('images/app/person.png') }}" alt="Achievement Image" class="achievement-image">
                <div class="tags-container">
                    <span class="tag">الرياضة</span>
                    <span class="tag">كرة القدم</span>
                </div>
                <div class="achievement-date">23 May 2023</div>
                <p class="achievement-description">حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية» من
                    جامعة السلطان قابوس على إنجازين مشرفين على المستوى الإقليمي والدولي جاء كالآتي: الميدالية الفضية
                    …</p>
                <button class="read-more-btn">إقرأ المزيد</button>
            </div>
            <div class="achievement-card">
                <img src="{{ asset('images/app/person.png') }}" alt="Achievement Image" class="achievement-image">
                <div class="tags-container">
                    <span class="tag">الرياضة</span>
                    <span class="tag">كرة القدم</span>
                </div>
                <div class="achievement-date">23 May 2023</div>
                <p class="achievement-description">حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية» من
                    جامعة السلطان قابوس على إنجازين مشرفين على المستوى الإقليمي والدولي جاء كالآتي: الميدالية الفضية
                    …</p>
                <button class="read-more-btn">إقرأ المزيد</button>
            </div>

            <div class="achievement-card">
                <img src="{{ asset('images/app/person.png') }}" alt="Achievement Image" class="achievement-image">
                <div class="tags-container">
                    <span class="tag">الرياضة</span>
                    <span class="tag">كرة القدم</span>
                </div>
                <div class="achievement-date">23 May 2023</div>
                <p class="achievement-description">حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية» من
                    جامعة السلطان قابوس على إنجازين مشرفين على المستوى الإقليمي والدولي جاء كالآتي: الميدالية الفضية
                    …</p>
                <button class="read-more-btn">إقرأ المزيد</button>
            </div>

            <div class="achievement-card">
                <img src="{{ asset('images/app/person.png') }}" alt="Achievement Image" class="achievement-image">
                <div class="tags-container">
                    <span class="tag">الرياضة</span>
                    <span class="tag">كرة القدم</span>
                </div>
                <div class="achievement-date">23 May 2023</div>
                <p class="achievement-description">حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية» من
                    جامعة السلطان قابوس على إنجازين مشرفين على المستوى الإقليمي والدولي جاء كالآتي: الميدالية الفضية
                    …</p>
                <button class="read-more-btn">إقرأ المزيد</button>
            </div>

            <div class="achievement-card">
                <img src="{{ asset('images/app/person.png') }}" alt="Achievement Image" class="achievement-image">
                <div class="tags-container">
                    <span class="tag">الرياضة</span>
                    <span class="tag">كرة القدم</span>
                </div>
                <div class="achievement-date">23 May 2023</div>
                <p class="achievement-description">حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية» من
                    جامعة السلطان قابوس على إنجازين مشرفين على المستوى الإقليمي والدولي جاء كالآتي: الميدالية الفضية
                    …</p>
                <button class="read-more-btn">إقرأ المزيد</button>
            </div>
        </div>
    </div>
    <div class="mt-5 more-library">
        <div>
            <h2 class="more-title">المزيد</h2>
        </div>
        <div class="achievers-grid">
            <div class="achiever-card">
                <div class="card-image"
                     style="background-image: url('{{ asset('images/app/achieverExampleImg3.png') }}')">
                    <div class="category-badge">فئة 1</div>
                    <div class="name-badge">اسم المحقق 1</div>
                </div>
            </div>
            <div class="achiever-card">
                <div class="card-image"
                     style="background-image: url('{{ asset('images/app/achieverExampleImg1.png') }}')">
                    <div class="category-badge">فئة 2</div>
                    <div class="name-badge">اسم المحقق 2</div>
                </div>
            </div>
            <div class="achiever-card">
                <div class="card-image"
                     style="background-image: url('{{ asset('images/app/achieverExampleImg2.png') }}')">
                    <div class="category-badge">فئة 3</div>
                    <div class="name-badge">اسم المحقق 3</div>
                </div>
            </div>
            <div class="achiever-card">
                <div class="card-image"
                     style="background-image: url('{{ asset('images/app/achieverExampleImg3.png') }}')">
                    <div class="category-badge">فئة 4</div>
                    <div class="name-badge">اسم المحقق 4</div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <script>
        function toggleStory() {
            var moreText = document.getElementById("more-text");
            var btn = document.getElementById("show-more-btn");
            if (moreText.style.display === "none") {
                moreText.style.display = "inline";
                btn.innerHTML = "اقرأ أقل <span class='up-arrow'>↑</span>";
            } else {
                moreText.style.display = "none";
                btn.innerHTML = "اقرأ المزيد <span class='down-arrow'>↓</span>";
            }
        }
    </script>
    <style>
        #hero-section {
            position: relative;
            width: 100%;
            min-height: 530px;
            height: auto;
            overflow: hidden;
            padding: 0;
            margin: 0;
        }

        .background-container {
            position: relative;
            width: 100%;
            height: 100%;
            margin: 0;
        }

        .bg-main {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        .content-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            z-index: 2;
            padding-top: 0;
        }

        .hero-content-container {
            padding: 1rem 0;
            margin-top: 0;
        }

        .achiever-image-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .achiever-image {
            width: 100%;
            height: auto;
            max-height: 380px;
            object-fit: contain;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .hero-text {
            text-align: right;
            padding: 1rem;
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            color: white;
            margin-bottom: 1rem;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-subtitle {
            color: white;
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .hero-description {
            color: white;
            font-size: clamp(1rem, 2vw, 1.25rem);
            opacity: 0.8;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .social-icon {
            width: 30px;
            height: auto;
            margin: 0 10px;
        }

        .social-text {

            color: black;
            margin: 0 10px;
        }

        .statistics-data {
            color: #096770;
            font-weight: bold; /* Updated color */
        }

        .story-title {
            color: #096770;
            font-weight: bold; /* Updated color */
        }

        .more-library {
            padding: 2rem 1rem;
            margin: 1rem 0;
        }

        .more-title {
            color: #096770;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2rem;
            font-size: clamp(1.5rem, 4vw, 2rem);
        }

        .media-title {
            color: #096770;
            font-weight: bold;
            font-size: 2rem; /* Updated color */
        }

        .media-library {
            padding: 1rem;
            margin: 1rem 0;
        }

        .media-title {
            color: #096770;
            font-weight: bold;
        }

        .title-icon {
            width: 40px;
            height: 40px;
        }


        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .media-card {
            margin-top: 20px;
            position: relative;
            height: 280px; /* Adjust height as needed */
            background-size: cover;
            background-position: center;
            border-radius: 20px;
            overflow: hidden;
        }

        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent overlay */
            color: white;
            text-align: center;
        }

        .card-icon {
            width: 50px; /* Adjust icon size as needed */
            margin-bottom: 10px;
        }

        .empty-card {
            /* Optional styles for the empty card */
            height: 200px; /* Match the height of the other cards */
        }

        .achievement-library {
            padding: 1rem;
            margin: 1rem 0;
        }

        .achievement-title {
            text-align: center;
            font-size: 1.5rem;
            color: #0B626A; /* Title color */
            margin-bottom: 1.5rem;
        }

        .achievement-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .achievement-card {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background-color: var(--color-white); /* Card background color */
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16); /* Match shadow */
            padding: 1rem; /* Padding inside the card */
        }

        .achievement-image {
            width: 100%; /* Full width */
            height: auto; /* Maintain aspect ratio */
            border-radius: 20px;
        }

        .tags-container {
            display: flex;
            justify-content: flex-strt; /* Center tags */
            gap: 0.5rem; /* Space between tags */
            margin: 0.5rem 0; /* Margin around tags */
        }

        .tag {
            background-color: #D4F8F8; /* Light blue color for tags */
            color: var(--color-black); /* Text color */
            padding: 0.3rem 0.5rem; /* Padding inside tags */
            border-radius: 20px;
            font-size: 12px; /* Tag font size */
        }

        .achievement-date {
            font-size: 12px; /* Date font size */
            margin: 0.5rem 0; /* Margin around date */
            text-align: start; /* Center date */
        }

        .achievement-description {
            font-size: 12px; /* Description font size */
            margin: 0.5rem 0; /* Margin around description */
            text-align: start; /* Center description */
        }

        .read-more-btn {
            background-color: #096770; /* Green button */
            color: white; /* Text color */
            border: none; /* No border */
            padding: 0.5rem 1rem; /* Padding inside button */
            border-radius: 20px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor */
            display: block; /* Block display */
            margin: 0 auto; /* Center button */
            font-size: 12px; /* Button font size */
        }


        .filters-section {
            padding: 2rem 1rem;
            background-color: white;
        }

        .filters-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            justify-content: flex-start;
        }

        .filter-btn {
            display: flex;
            align-items: start;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            border: 1px solid #666;
            background: white;
            color: #666;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: var(--font-size-5xl);
        }

        .filter-btn.active {
            background: #0B626A;
            color: white;
            border-color: #0B626A;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-icon {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .filter-btn.active .filter-icon {
            filter: brightness(0) invert(1);
        }


        @media (max-width: 768px) {
            #hero-section {
                min-height: auto;
                padding: 0;
            }

            .hero-content-container {
                padding: 1rem 0;
                margin-top: 0;
            }

            .hero-text {
                text-align: center;
                padding: 1rem 0;
            }

            .achiever-image-container {
                max-width: 300px;
            }

            .achiever-image {
                max-height: 300px;
            }
        }

        @media (max-width: 480px) {
            .achiever-image-container {
                max-width: 250px;
            }

            .achiever-image {
                max-height: 250px;
            }
        }

        .achievers-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            padding: 1rem;
            margin: 0 auto;
            max-width: 1600px;
        }

        .achiever-card {
            border-radius: 26px;
            overflow: hidden;
            position: relative;
            aspect-ratio: 3/4;
            transition: transform 0.3s ease;
            height: 300px;
            margin: 0 auto;
            width: 100%;
        }

        .card-image {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            position: relative;
            border-radius: 26px;
        }

        .category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            font-weight: 500;
        }

        .name-badge {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            color: #0B626A;
            font-weight: 500;
            border: 2px solid white;
            white-space: nowrap;
        }

        @media (max-width: 1200px) {
            .achievers-grid {
                grid-template-columns: repeat(3, 1fr);
                padding: 1rem;
                gap: 1rem;
            }

            .achiever-card {
                height: 250px;
            }
        }

        @media (max-width: 992px) {
            .achievers-grid {
                grid-template-columns: repeat(2, 1fr);
                padding: 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .more-library {
                padding: 1rem 0.5rem;
            }

            .achievers-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
                padding: 0.5rem;
            }

            .achiever-card {
                height: 200px;
            }

            .category-badge {
                font-size: 0.8rem;
                padding: 0.3rem 0.8rem;
            }

            .name-badge {
                font-size: 0.8rem;
                padding: 0.3rem 0.8rem;
                width: 80%;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .more-library {
                padding: 1rem 0.25rem;
            }

            .achievers-grid {
                grid-template-columns: repeat(1, 1fr);
                gap: 1rem;
                padding: 0.5rem;
                max-width: 300px;
                margin: 0 auto;
            }

            .achiever-card {
                height: 250px;
                max-width: 100%;
            }

            .more-title {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }

            .category-badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.6rem;
            }

            .name-badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.6rem;
                width: 90%;
            }
        }

        /* Add smooth transitions */
        .hero-title, .hero-subtitle, .hero-description,
        .achievement-card, .media-card, .achiever-card {
            transition: all 0.3s ease-in-out;
        }

        /* Improve card responsiveness */
        .achievement-card, .media-card {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Improve image handling */
        .achievement-image, .media-card img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        /* Remove any top margin from the first element in the page */
        .background-container > *:first-child {
            margin-top: 0;
        }
    </style>
@endpush
