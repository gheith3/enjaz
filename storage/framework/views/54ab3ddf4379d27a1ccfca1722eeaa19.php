<div class="messages-section">
    <div class="container">


        <div class="row">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4">
                <div class="message-card" data-aos="fade-up" data-aos-duration="<?php echo e(500 * ($loop->index + 1)); ?>">
                    <div class="card-content">
                        <div class="icon-wrapper">
                            <img src="<?php echo e(asset($message['icon'])); ?>"
                                 class="message-icon"
                                 alt="<?php echo e($message['title']); ?>">
                        </div>
                        <h4 class="message-title"><?php echo e($message['title']); ?></h4>
                        <p class="message-description"><?php echo e($message['description']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php $__env->startPush('styles'); ?>
<style>
.messages-section {
    position: relative;
    width: 100%;
    padding: 80px 0;
}

.message-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    height: 100%;
    transition: transform 0.3s ease;
    margin: 15px 0;
}

.message-card:hover {
    transform: translateY(-5px);
}

.card-content {
    padding: 30px;
    text-align: center;
}

.icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.message-icon {
    width: 100%;
    height: auto;
}

.message-title {
    color: #0B626A;
    font-weight: bold;
    margin-bottom: 15px;
    font-size: 1.5rem;
}

.message-description {
    color: #666;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 0;
}

/* Animation classes */
[data-aos] {
    opacity: 0;
    transition-property: opacity, transform;
}

[data-aos].aos-animate {
    opacity: 1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .message-card {
        margin: 10px 0;
    }

    .card-content {
        padding: 20px;
    }
}
</style>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views/livewire/about-section-messages.blade.php ENDPATH**/ ?>