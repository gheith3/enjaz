<?php
    use App\Settings\CompanySettings;
    $settings = app(CompanySettings::class);
?>

<nav class="navbar navbar-expand-sm navbar-dark bg-transparent">
    <div class="container">
        <a class="navbar-brand" href="<?php echo e(route('home')); ?>">
            <img src="<?php echo e(Storage::url($settings->logo)); ?>" alt="Logo" class="d-inline-block align-top">
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvas"
            aria-controls="navbarOffcanvas" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Regular menu for sm and up -->
        <div class="collapse navbar-collapse d-none d-sm-block" id="mynavbar">
            <ul class="navbar-nav me-auto gap-4">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('home')); ?>">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('about')); ?>">عنا</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('achievements')); ?>">الإنجازات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('organizations')); ?>">المؤسسات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('achievers')); ?>">المنجزين</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('media')); ?>">الإعلام</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('contact-us')); ?>">تواصل معنا</a>
                </li>
            </ul>
        </div>

        <!-- Offcanvas menu for xs -->
        <div class="offcanvas offcanvas-start bg-primary d-block d-sm-none" tabindex="-1" id="navbarOffcanvas"
            aria-labelledby="navbarOffcanvasLabel">
            <div class="offcanvas-header ">
                <br>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-white py-3 border-bottom border-light"
                            href="<?php echo e(route('home')); ?>">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white py-3 border-bottom border-light"
                            href="<?php echo e(route('about')); ?>">عنا</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white py-3 border-bottom border-light"
                            href="<?php echo e(route('achievements')); ?>">الإنجازات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white py-3 border-bottom border-light"
                            href="<?php echo e(route('organizations')); ?>">المؤسسات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white py-3 border-bottom border-light"
                            href="<?php echo e(route('achievers')); ?>">المنجزين</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white py-3 border-bottom border-light"
                            href="<?php echo e(route('media')); ?>">الإعلام</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white py-3 border-bottom border-light"
                            href="<?php echo e(route('contact-us')); ?>">تواصل معنا</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<?php /**PATH E:\Projects\enjaz\resources\views/components/layouts/nav.blade.php ENDPATH**/ ?>