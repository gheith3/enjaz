<?php $__env->startSection('title', 'المنجزين'); ?>

<div>
    <div class="nav-bg">
        <?php echo e(view('components.layouts.nav')); ?>

        <div class="pt-5">
            <div class="container p-3 mb-3">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div>
                            <h2 class="text-center text-white">
                                المنجزين
                            </h2>
                        </div>
                        <div class="d-flex gap-1 mt-5">
                            <input type="text" class="form-control" wire:model="searchQuery" placeholder="العنوان">
                            <button class="btn btn-md text-white" wire:click="search">
                                <img src="<?php echo e(asset('images\app\searchIcon.svg')); ?>" alt="Search">
                            </button>
                        </div>
                        <div class="row justify-content-center mt-3">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->getCategories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button wire:click="setType('<?php echo e($key); ?>')"
                                    class="col-auto m-1 btn bt-sm rounded rounded-lg <?php echo e($key == $selected ? 'border-white text-white' : 'bg-white text-black-50'); ?>">
                                    <?php echo e($item); ?>

                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="container mt-5">
        <div class="row">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $achievers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achiever): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-3 p-3" wire:key="<?php echo e($achiever->id); ?>">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.achiever-card', ['achiever' => $achiever]);

$__html = app('livewire')->mount($__name, $__params, "card-{$achiever->id}", $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        <div class="pagination">
            <?php echo e($achievers->links('vendor.pagination.custom')); ?>

        </div>
    </div>

    <br>
    <br>
    <br>
</div>
<?php /**PATH E:\Projects\enjaz\resources\views/livewire/pages/achievers-page.blade.php ENDPATH**/ ?>