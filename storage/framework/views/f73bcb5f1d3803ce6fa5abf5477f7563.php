<div>
    <div class="nav-bg">
        <?php echo e(view("components.layouts.nav")); ?>

        <div class="mt-5 mb-2">
            <h1 class="text-white text-center">
                الإصدارات
            </h1>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="search-box">
                            <input type="text" 
                                   wire:model.live="searchQuery"
                                   class="form-control"
                                   placeholder="ابحث...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>
    <div style="min-height: 80vh">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $publications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white shadow-sm rounded rounded-lg mb-3">
                            <div class="row m-0 p-0">
                                <div class="col-md-4 m-0 p-0">
                                    <img src="<?php echo e(Storage::url($item->cover_image)); ?>"
                                         class="rounded"
                                         style="width: 100%; height: 250px"
                                         alt="">
                                </div>
                                <div class="col-md-8 d-flex align-items-center">
                                    <div class="w-100">
                                        <p class="new-achievement-title" style="font-size: 20px; font-weight: bolder">
                                            <?php echo e($item->title); ?>

                                        </p>

                                        <p class="mt-1" style="font-size: 15px">
                                            <?php echo e(str($item->description)->stripTags()); ?>

                                        </p>
                                        <div class="d-flex justify-content-end m-1">
                                            <a href="<?php echo e($item->url); ?>"
                                               target="_blank"
                                               class="btn app-btn read-more-btn">
                                                تحميل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            <div class="pagination">
                <?php echo e($publications->links('vendor.pagination.custom')); ?>

            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
    <style>
        
        
        
        
        
        
    </style>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views/livewire/pages/publications-page.blade.php ENDPATH**/ ?>