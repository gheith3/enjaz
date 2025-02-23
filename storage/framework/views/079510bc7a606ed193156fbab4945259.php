<?php $__env->startSection('title', $achiever->name_ar); ?>

<div>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('page-header', ['title' => $achiever->name_ar,'description' => $achiever->description_ar,'backgroundImage' => null,'headerImage' => $achiever->image_path]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3593935475-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    <div class="mt-5">


        <div class="mt-5">
            <div class="container">
                <div class="app-card p-3" id="statistics-card">
                    <div class="story-section">
                        <h2 class="story-title">قصتي</h2>
                        <div class="story-text" id="story-text" style="margin-top: 35px;">
                            <?php echo $achiever->story_ar; ?>

                        </div>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if(count($achiever->getSocialMediaLinks()) > 0): ?>

                        <hr>
                        <div class="row justify-content-center">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $achiever->getSocialMediaLinks(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social_media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-1 col-4 p-4 d-flex justify-content-center">
                                    <a data-aos="fade-in" data-aos-duration="<?php echo e($loop->index * 1500); ?>" target="_blank"
                                        href="<?php echo e($social_media['url']); ?>">
                                        <img src="<?php echo e($social_media['icon']); ?>" class="social-icon"
                                            alt="<?php echo e($social_media['account']); ?>">
                                    </a>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <!--[if BLOCK]><![endif]--><?php if($achievements != 0 || $types != 0): ?>
                    <div class="app-card" style="margin-top: 80px">
                        <div class="row justify-content-center">
                            <div class="col-md-4 pb-3 pt-3">
                                <div data-aos="fade-in" data-aos-duration="1800">
                                    <div class="text-center">
                                        <img src="<?php echo e(asset('images/badge.svg')); ?>" class="statistics-image"
                                            alt="achievement">
                                    </div>
                                    <div class=" mt-4">
                                        <h5 class="text-center statistics-title text-black-50">
                                            الإنجازات
                                        </h5>
                                        <h3 class="text-center statistics-data text-black-50">
                                            <?php echo e($achievements); ?>

                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 pb-3 pt-3">
                                <div data-aos="fade-in" data-aos-duration="1800">
                                    <div class="text-center">
                                        <img src="<?php echo e(asset('images/categories.svg')); ?>" class="statistics-image"
                                            alt="achievement">
                                    </div>
                                    <div class=" mt-4">
                                        <h5 class="text-center statistics-title text-black-50">
                                            المجالات
                                        </h5>
                                        <h3 class="text-center statistics-data text-black-50">
                                            <?php echo e($types); ?>

                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <div>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('new-achievements', ['sectionTitle' => 'الإنجازات','showAll' => true,'achieverId' => $achiever->id]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3593935475-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            </div>
        </div>



        <br>
    </div>
    <?php $__env->startPush('scripts'); ?>
        <script>
            function toggleStory() {
                var moreText = document.getElementById("more-text");
                var btn = document.getElementById("show-more-btn");
                if (moreText.style.display === "none") {
                    moreText.style.display = "inline";
                    btn.innerHTML = "اقرأ أقل <span class='up-arrow'>↑</span>";
                } else {
                    moreText.style.display = "none";
                    btn.innerHTML = "اقرأ المزيد <span class='down-arrow'>↓</span>";
                }
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views/livewire/single-achiever-hero.blade.php ENDPATH**/ ?>