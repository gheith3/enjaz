<div>
    <div style="background-image: url('<?php echo e(Storage::url($backgroundImage ?? 'images/app/ABG.svg')); ?>')">
        <div class="nav-bg">
            <?php echo e(view("components.layouts.nav")); ?>

        </div>
        <div class="pt-5"
             style="background-image: url('<?php echo e(asset('images/app/AchieversBG.svg')); ?>'); min-height: 290px; background-size: auto; background-repeat: no-repeat">
            <div class="container p-3 mb-3">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div>
                            <h2 class="text-center text-white">
                                الإنجازات
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
            <?php $__currentLoopData = $this->achievements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achievement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <div class="bg-white shadow-sm rounded rounded-lg mb-3">
                        <div class="row m-0 p-0">
                            <div class="col-md-4 m-0 p-0">
                                <img src="<?php echo e(Storage::url($achievement->cover_image_path)); ?>"
                                     class="rounded"
                                     style="width: 100%; height: 250px"
                                     alt="">
                            </div>
                            <div class="col-md-8 d-flex align-items-center">
                                <div class="w-100">
                                    <p class="new-achievement-title" style="font-size: 20px; font-weight: bolder">
                                        <?php echo e($achievement->title_ar); ?>

                                    </p>
                                    <div class="d-flex gap-1 mt-1">
                                        <h6><span class="badge achievement-badge p-2">
                                        <?php echo e($achievement->achievementMainType->title_ar); ?>

                                    </span></h6>
                                        <h6><span class="badge achievement-badge p-2">
                                        <?php echo e($achievement->subAchievementType->title_ar); ?>

                                    </span></h6>
                                    </div>
                                    <p class="mt-1" style="font-size: 15px">
                                        <?php echo e(str($achievement->description_ar)->stripTags()->limit(200)); ?>

                                    </p>
                                    <div class="d-flex justify-content-end m-1">
                                        <a href="<?php echo e(route('achievement.show', ['achievement' => $achievement->id])); ?>"
                                           class="btn app-btn read-more-btn">
                                            اقرأ المزيد
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                
                
                
                
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <br>
    <br>
    <br>
</div>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\pages\achievements-page.blade.php ENDPATH**/ ?>