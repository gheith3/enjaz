<div class="achievers-grid">
    <?php $__currentLoopData = $this->achievers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achiever): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('achievers.show', ['achiever' => $achiever->id])); ?>">
            <div class="achiever-card">
                <div class="card-image" style="background-image: url('<?php echo e(asset($achiever->image_path)); ?>')">
                    
                    <div class="name-badge"><?php echo e($achiever->name_ar); ?></div>
                </div>
            </div>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<?php $__env->startPush('styles'); ?>

<style>

</style>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\achievers-section-grid.blade.php ENDPATH**/ ?>