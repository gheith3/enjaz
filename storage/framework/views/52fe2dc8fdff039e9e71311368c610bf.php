<div class="filters-section">
    <div class="filters-row primary-filters">
        <button
            wire:click="setFilter('0-0')"
            class="filter-btn <?php echo e('0-0' === $selectedFilter ? 'active' : ''); ?>">
            <span>
            الكل
            </span>
        </button>
        <?php $__currentLoopData = $primaryFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button
                wire:click="setFilter('<?php echo e($this->getFilterId($filter->id)); ?>')"
                class="filter-btn <?php echo e($this->getFilterId($filter->id) === $selectedFilter ? 'active' : ''); ?>">
                <?php if($filter['icon']): ?>
                    <img src="<?php echo e(asset($filter['icon'])); ?>" alt="<?php echo e($filter['name']); ?>" class="filter-icon">
                <?php endif; ?>
                <span><?php echo e($filter->title_ar); ?></span>
            </button>
            <?php $__currentLoopData = $filter->subTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button
                    wire:click="setFilter('<?php echo e($this->getFilterId($filter->id, $subType->id)); ?>')"
                    class="filter-btn <?php echo e($this->getFilterId($filter->id, $subType->id) === $selectedFilter ? 'active' : ''); ?>">
                    <span><?php echo e($subType->title_ar); ?></span>
                </button>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

</div>
<?php $__env->startPush('styles'); ?>

    <style>
        .filters-section {
            padding: 2rem 1rem;
            background-color: white;
        }

        .filters-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            justify-content: center;
        }

        .filter-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            border: 1px solid #666;
            background: white;
            color: #666;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: var(--font-size-5xl);
        }

        .filter-btn.active {
            background: #0B626A;
            color: white;
            border-color: #0B626A;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-icon {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .filter-btn.active .filter-icon {
            filter: brightness(0) invert(1);
        }

        @media (max-width: 768px) {
            .filters-section {
                padding: 1rem 0.5rem;
            }

            .filter-btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }

            .filter-icon {
                width: 20px;
                height: 20px;
            }
        }
    </style>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\achievers-section-filters.blade.php ENDPATH**/ ?>