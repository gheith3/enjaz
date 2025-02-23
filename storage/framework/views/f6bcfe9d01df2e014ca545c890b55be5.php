<?php if($paginator->hasPages()): ?>
    <nav>
        <ul class="pagination">
            
            <?php if($paginator->onFirstPage()): ?>
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link bg-secondary text-white border-0 rounded-circle mx-1">&lsaquo;</span>
                </li>
            <?php else: ?>
                <li class="page-item">
                    <button type="button" class="page-link bg-primary text-white border-0 rounded-circle mx-1" wire:click="previousPage" wire:loading.attr="disabled" rel="prev">&lsaquo;</button>
                </li>
            <?php endif; ?>

            
            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php if(is_string($element)): ?>
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link bg-secondary text-white border-0 rounded-circle mx-1"><?php echo e($element); ?></span>
                    </li>
                <?php endif; ?>

                
                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $paginator->currentPage()): ?>
                            <li class="page-item active" aria-current="page">
                                <span class="page-link bg-primary text-white border-0 rounded-circle mx-1"><?php echo e($page); ?></span>
                            </li>
                        <?php else: ?>
                            <li class="page-item">
                                <button type="button" class="page-link bg-light text-primary border-0 rounded-circle mx-1" wire:click="gotoPage(<?php echo e($page); ?>)"><?php echo e($page); ?></button>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($paginator->hasMorePages()): ?>
                <li class="page-item">
                    <button type="button" class="page-link bg-primary text-white border-0 rounded-circle mx-1" wire:click="nextPage" wire:loading.attr="disabled" rel="next">&rsaquo;</button>
                </li>
            <?php else: ?>
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link bg-secondary text-white border-0 rounded-circle mx-1">&rsaquo;</span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?> <?php /**PATH E:\Projects\enjaz\resources\views\vendor\pagination\custom.blade.php ENDPATH**/ ?>