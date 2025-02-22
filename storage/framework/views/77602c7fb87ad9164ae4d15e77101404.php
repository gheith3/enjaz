<div id="media-page">
    <div class="nav-bg">
        <?php echo e(view("components.layouts.nav")); ?>

    </div>

    <div style="margin-top: 120px" class="container">
        <div class="row m-0">
            <div class="col-md-6">
                <div class="media-card photos-card"
                     style="background-image: url('<?php echo e(asset('images/media/m01.jpg')); ?>')">
                    <div class="card-overlay">
                        <img src="<?php echo e(asset('images\app\firstBookIcon.svg')); ?>" alt="Photos" class="card-icon">
                        <a href="<?php echo e($settings->about_us_document); ?>"
                           target="_blank"
                           class="link-text d-flex">
                            <h3>عنا</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pb-4">
                <div class="media-card interviews-card"
                     style="background-image: url('<?php echo e(asset('images/media/m03.jpg')); ?>');">
                    <div class="card-overlay">
                        <img src="<?php echo e(asset('images\app\YTI.svg')); ?>" alt="Interviews" class="card-icon">
                        <a href="<?php echo e(route('media.videos')); ?>"
                           class="link-text">
                            <h3>لقاءات مسجلة</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pb-4">
                    <div class="media-card photos-card"
                     style="background-image: url('<?php echo e(asset('images/media/m02.jpg')); ?>');">
                    <div class="card-overlay">
                        <img src="<?php echo e(asset('images\app\firstBookIcon.svg')); ?>" alt="Photos" class="card-icon">
                        <a href="<?php echo e(route('media.images-albums')); ?>"
                           class="link-text">
                            <h3>مكتبة الصور</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="media-card photos-card"
                     style="background-image: url('<?php echo e(asset('images/media/m04.jpg')); ?>')">
                    <div class="card-overlay">
                        <img src="<?php echo e(asset('images\app\firstBookIcon.svg')); ?>" alt="Photos" class="card-icon">
                        <a href="<?php echo e(route('media.publications')); ?>"
                           class="link-text">
                            <h3>الإصدارات</h3>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
    <br>

</div>

<?php $__env->startPush('styles'); ?>
    <style>


        .media-library {
            padding: 1rem;
            margin: 1rem 0;
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

        .media-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr 1.3fr;
            gap: 1.5rem;
        }

        /* Mobile Optimization */
        @media (max-width: 1024px) {
            .media-grid {
                grid-template-columns: 1fr 1fr;
            }

            .books-grid {
                grid-column: span 2;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .media-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .media-card {
                min-height: 250px;
            }

            .books-grid {
                grid-column: auto;
                grid-template-columns: 1fr;
            }

            .card-overlay,
            .book-overlay {
                padding: 1rem;
            }

            .card-icon {
                width: 50px;
                height: 50px;
            }

            h3 {
                font-size: 1.2rem;
            }

            .media-columns {
                gap: 1rem;
            }
        }

        /* Existing styles */
        .media-card {
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            min-height: 345px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.29);
            transition: transform 0.3s ease;
        }

        .media-card:hover {
            transform: translateY(-5px);
        }

        .profile-card {
            background: linear-gradient(180deg, #0B626A, #063135);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .media-columns {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .interviews-card,
        .photos-card {
            background-size: cover;
            background-position: center;
        }

        .card-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            background: linear-gradient(180deg, rgba(11, 98, 106, 0.8), rgba(6, 49, 53, 0.8));
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .books-grid {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .book-card {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            height: 345px;
            transition: transform 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-5px);
        }

        .book-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .book-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            background: linear-gradient(180deg, rgba(11, 98, 106, 0.53), rgba(6, 49, 53, 0.53));
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .card-icon {
            width: 77px;
            height: 77px;
        }

        h3 {
            color: white;
            font-size: 1.5rem;
            margin: 0;
        }

        /* Touch device optimization */
        @media (hover: none) {
            .media-card:hover,
            .book-card:hover {
                transform: none;
            }
        }

        /* Loading state */
        .media-card.loading {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views/livewire/pages/media-page.blade.php ENDPATH**/ ?>