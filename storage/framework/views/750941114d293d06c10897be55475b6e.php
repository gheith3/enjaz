<div>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('page-header', ['title' => $institute->name_ar,'headerImage' => $institute->logo_path]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1806192305-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    <div class="mt-5">
        <div class="container">
            <div class="card" id="statistics-card">
                <div class="card-body">
                    <div class="row m-3 justify-content-center">
                        <div class="story-section">
                            <div class="story-text" id="story-text">
                                <?php echo $institute->description_ar; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(str($institute->manager_name_ar)->isNotEmpty()): ?>
        <div class="mt-5">
            <div class="row mx-3 justify-content-center">
                <div class="col-md-3 d-flex justify-content-center">
                    <img src="<?php echo e(Storage::url($institute->manager_image_path)); ?>"
                         alt="<?php echo e($institute->manager_name_en); ?>" style="width: 250px">
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div>
                        <div class="story-text" id="story-text">
                            <?php echo $institute->manager_message_ar; ?>

                        </div>
                        <div>
                            <h3 style="color: #357079">
                                <?php echo e($institute->manager_name_ar); ?>

                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <div class="mt-5">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('new-achievements', ['showAll' => true,'sectionTitle' => 'انجازاتنا','instituteId' => $institute->id]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1806192305-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>

    <div class="mt-5">
        <div class="container mt-5">
            <div>
                <h3>
                    المزيد
                </h3>
            </div>
            <div class="row justify-content-center">
                <?php $__currentLoopData = $this->getRandomInstitutes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organization): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-2" wire:key="<?php echo e($organization->id); ?>">
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
    </div>
    <br>
</div>














































































































































































































































































































































































































































<?php /**PATH E:\Projects\enjaz\resources\views\livewire\institute-page.blade.php ENDPATH**/ ?>