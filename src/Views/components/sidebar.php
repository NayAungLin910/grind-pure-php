<!-- Sidebar Open Button -->
<div class="sidebar-open-button" onclick="sidebarToggle()">
    <i class="bi bi-layout-sidebar-inset"></i>
</div>

<nav class="sidebar">

    <!-- Sidebar Toggle Button -->
    <div class="sidebar-close-btn">
        <i class="bi bi-x-circle" onclick="sidebarClose()"></i>
    </div>

    <!-- Sidebar menu items -->
    <div class="sidebar-menu">

        <!-- Item 1 -->
        <div class="sidebar-menu-item">
            <a href="">
                <i class="bi bi-collection"></i>
                Dash
            </a>
        </div>

        <!-- Dropdown Item 1 -->
        <div class="sidebar-menu-item" onclick="subMenuToggle(1)">
            <div href="">
                <i class="bi bi-collection"></i>
                Dash 1
                <i class="bi bi-chevron-down sidebar-menu-item-droparrow droparrow-1" id="droparrow-1"></i>
            </div>

            <!--Sub Menu of Dropdown Item 1 -->
            <div class="sub-menu" id="sub-menu-1">
                <a href="" class="sub-menu-item">
                    Sub Menu Item 1
                </a>
                <a href="" class="sub-menu-item">
                    Sub Menu Item 2
                </a>
            </div>
        </div>

        <!-- Dropdown Item 2 -->
        <div class="sidebar-menu-item" onclick="subMenuToggle(2)">
            <div href="">
                <i class="bi bi-collection"></i>
                Dash 2
                <i class="bi bi-chevron-down sidebar-menu-item-droparrow droparrow-2" id="droparrow-2"></i>
            </div>

            <!--Sub Menu of Dropdown Item 1 -->
            <div class="sub-menu" id="sub-menu-2">
                <a href="" class="sub-menu-item">
                    Sub Menu Item 2
                </a>
                <a href="" class="sub-menu-item">
                    Sub Menu Item 2
                </a>
            </div>
        </div>

    </div>
</nav>