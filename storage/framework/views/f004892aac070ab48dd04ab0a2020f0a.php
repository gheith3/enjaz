<div>
    <?php $__env->startSection('title', $achievement->title_ar); ?>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('page-header', ['backgroundImage' => $achievement->cover_image_path,'title' => $achievement->title_ar,'subTitle' => $achievement->achievementMainType->title_ar]);

$__html = app('livewire')->mount($__name, $__params, 'lw-2886305176-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>


    <div class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <?php if($achievement->organizations->count() > 0): ?>
                        <?php $__currentLoopData = $achievement->organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organization): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="pb-3" wire:key="<?php echo e($organization->id); ?>">
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
                    <?php endif; ?>
                    <?php if($achievement->publicFigures->count() > 0): ?>
                        <?php $__currentLoopData = $achievement->publicFigures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achiever): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="pb-3" wire:key="<?php echo e($achiever->id); ?>">
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
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>

                <div class="col-md-9">
                    <div class="app-card p-3" id="statistics-card">
                        <div class="d-flex gap-2">

                            <span class="achievement-label"><?php echo e($achievement->created_at->year); ?></span>

                            <span class="achievement-label"><?php echo e($achievement->achievementMainType->title_ar); ?></span>

                            <span class="achievement-label"><?php echo e($achievement->subAchievementType->title_ar); ?></span>

                            <span class="achievement-label"><?php echo e($achievement->achievementScope->title_ar); ?></span>

                        </div>
                        <div class="story-text mt-2" id="story-text">
                            <?php echo $achievement->description_ar; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('new-achievements', ['sectionTitle' => 'المزيد','activeAchievement' => $achievement->id,'achievementMainTypeId' => $achievement->achievement_main_type_id]);

$__html = app('livewire')->mount($__name, $__params, 'lw-2886305176-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
    <br>
</div>

<?php $__env->startPush('styles'); ?>
    <style>
        .achievement-label {
            font-size: 12px;
            border: 1px solid #1f4a50;
            color: #1f4a50;
            border-radius: 12px;
            padding: 5px 10px;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\single-achievement.blade.php ENDPATH**/ ?>