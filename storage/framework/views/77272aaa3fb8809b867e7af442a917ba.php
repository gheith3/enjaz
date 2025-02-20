<div>
    <div class="container">
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php $__currentLoopData = $this->profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="swiper-slide p-4"
                         id="slide-<?php echo e($loop->index); ?>"
                         data-id="<?php echo e($profile['id']); ?>">
                        <div class="text-center">
                            <img style=""
                                 src="<?php echo e($profile['src']); ?>"
                                 alt="Profile 1"
                                 loading="lazy"
                            />
                            <div class="swiper-lazy-preloader"></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
    />
    <style>
        .swiper {
            width: 100%;
            max-width: 1200px;
            height: 400px;
            padding: 4rem 0;
        }

        .swiper-slide {
            width: fit-content;
            height: fit-content;
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            opacity: 0.6;
            transform: scale(0.8);
            align-items: center;
            background: rgba(255, 255, 255, 0.6);
        }

        .slid-text {
            visibility: hidden;
        }

        .swiper-slide-active {
            opacity: 1;
            transform: scale(5);
            z-index: 2;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background: white;

        }

        .swiper-slide-active .slid-text {
            visibility: visible;
        }

        .swiper-slide-prev,
        .swiper-slide-next {
            opacity: 0.8;
            transform: scale(0.9);
            z-index: 1;
        }

        .swiper-slide {
            background-size: cover;
            background-position: center;
            /*background-origin: content-box;*/
        }

        .swiper-slide img {
            height: 50px;
            object-fit: cover;
        }

        .swiper-slide-active img {
            height: 80px;
            transition: all 0.3s ease;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: #006064;
            background: rgba(255, 255, 255, 0.8);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 18px;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: rgba(255, 255, 255, 1);
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        let lastSelectId = "init-id";
        const swiper = new Swiper('.swiper', {
            slidesPerView: 'auto',
            spaceBetween: 40,
            centeredSlides: true,
            loop: true,
            speed: 800,
            autoplay: {
                delay: 20000,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                },
                640: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                1024: {
                    slidesPerView: 3,
                }
            },
            effect: 'coverflow',
            coverflowEffect: {
                // rotate: 0,
                // stretch: 0,
                // depth: 100,
                // modifier: 1,
                slideShadows: false,
            },
            on: {
                realIndexChange: function (swiper) {
                    // console.log(swiper.realIndex);
                    const activeSlide = document.querySelector('#slide-'+swiper.realIndex);
                    let textContent = activeSlide.dataset.id;
                    updateActiveSlideInfo(textContent);
                }
            },
        });
        // swiper.on('realIndexChange', function () {
        //     updateActiveSlideInfo();
        // });
        // swiper.on('afterInit', function () {
        //     updateActiveSlideInfo();
        // });

        // swiper.init();


        // updateActiveSlideInfo();

        function updateActiveSlideInfo(index) {
            Livewire.dispatch('slide-changed', {selectedId: index});
        }
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\profile-slider.blade.php ENDPATH**/ ?>