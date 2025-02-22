<footer id="app-footer" class="pt-5 pb-4">
    <div class="container text-center text-md-left">
        <div class="row text-center text-md-left">
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <img src="<?php echo e(Storage::url($settings->logo)); ?>" alt="Logo"
                     class="footer-logo">
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mt-3">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->getLinks()->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $text => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p class="text-end">
                        <a href="<?php echo e($link); ?>" class="text-white-50 link-text">
                            <?php echo e($text); ?>

                        </a>
                    </p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mt-3">
                <?php $__currentLoopData = $this->getLinks()->skip(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $text => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p class="text-end">
                        <a href="<?php echo e($link); ?>" class="text-white-50 link-text">
                            <?php echo e($text); ?>

                        </a>
                    </p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mt-3">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->getAddressDetails(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p class="text-end text-white-50">
                           <?php echo e($item['text']); ?>

                    </p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3 d-flex align-items-center justify-content-center">
                <p class="mb-0 text-white-50">
        <span>
            جميع الحقوق محفوظة
        </span>
                    <span>
            <?php echo e(now()->year); ?>

        </span>
                </p>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH E:\Projects\enjaz\resources\views/livewire/components/footer.blade.php ENDPATH**/ ?>