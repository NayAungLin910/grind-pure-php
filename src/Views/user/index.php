<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../src/Views/components/head.php")  ?>

    <!-- Swiper JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

</head>

<body>
    <!-- Nav Bar -->
    <?php require_once("../src/Views/components/nav.php") ?>

    <!-- Main Body -->
    <div class="main">
        <div>

            <?php if (count($phpCourses) > 0) : ?>
                <div class="course-section">
                    <h3 class="mtb-sm text-white">PHP Courses</h3>
                    <!-- PHP phpCourses -->
                    <div class="swiper php-phpCourse-swiper">
                        <div class="swiper-wrapper">

                            <?php foreach ($phpCourses as $phpCourse) : ?>
                                <div class="swiper-slide swiper-course text-white">
                                    <img class="swiper-course-image" src="<?= $phpCourse->getImage() ?>" alt="">
                                    <div class="card-title">
                                        <?= htmlspecialchars($phpCourse->getTitle()) ?>
                                    </div>
                                    <div class="card-description">
                                        <?= htmlspecialchars($phpCourse->getDescription()) ?>
                                    </div>

                                    <div class="card-description bg-pri-heavy-2 half-bottom-round-mid point-normal" style="width: 100%;">
                                        <?php foreach ($phpCourse->getUndeletedTags() as $tag) : ?>
                                            <a href="<?= getRouteUsingRouteName('show-public-course') . '?title=' . $phpCourse->getTitle() . "&tags%5B%5D=" . $tag->getId() ?>" class="btn link-plain text-white btn-tag-small"><?= htmlspecialchars($tag->getName()) ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>

                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (count($javaCourses) > 0) : ?>
                <div class="course-section mtb-sm">
                    <h3 class="mtb-sm text-white">Java Courses</h3>
                    <!-- Java Courses -->
                    <div class="swiper php-phpCourse-swiper">
                        <div class="swiper-wrapper">

                            <?php foreach ($javaCourses as $javaCourse) : ?>
                                <div class="swiper-slide swiper-course text-white">
                                    <img class="swiper-course-image" src="<?= $javaCourse->getImage() ?>" alt="">
                                    <div class="card-title">
                                        <?= htmlspecialchars($javaCourse->getTitle()) ?>
                                    </div>
                                    <div class="card-description">
                                        <?= htmlspecialchars($javaCourse->getDescription()) ?>
                                    </div>

                                    <div class="card-description bg-pri-heavy-2 half-bottom-round-mid point-normal" style="width: 100%;">
                                        <?php foreach ($javaCourse->getUndeletedTags() as $tag) : ?>
                                            <a href="<?= getRouteUsingRouteName('show-public-course') . '?title=' . $javaCourse->getTitle() . "&tags%5B%5D=" . $tag->getId() ?>" class="btn link-plain text-white btn-tag-small"><?= htmlspecialchars($tag->getName()) ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>

                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
<script src="./assets/js/nav-toggle.js"></script>
<script>
    let swiper = new Swiper('.php-phpCourse-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 4,
            }
        }
    })
</script>

</html>