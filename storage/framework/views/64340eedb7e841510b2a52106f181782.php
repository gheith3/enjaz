<a href="<?php echo e(route('institutes.show', ['institute' => $organization->id])); ?>">
    <div class="app-card shadow-sm">
        <div style="position: relative;">
            <div class="p-2">
                <img src="<?php echo e(Storage::url($organization->logo_path)); ?>"
                     style="width: 100%; height: 180px"
                     alt="<?php echo e($organization->name_ar); ?>"/>
            </div>
            <div
                style="font-size: 16px; padding: 5px 15px; position: absolute; bottom: 0; width: 100%; background: rgb(19,96,104, 0.7); color: white; text-align: center;">
                <?php echo e($organization->name_ar); ?>

            </div>
        </div>
    </div>
</a>
<?php /**PATH E:\Projects\enjaz\resources\views/livewire/components/organization-card.blade.php ENDPATH**/ ?>