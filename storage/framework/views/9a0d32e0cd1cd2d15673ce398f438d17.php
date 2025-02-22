<div id="landing-section">
    <div>
        <?php echo e(view("components.layouts.nav")); ?>

        <div style="min-height: 290px; background-size: auto; background-repeat: no-repeat">
            <div class="container">
                <div class="row justify-content-center pt-5">
                    <div class="col-md-4 text-center">
                        <img data-aos="fade-in" data-aos-duration="1800"
                             src="<?php echo e(Storage::url($image['image_path'])); ?>"
                             alt="Landing Image"
                             id="landing-image">
                    </div>
                    <div class="col-md-5 d-flex align-items-center pb-3">
                        <div data-aos="fade-in" data-aos-duration="1800">
                            <div class="pt-3">
                                <p id="landing-image-text" class="text-center">
                                    <?php echo e($image['description_ar']); ?>

                                </p>
                                <p id="landing-image-subtitle" class="text-center">
                                    <?php echo e($image['subtitle_ar']); ?>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
    <style>
        #landing-section {
            background-image: url('<?php echo e(asset("images/app/bg02.png")); ?>');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views/livewire/home-landing-images.blade.php ENDPATH**/ ?>