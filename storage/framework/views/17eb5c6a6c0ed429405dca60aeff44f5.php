<div>
    <div class="container mt-5">
        <?php $__currentLoopData = $this->getList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="row mb-5">
                <div class="col-12 pb-3">
                    <?php if (isset($component)) { $__componentOriginala30e4072acdaad0eb465b395014f037c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala30e4072acdaad0eb465b395014f037c = $attributes; } ?>
<?php $component = App\View\Components\SectionTitle::resolve(['title' => $item['category']->title_ar] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                </div>
                <?php $__currentLoopData = collect($item['achievers'])->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achiever): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3 p-3"
                         data-aos="fade-in"
                         data-aos-duration="<?php echo e($loop->index * 800 + 1500); ?>" wire:key="<?php echo e($achiever->id); ?>">
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
                <div class="col-12">
                    <div data-aos="fade-in" data-aos-duration="800">
                        <div class="d-flex justify-content-end mt-5">
                            <a class="btn app-btn px-5 mx-3">
                                المزيد
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>
</div>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\achievers.blade.php ENDPATH**/ ?>