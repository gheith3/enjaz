<div>
    <?php if(count($this->achievements)): ?>
        <div class="container" id="new-achievement">
            <?php if (isset($component)) { $__componentOriginala30e4072acdaad0eb465b395014f037c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala30e4072acdaad0eb465b395014f037c = $attributes; } ?>
<?php $component = App\View\Components\SectionTitle::resolve(['title' => $sectionTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-title'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SectionTitle::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala30e4072acdaad0eb465b395014f037c)): ?>
<?php $attributes = $__attributesOriginala30e4072acdaad0eb465b395014f037c; ?>
<?php unset($__attributesOriginala30e4072acdaad0eb465b395014f037c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala30e4072acdaad0eb465b395014f037c)): ?>
<?php $component = $__componentOriginala30e4072acdaad0eb465b395014f037c; ?>
<?php unset($__componentOriginala30e4072acdaad0eb465b395014f037c); ?>
<?php endif; ?>
            <div class="row" id="new-achievements-boxes">
                <?php $__currentLoopData = $this->achievements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achievement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3">
                        <div data-aos="fade-in"
                             data-aos-duration="<?php echo e($loop->index * 1500); ?>">
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.achievement-card', ['achievement' => $achievement]);

$__html = app('livewire')->mount($__name, $__params, 'lw-2885470912-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if(!$showAll): ?>
                <div class="d-flex justify-content-end mt-5">
                    <a href="<?php echo e(route('achievements')); ?>" data-aos="fade-up" data-aos-duration="1000"
                       class="btn app-btn px-5 mx-3">
                        المزيد
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\new-achievements.blade.php ENDPATH**/ ?>