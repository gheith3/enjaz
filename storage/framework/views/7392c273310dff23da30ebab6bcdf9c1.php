<div>
    <div style="background-image: url('<?php echo e(asset($backgroundImage ?? 'images/app/ABG.svg')); ?>')">
        <?php echo e(view("components.layouts.nav")); ?>


        <div class="pt-5" style="background-image: url('<?php echo e(asset('images/app/AchieversBG.svg')); ?>'); min-height: 290px; background-size: auto; background-repeat: no-repeat">
            <div class="container p-3 mb-3">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div>
                            <h2 class="text-center text-white">
                                المؤسسات
                            </h2>
                        </div>
                        <div class="d-flex gap-1 mt-5">
                            <input type="text"
                                   class="form-control"
                                   wire:model="searchQuery"
                                   placeholder="العنوان">
                            <button class="btn btn-md text-white"
                                    wire:click="search">
                                <img src="<?php echo e(asset('images\app\searchIcon.svg')); ?>" alt="Search">
                            </button>
                        </div>
                        <div class="row justify-content-center mt-3">
                            <?php $__currentLoopData = $this->getCategories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button
                                    wire:click="setType('<?php echo e($key); ?>')"
                                    class="col-auto m-1 btn bt-sm rounded rounded-lg <?php echo e($key == $selected ? 'border-white text-white' : 'bg-white text-black-50'); ?>">
                                    <?php echo e($item); ?>

                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="container mt-5">
        <div class="row justify-content-center">
            <?php $__currentLoopData = $this->organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organization): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-2 pb-2" wire:key="<?php echo e($organization->id); ?>">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.organization-card', ['organization' => $organization]);

$__html = app('livewire')->mount($__name, $__params, "card-{$organization->id}", $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <br>
    <br>
    <br>
</div>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\pages\organizations-page.blade.php ENDPATH**/ ?>