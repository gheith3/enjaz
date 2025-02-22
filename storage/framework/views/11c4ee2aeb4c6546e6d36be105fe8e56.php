<div>
    <div class="container">
        <div class="app-card pt-4 pb-2">
            <div class="row m-0 p-0 justify-content-center">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->getStatics(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-md-4 pb-3">
                        <div data-aos="fade-in" data-aos-duration="<?php echo e($loop->index + 800); ?>">
                            <div class="text-center">
                                <img src="<?php echo e($item['icon']); ?>"
                                     class="statistics-image"
                                     alt="achievement">
                            </div>
                            <div class=" mt-4">
                                <h5 class="text-center statistics-title text-black-50">
                                    <?php echo e($item['title']); ?>

                                </h5>
                                <h3 class="text-center statistics-data text-black-50">
                                    <?php echo e($item['count']); ?>

                                </h3>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
</div>
<?php /**PATH E:\Projects\enjaz\resources\views/livewire/statics.blade.php ENDPATH**/ ?>