<div class="container">
    <div class="row justify-content-center">
        <?php $__currentLoopData = $this->institutes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $institute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-2">
                <a href="<?php echo e(route('institutes.show', ['institute' => $institute->id])); ?>">
                    <div class="card shadow-sm border-none mb-3" style="height: 180px">
                        <div class="card-body d-flex justify-content-center">
                            <img src="<?php echo e(Storage::url($institute->logo_path)); ?>"
                                 style="width: 100%"
                                 alt="logo"/>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>


<?php /**PATH E:\Projects\enjaz\resources\views\livewire\institutes-section-grid.blade.php ENDPATH**/ ?>