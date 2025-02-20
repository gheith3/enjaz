<div>
    <div class="nav-bg">
        <?php echo e(view("components.layouts.nav")); ?>

    </div>
    <div style="min-height: 80vh">

        <div class="container mt-5">
            <?php $__currentLoopData = $this->getList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white shadow-sm rounded rounded-lg mb-3">
                    <div class="row m-0 p-0">
                        <div class="col-md-4 m-0 p-0">
                            <img src="<?php echo e(asset($item->images[0]['image'])); ?>"
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
                                    <a href="<?php echo e(route('media.images-albums.show', ['id' => $item->id])); ?>"
                                       class="btn app-btn read-more-btn">
                                        مشاهدة
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


<?php /**PATH E:\Projects\enjaz\resources\views\livewire\pages\images-albums-page.blade.php ENDPATH**/ ?>