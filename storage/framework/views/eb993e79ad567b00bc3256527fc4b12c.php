<a class="link-text" href="<?php echo e(route('achievers.show', ['achiever' => $achiever->id])); ?>">
    <div class="achievers-image-card">
        <div class="app-card">
            <div class="achievers-image-d">
                <div style="position: relative;">
                    <img src="<?php echo e(Storage::url($achiever->image_path)); ?>"
                         alt="<?php echo e($achiever->name_ar); ?>"/>
                    <div class="achievers-image-card-name">
                        <?php echo e($achiever->name_ar); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</a>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\components\achiever-card.blade.php ENDPATH**/ ?>