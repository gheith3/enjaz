<?php if (isset($component)) { $__componentOriginal9b945b32438afb742355861768089b04 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9b945b32438afb742355861768089b04 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <p class="font-bold"><?php echo e($group . ' - '. $translationKey); ?></p>
    <div class="text-base grid">
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php ($id = $group.$translationKey.'translations'.$locale); ?>
            <div
                class="flex items-center"
                x-data="{
                    editing: false,
                    openForm(){
                        $dispatch('close-forms');
                        this.editing = true;
                        $nextTick(() => {
                            setTimeout(() => {
                                $refs.input.focus();
                            }, 50); //Adding a small delay makes this way more consistent
                        });
                    },
                    closeWithSave(){
                        if (this.editing){
                            this.closeEdit();
                            $wire.save(this.locale);
                        }
                    },
                    closeWithCancel(){
                        if (this.editing){
                            this.closeEdit();
                            $wire.cancel();
                        }
                    },
                    closeEdit() {
                        this.editing = false;
                    },
                    locale: '<?php echo e($locale); ?>'
                }"
                @click.outside="closeWithSave"
                @close-forms.window="closeWithSave()"
            >
                <label
                    class="w-16 font-bold"
                    for="<?php echo e($id); ?>"
                    x-ref="label"
                    x-bind:tabindex="!editing && '0'"
                    @focus="openForm"
                >
                    <?php echo e($locale); ?>:
                </label>
                <?php if (isset($component)) { $__componentOriginal549c94d872270b69c72bdf48cb183bc9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal549c94d872270b69c72bdf48cb183bc9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.link','data' => ['xShow' => '!editing','@click.prevent' => 'openForm','class' => 'w-full cursor-pointer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => '!editing','@click.prevent' => 'openForm','class' => 'w-full cursor-pointer']); ?>
                    <div class="w-full p-2">
                        <!--[if BLOCK]><![endif]--><?php if(isset($translations[$locale]) && !empty(trim($translations[$locale]))): ?>
                            <?php echo e($translations[$locale]); ?>

                        <?php else: ?>
                            <span class="text-gray-400 decoration-gray-400">
                                <?php echo app('translator')->get('filament-translation-manager::messages.missing_translation'); ?>
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal549c94d872270b69c72bdf48cb183bc9)): ?>
<?php $attributes = $__attributesOriginal549c94d872270b69c72bdf48cb183bc9; ?>
<?php unset($__attributesOriginal549c94d872270b69c72bdf48cb183bc9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal549c94d872270b69c72bdf48cb183bc9)): ?>
<?php $component = $__componentOriginal549c94d872270b69c72bdf48cb183bc9; ?>
<?php unset($__componentOriginal549c94d872270b69c72bdf48cb183bc9); ?>
<?php endif; ?>
                <div
                    class="block w-full flex items-center space-x-2"
                    x-show="editing"
                >
                    <form @submit.prevent="closeWithSave" class="w-full">
                        <input
                            wire:model.defer="translations.<?php echo e($locale); ?>"
                            class="<?php echo e('block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 '); ?>"
                            id="<?php echo e($id); ?>"
                            type="text"
                            x-ref="input"
                        >
                    </form>
                    <div class="flex items-center align-center">
                        <button @click="closeWithCancel">
                            <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['alias' => 'filament-chained-translation-manager::cancel-translation','icon' => 'heroicon-o-x-mark','class' => 'w-5 h-5 text-danger-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['alias' => 'filament-chained-translation-manager::cancel-translation','icon' => 'heroicon-o-x-mark','class' => 'w-5 h-5 text-danger-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9b945b32438afb742355861768089b04)): ?>
<?php $attributes = $__attributesOriginal9b945b32438afb742355861768089b04; ?>
<?php unset($__attributesOriginal9b945b32438afb742355861768089b04); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9b945b32438afb742355861768089b04)): ?>
<?php $component = $__componentOriginal9b945b32438afb742355861768089b04; ?>
<?php unset($__componentOriginal9b945b32438afb742355861768089b04); ?>
<?php endif; ?>
<?php /**PATH E:\Projects\enjaz\vendor\statikbe\laravel-filament-chained-translation-manager\src\/../resources/views/livewire/translation-edit-form.blade.php ENDPATH**/ ?>