<div class="ceo-message-container">
    <div class="message-content">
        <p class="message-text">
            <?php echo e($teemSettings->managerMessage); ?>

        </p>
    </div>
    <div class="ceo-info">
        <div class="ceo-name">
            <?php echo e($teemSettings->managerName); ?>

            <br>
            <?php echo e($teemSettings->managerPosition); ?>

        </div>
        <img src="<?php echo e(asset('images/app/ceo.png')); ?>" alt="CEO" class="ceo-image">
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.ceo-message-container {
    border: 1px solid #707070;
    background: transparent linear-gradient(180deg, #0B626A 0%, #063135 100%) 0% 0% no-repeat padding-box;
    box-shadow: 0px 3px 6px #00000029;
    opacity: 1;
    padding: 2rem;
    margin: 2rem 0;
    border-radius: 20px;
}

.message-text {
    color: #FFFFFF;
    line-height: 1.8;
    margin-top: 80px;
    margin-bottom: 2rem;
    text-align: right;
}

.ceo-info {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    flex-direction: row-reverse;
}

.ceo-image {
    background: transparent no-repeat padding-box;
    opacity: 1;
    width: 200px;
    height: 200px;
    object-fit: cover;
}

.ceo-name {
    color: #FFFFFF;
    text-align: right;
    line-height: 1.5;
    font-size: 1.2rem;
}
</style>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\about-section-ceo.blade.php ENDPATH**/ ?>