<!-- Sidebar Open Button -->
<div class="sidebar-open-button">
    <i class="bi bi-layout-sidebar-inset" onclick="sidebarToggle()"></i>
</div>

<nav class="sidebar">

    <!-- Sidebar Toggle Button -->
    <div class="sidebar-close-btn flex jce p-mid">
        <i class="bi bi-x-circle" onclick="sidebarClose()"></i>
    </div>

    <!-- Sidebar menu items -->
    <div class="sidebar-menu">

        <!-- Item 1 -->
        <div class="sidebar-menu-item">
            <a class="link-plain text-white" href="">
                <i class="bi bi-collection"></i>
                Dash
            </a>
        </div>

        <!-- Tag dropdown -->
        <div class="sidebar-menu-item" onclick="subMenuToggle(1)">
            <div class="flex jcb">
                <div>
                    <i class="bi bi-tag"></i>
                    Tag
                </div>
                <i class="bi bi-chevron-down sidebar-menu-item-droparrow droparrow-1 <?php echoClassCurrentRouteSame('show-tag-create', 'rotate'); ?>" id="droparrow-1"></i>
            </div>

            <!--Sub Menu of Dropdown Item 1 -->
            <div class="sub-menu <?php echoClassCurrentRouteSame('show-tag-create', 'active') ?>" id="sub-menu-1">
                <!-- <a class="link-plain text-white" href="<?php echo getRouteUsingRouteName('show-course') ?>" class="">
                    <div class="sub-menu-item <?php echoClassCurrentRouteSame('show-course', 'active'); ?>">
                        <i class="bi bi-card-list"></i>
                        Tags
                    </div>
                </a> -->
                <a class="link-plain text-white" href="<?php echo getRouteUsingRouteName('show-tag-create') ?>" class="">
                    <div class="sub-menu-item <?php echoClassCurrentRouteSame('show-tag-create', 'active') ?>">
                        <i class="bi bi-plus-square"></i>
                        Create Tag
                    </div>
                </a>
            </div>
        </div>

        <!-- Course Dropdown -->
        <div class="sidebar-menu-item" onclick="subMenuToggle(2)">
            <div class="flex jcb">
                <div>
                    <i class="bi bi-journals"></i>
                    Course
                </div>
                <i class="bi bi-chevron-down sidebar-menu-item-droparrow droparrow-1 <?php echoClassCurrentRouteSame('show-course', 'rotate');
                                                                                        echoClassCurrentRouteSame('show-course', 'rotate'); ?>" id="droparrow-2"></i>
            </div>

            <!--Sub Menu of Course -->
            <div class="sub-menu <?php echoClassCurrentRouteSame('show-course', 'active');
                                    echoClassCurrentRouteSame('show-course-create', 'active') ?>" id="sub-menu-2">
                <a class="link-plain text-white" href="<?php echo getRouteUsingRouteName('show-course') ?>" class="">
                    <div class="sub-menu-item <?php echoClassCurrentRouteSame('show-course', 'active'); ?>">
                        <i class="bi bi-card-list"></i>
                        Courses
                    </div>
                </a>
                <a class="link-plain text-white" href="<?php echo getRouteUsingRouteName('show-course-create') ?>" class="">
                    <div class="sub-menu-item <?php echoClassCurrentRouteSame('show-course-create', 'active') ?>">
                        <i class="bi bi-plus-square"></i>
                        Create Course
                    </div>
                </a>
            </div>
        </div>

        <!-- Dropdown Item 2 -->
        <div class="sidebar-menu-item" onclick="subMenuToggle(2)">
            <div class="flex jcb">
                <div>
                    <i class="bi bi-collection"></i>
                    Dash 2
                </div>
                <i class="bi bi-chevron-down sidebar-menu-item-droparrow droparrow-2" id="droparrow-2"></i>
            </div>

            <!--Sub Menu of Dropdown Item 1 -->
            <div class="sub-menu" id="sub-menu-2">
                <div class="sub-menu-item">
                    <a class="link-plain text-white" href="" class="">
                        Sub Menu Item 1
                    </a>
                </div>
                <div class="sub-menu-item">
                    <a class="link-plain text-white" href="" class="">
                        Sub Menu Item 2
                    </a>
                </div>
            </div>
        </div>

    </div>
</nav>