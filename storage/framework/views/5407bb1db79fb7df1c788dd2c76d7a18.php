<div>
    <div class="pb-0"
        style="background-image: url('<?php echo e($backgroundImage != null ? Storage::url($backgroundImage) : asset('images/app/ABG.svg')); ?>');
     height: 350px;
    display: flex; flex-direction: column; justify-content: space-between; background-size: cover; background-repeat: no-repeat">

        <div>
            <?php echo e(view('components.layouts.nav')); ?>

        </div>
        <div class="header-content">
            <div class="container">
                <div class="d-flex w-100 h-100 gap-3">
                    <?php if($headerImage): ?>
                        <div>
                            <img src="<?php echo e(Storage::url($headerImage)); ?>" style="max-width: 200px; min-width: 180px"
                                alt="logo" class="achiever-image">
                        </div>
                    <?php endif; ?>
                    <div>
                        <div class="d-flex align-items-center" style="height: 100%;">
                            <div>
                                <?php if($subTitle): ?>
                                    <h4 class="header-title text-white">
                                        <?php echo e($subTitle); ?>

                                    </h4>
                                <?php endif; ?>
                                <?php if($title): ?>
                                    <h1 class="header-title text-white">
                                        <?php echo e($title); ?>

                                    </h1>
                                <?php endif; ?>
                                <?php if($description): ?>
                                    <p class="header-description text-white-50">
                                        <?php echo e($description); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\page-header.blade.php ENDPATH**/ ?>