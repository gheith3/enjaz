<div>
    <!--[if BLOCK]><![endif]--><?php if($this->person != null): ?>
        <div id="public-figure">
            <div class="container">
                <div class="row justify-content-center pt-5">
                    <div class="col-md-4 text-center">
                        <img data-aos="fade-in" data-aos-duration="1800"
                             src="<?php echo e(Storage::url($this->person->image_path)); ?>"
                             alt="Landing Image"
                             style="width: 250px">
                    </div>
                    <div class="col-md-6 d-flex align-items-center pb-3">
                        <div data-aos="fade-in" data-aos-duration="1800">
                            <p class="public-figure-card-title"><?php echo e($this->person->name_ar); ?></p>
                            <a class="link-text" href="<?php echo e(route('achievers.show', ['achiever' => $this->person->id])); ?>">
                                <div id="public-figure-text" class="bg-white-50 rounded rounded-lg shadow-sm p-3">
                                    <?php echo e(str($this->person->story_ar)->limit(250)->toHtmlString()); ?>

                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div>
<?php /**PATH E:\Projects\enjaz\resources\views/livewire/components/selected-achiever.blade.php ENDPATH**/ ?>