<?php $__env->startSection('title', 'الإعلام | الإصدارات'); ?>

<div>
    <div class="bg-primary">
        <?php echo e(view('components.layouts.nav')); ?>

        <div class="py-5">
            <h1 class="text-white text-center mb-4">
                الإصدارات
            </h1>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" wire:model.live="searchQuery" class="form-control" placeholder="ابحث...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="min-vh-75">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <?php $__currentLoopData = $publications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card shadow-sm mb-4">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="<?php echo e(Storage::url($item->cover_image)); ?>"
                                        class="img-fluid rounded-start h-100 object-fit-cover"
                                        alt="<?php echo e($item->title); ?>">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body d-flex flex-column h-100">
                                        <h5 class="card-title fw-bold">
                                            <?php echo e($item->title); ?>

                                        </h5>
                                        <p class="card-text flex-grow-1">
                                            <?php echo e(str($item->description)->stripTags()); ?>

                                        </p>
                                        <div class="text-end mt-3">
                                            <a href="<?php echo e($item->url); ?>" target="_blank" class="btn btn-primary">
                                                تحميل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mb-5">
            <?php echo e($publications->links('vendor.pagination.custom')); ?>

        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
    <style>
             
    </style>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\pages\publications-page.blade.php ENDPATH**/ ?>