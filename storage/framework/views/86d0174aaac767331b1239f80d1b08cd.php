<div>
    <div class="nav-bg">
        <?php echo e(view("components.layouts.nav")); ?>

    </div>
    <div style="min-height: 80vh; margin-top: 80px">
        <div class="container">
            <div class="app-card p-3">
                <h4>
                    <?php echo e($this->album->title); ?>

                </h4>
                <p>
                    <?php echo e($this->album->description); ?>

                </p>
            </div>
        </div>

        <div>
            <div class="container my-5">
                <div class="row justify-content-center gallery">
                    <?php $__currentLoopData = $this->album->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 mb-4">
                            <img src="<?php echo e(asset($item['image'])); ?>"
                                 class="img-fluid" alt="Image 1" data-index="0">
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Modal for image preview -->
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <img src="" alt="Expanded Image" id="expandedImage">
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" id="prevButton">السابق</button>
                            <button class="btn btn-secondary" id="nextButton">التالي</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Custom styles for the gallery */
        .gallery img {
            cursor: pointer;
            transition: transform 0.2s;
            width: 100%;
            height: 180px;
            box-shadow: 0 3px 6px rgba(124, 124, 124, 0.16);
            border-radius: 20px;
        }

        .gallery img:hover {
            transform: scale(1.05);
        }

        .modal-content {
            background-color: transparent;
            border: none;
        }

        .modal-body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-body img {
            max-width: 100%;
            max-height: 80vh;
        }

        .modal-footer {
            justify-content: space-between;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        // JavaScript for gallery functionality
        document.addEventListener('DOMContentLoaded', function () {
            const galleryImages = document.querySelectorAll('.gallery img');
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            const expandedImage = document.getElementById('expandedImage');
            const prevButton = document.getElementById('prevButton');
            const nextButton = document.getElementById('nextButton');

            let currentIndex = 0;

            // Open modal with the clicked image
            galleryImages.forEach((img, index) => {
                img.addEventListener('click', () => {
                    currentIndex = index;
                    updateModalImage();
                    modal.show();
                });
            });

            // Update modal image based on currentIndex
            function updateModalImage() {
                expandedImage.src = galleryImages[currentIndex].src;
            }

            // Navigate to the previous image
            prevButton.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
                updateModalImage();
            });

            // Navigate to the next image
            nextButton.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % galleryImages.length;
                updateModalImage();
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\pages\images-album-page.blade.php ENDPATH**/ ?>