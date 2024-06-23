<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../src/Views/components/head.php")  ?>
</head>

<body>

    <!-- Nav Bar -->
    <?php require_once("../src/Views/components/nav.php") ?>

    <!-- Sidebar --->
    <?php require_once("../src/Views/components/sidebar.php") ?>

    <!-- Main Body -->
    <main class="main sidebar-main p-mid">

        <!-- Tag Create Card -->
        <div class="card create-card">

            <!-- Header -->
            <h3 class="text-white">
                <div class="flex jcc">
                    <div>
                        <i class="bi bi-tag"></i>
                        Tags in
                        <i class="bi bi-trash-fill"></i>
                        Bin
                    </div>
                </div>
            </h3>

            <!-- Filter Section -->
            <form action="<?= getRouteUsingRouteName('show-bin-tag') ?>" method="get">

                <div class="filter-form-groups">

                    <!-- Title -->
                    <div class="filter-form-group flex aic g-sm">
                        <label class="form-label text-white" for="name">Name</label>
                        <input class="input form-input limit-input-width" id="name" name="name" value="<?php displayFlashedSessionValue('old', 'name') ?>" type="text">
                    </div>

                    <!-- Created By Me -->
                    <div class="flex aic g-sm">
                        <label class="form-label text-white" for="created_by_me">Created By Me</label>
                        <input type="checkbox" class="checkbox" name="created_by_me" id="created_by_me" <?php if (checkFlashedSessionExist('old', 'created_by_me')) echo "checked" ?>>
                    </div>

                    <!-- Oldest -->
                    <div class="flex aic g-sm">
                        <label class="form-label text-white" for="oldest">Oldest</label>
                        <input type="checkbox" class="checkbox" name="oldest" id="oldest" <?php if (checkFlashedSessionExist('old', 'sortByOldest')) echo "checked" ?>>
                    </div>
                </div>

                <div class="flex g-mid filter-action-group">
                    <button type="submit" class="btn"><i class="bi bi-search"></i> Search</button>
                    <a href="<?= getRouteUsingRouteName('show-bin-tag') ?>" type="submit" class="btn link-plain"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                </div>
            </form>

            <!-- Tags -->
            <?php if (count($tags) > 0) : ?>
                <div class="res-table">
                    <table class="table">
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Creator Name</th>
                            <th></th>
                        </tr>

                        <?php foreach ($tags as $index => $tag) : ?>
                            <tr>
                                <td><?= $index + 1 ?>.</td>
                                <td><?= htmlspecialchars($tag->getName()) ?></td>
                                <td><?= htmlspecialchars($tag->getUser()->getName()) ?></td>
                                <td>
                                    <div class="flex jcc aic g-mid">
                                        <!-- Recover --->
                                        <form action="<?= getRouteUsingRouteName('post-bin-tag-recover') ?>" method="POST">
                                            <input type="hidden" value="<?= htmlspecialchars($tag->getId()) ?>" name="recover-id">
                                            <button type="submit" class="btn square">
                                                <i class="bi bi-arrow-counterclockwise"></i> Recover
                                            </button>
                                        </form>

                                        <!-- Delete --->
                                        <form action="<?= getRouteUsingRouteName('post-bin-tag-delete') ?>" method="POST" id="delete-tag-form-<?= htmlspecialchars($tag->getId()) ?>">
                                            <input type="hidden" value="<?= htmlspecialchars($tag->getId()) ?>" name="delete-id">
                                            <button type="button" onclick="confirmDeleteTag(<?= htmlspecialchars($tag->getId()) ?>, '<?= htmlspecialchars($tag->getName()) ?>')" class="btn square">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php else : ?>
                <div class="pagi-not-found">
                    <i class="bi bi-question-diamond-fill"></i> No tags found!
                </div>
            <?php endif; ?>

            <!-- Pagination Navigation -->
            <?php
            $pagiResource = $tags;
            require_once("../src/Views/components/pagi-nav.php")
            ?>
    </main>

    <!-- Notification -->
    <?php require_once("../src/Views/components/notification.php") ?>
</body>

<!-- nav toggle -->
<script src="/assets/js/nav-toggle.js"></script>

<!-- sidebar toggle -->
<script src="/assets/js/sidebar-toggle.js"></script>

<!-- Noti Js -->
<script src="/assets/js/noti-uti.js"></script>

<!-- Sweetalert 2 Js -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Sweetalert Confirm Delete -->
<script>
    // confirm the tag permanent delete
    function confirmDeleteTag(tagId, tagName) {
        Swal.fire({
            title: `Are you sure about deleteing the tag, ${tagName} permanently?`,
            text: "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                let deleteForm = document.querySelector(`#delete-tag-form-${tagId}`);
                deleteForm.submit();
            }
        });
    }
</script>

</html>