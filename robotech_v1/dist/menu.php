<?php
include 'conexion.php';

?>



<button id="toggleSidebar" class="floating-button">
    ☰
</button>
<div id="sidebar" class="sidebar-hidden">
    <div class="min-h-full z-[99]  fixed inset-y-0 print:hidden bg-gradient-to-t from-[#6f3dc3] from-10% via-[#603dc3] via-40% to-[#5c3dc3] to-100% dark:bg-[#603dc3] main-sidebar duration-300 group-data-[sidebar=dark]:bg-[#603dc3] group-data-[sidebar=brand]:bg-brand group-[.dark]:group-data-[sidebar=brand]:bg-[#603dc3]">
        <div class=" text-center border-b bg-[#603dc3] border-r h-[64px] flex justify-center items-center brand-logo dark:bg-[#603dc3] dark:border-slate-700/40 group-data-[sidebar=dark]:bg-[#603dc3] group-data-[sidebar=dark]:border-slate-700/40 group-data-[sidebar=brand]:bg-brand group-[.dark]:group-data-[sidebar=brand]:bg-[#603dc3] group-data-[sidebar=brand]:border-slate-700/40">
            <a href="https://weblowcostbcn.com/erp-crm/robotech_v1/dist/inicio.php" class="logo">
                <span>
                    <img src="assets/images/logo.jpeg" alt="logo-small" class="logo-sm h-16 w-40 align-middle inline-block">
                </span>
                <!-- <span>
                    <img src="assets/images/logo.jpeg" alt="logo-large" class="logo-lg h-[28px] logo-light hidden dark:inline-block ms-1 group-data-[sidebar=dark]:inline-block group-data-[sidebar=brand]:inline-block">
                    <img src="assets/images/logo.jpeg" alt="logo-large"
                        class="logo-lg h-[28px] logo-dark inline-block dark:hidden ms-1 group-data-[sidebar=dark]:hidden group-data-[sidebar=brand]:hidden">
                </span> -->
            </a>
        </div>
        <div class="border-r pb-14 h-[100vh] overflow-hidden dark:bg-[#603dc3] dark:border-slate-700/40 group-data-[sidebar=dark]:border-slate-700/40 group-data-[sidebar=brand]:border-slate-700/40">
            <div class="p-4 block">
                <ul class="navbar-nav">
                    <li class="uppercase text-[11px]  text-primary-500 dark:text-primary-400 mt-0 leading-4 mb-2 group-data-[sidebar=dark]:text-primary-400 group-data-[sidebar=brand]:text-primary-300">
                        <span class="text-[9px] text-slate-600 dark:text-slate-500 group-data-[sidebar=dark]:text-slate-500 group-data-[sidebar=brand]:text-slate-400">DashboardS & Apps</span>
                    </li>
                    <li>
                        <div id="parent-accordion" data-fc-type="accordion">
                            <a href="#"
                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200 "
                                data-fc-type="collapse" data-fc-parent="parent-accordion">
                                <span data-lucide="home"
                                    class="w-5 h-5 text-center text-slate-800 dark:text-slate-400 me-2 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                <span>Admin</span>
                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180 "></i>
                            </a>

                            <div id="Admin-flush" class="overflow-hidden">
                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                    <li class="nav-item relative block">
                                        <a href="inicio.php"
                                            class="nav-link hover:text-primary-500 rounded-md dark:hover:text-primary-500 relative flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Inicio
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="clientes.php"
                                            class="nav-link hover:text-primary-500 rounded-md dark:hover:text-primary-500 relative flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Clientes
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="facturas.php"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Facturas
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="proyectos.php"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Proyectos
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="productos.php"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Productos
                                        </a>
                                    </li>
                                    <!-- <li class="nav-item relative block">
                                        <a href="detalles-cliente.php"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Detalles de clientes
                                        </a>
                                    </li> -->
                                    <!-- <li class="nav-item relative block">
                                        <a href="admin-orders.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Orders
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="admin-order-details.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Order Details
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="admin-refund.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Refund
                                        </a>
                                    </li> -->
                                </ul>
                            </div>
                            <!-- <a href="#"
                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200 "
                                data-fc-type="collapse" data-fc-parent="parent-accordion">
                                <span data-lucide="home"
                                    class="w-5 h-5 text-center text-slate-800 dark:text-slate-400 me-2 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                <span>Customer</span>
                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180 "></i>
                            </a> -->
                            <!-- <div id="Customer-flush" class="hidden  overflow-hidden">
                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                    <li class="nav-item relative block">
                                        <a href="customers-home.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400 "></i>
                                            Home
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="customers-pro-details.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Product details
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="customers-products.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Product filter
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="customers-cart.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Cart
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="customers-checkout.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Checkout
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="vista-perfil.php"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Profile
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="customers-stores.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Favourite store
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="customers-wishlist.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Wishlist
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="customers-order-track.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Order track
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="customers-invoice.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative   flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Invoice
                                        </a>
                                    </li>
                                </ul>
                            </div> -->
                            <!-- <div data-fc-type="collapse" data-fc-parent="parent-accordion">
                                <a href="#"
                                    class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200">
                                    <span data-lucide="grid"
                                        class="w-5 h-5 text-center text-slate-800 me-2 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                    <span>Apps</span>
                                    <i class="icofont-thin-down fc-collapse-open:rotate-180 ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></i>
                                </a>
                            </div> -->
                            <!-- <div class="hidden  overflow-hidden">
                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2" id="apps-accordion"
                                    data-fc-type="accordion">
                                    <li class="nav-item relative block">
                                        <a href="apps-chat.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200  flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Chat
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="apps-contact-list.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Contact List
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="apps-calendar.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Calendar
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="apps-files.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            File Mamager
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="apps-invoice.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Invoice
                                        </a>
                                    </li>
                                    <li>
                                        <div id="Email" data-fc-type="collapse" data-fc-parent="apps-accordion">
                                            <a href="#"
                                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200 "
                                                aria-expanded="false" aria-controls="Email-flush">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                <span>Email</span>
                                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400  fc-collapse-open:rotate-180"></i>
                                            </a>
                                        </div>
                                        <div id="Email-flush" class=" hidden  overflow-hidden "
                                            aria-labelledby="Email">
                                            <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                <li class="nav-item relative block">
                                                    <a href="apps-email-inbox.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Inbox
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="apps-email-read.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Read Email
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                </ul>
                            </div> -->


                            <!-- <div class="border-b border-dashed dark:border-slate-700/40 my-3 group-data-[sidebar=dark]:border-slate-700/40 group-data-[sidebar=brand]:border-slate-700/40"></div>
                            <div class="text-[9px] text-slate-600 dark:text-slate-500 group-data-[sidebar=dark]:text-slate-500 group-data-[sidebar=brand]:text-slate-400">
                                C<span>omponents & Extra</span>
                            </div>
                            <div data-fc-type="collapse" data-fc-parent="parent-accordion">
                                <a href="#"
                                    class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200">
                                    <span data-lucide="box"
                                        class="w-5 h-5 text-center text-slate-800 me-2 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                    <span>UI Kit</span>
                                    <i class="icofont-thin-down fc-collapse-open:rotate-180 ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></i>
                                </a>
                            </div>
                            <div class="hidden  overflow-hidden">
                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2" id="UI_Kit-accordion"
                                    data-fc-type="accordion">
                                    <li>
                                        <div id="UI_Elements" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                            <a href="#"
                                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                aria-expanded="false" aria-controls="UI_Elements-flush">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                <span>UI Elements</span>
                                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                            </a>
                                        </div>
                                        <div id="UI_Elements-flush" class=" hidden  overflow-hidden "
                                            aria-labelledby="UI_Elements">
                                            <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                <li class="nav-item relative block">
                                                    <a href="ui-alerts.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Alerts
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-avatars.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Avatars
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-buttons.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Buttons
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-badges.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Budges
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-cards.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Cards
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-carousels.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Carousels
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-dropdowns.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Dropdowns
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-grids.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Grids
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-images.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Images
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-lists.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Lists
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-modals.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Modals
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-navs.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Navs
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-navbars.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Navbars
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-paginations.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Paginations
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-popover-tooltips.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Popover & Tooltips
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-progress.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Progress
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-spinners.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Spinners
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-tabs-accordions.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Tabs & Accordions
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-typography.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Typography
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="ui-videos.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Videos
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li>
                                        <div id="Advanced_UI" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                            <a href="#"
                                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200 "
                                                aria-expanded="false" aria-controls="Advanced_UI-flush">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                <span>Advanced UI</span>
                                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400  fc-collapse-open:rotate-180"></i>
                                            </a>
                                        </div>
                                        <div id="Advanced_UI-flush" class=" hidden  overflow-hidden "
                                            aria-labelledby="Advanced_UI">
                                            <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                <li class="nav-item relative block">
                                                    <a href="advanced-animation.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Animation
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="advanced-clipboard.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Clipboard
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="advanced-dragula.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Dragula
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="advanced-highlight.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Highlight
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="advanced-rangeslider.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Range Slider
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="advanced-ratings.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Ratings
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="advanced-ribbons.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Ribbons
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="advanced-sweetalerts.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Sweet Alert
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li>
                                        <div id="Forms" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                            <a href="#"
                                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                aria-expanded="false" aria-controls="Forms-flush">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                <span>Forms</span>
                                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                            </a>
                                        </div>
                                        <div id="Forms-flush" class=" hidden  overflow-hidden "
                                            aria-labelledby="Forms">
                                            <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                <li class="nav-item relative block">
                                                    <a href="forms-elements.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Basic Elements
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="forms-advance.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Advanced Elements
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="forms-validation.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Validation
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="forms-wizard.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Wizard
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="forms-editors.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Editors
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="forms-uploads.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Uploads
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="forms-img-crop.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Image Crop
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li>
                                        <div id="Charts" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                            <a href="#"
                                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                aria-expanded="false" aria-controls="Charts-flush">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                <span>Charts</span>
                                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                            </a>
                                        </div>
                                        <div id="Charts-flush" class=" hidden  overflow-hidden "
                                            aria-labelledby="Charts">
                                            <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                <li class="nav-item relative block">
                                                    <a href="charts-apex.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Apex
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="charts-echarts.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Echarts
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="charts-justgage.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        JustGage
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="charts-chartjs.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Chartjs
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="charts-toast-ui.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        ToastUI
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <div id="Tables" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                            <a href="#"
                                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                aria-expanded="false" aria-controls="Tables-flush">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                <span>Tables</span>
                                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                            </a>
                                        </div>
                                        <div id="Tables-flush" class=" hidden  overflow-hidden "
                                            aria-labelledby="Tables">
                                            <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                <li class="nav-item relative block">
                                                    <a href="tables-basic.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Basic
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="tables-datatable.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Datatable
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <div id="Icons" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                            <a href="#"
                                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                aria-expanded="false" aria-controls="Icons-flush">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                <span>Icons</span>
                                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                            </a>
                                        </div>
                                        <div id="Icons-flush" class=" hidden  overflow-hidden "
                                            aria-labelledby="Icons">
                                            <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                <li class="nav-item relative block">
                                                    <a href="icons-lucide.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Lucide
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="icons-fontawesome.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Fontawesome
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="icons-icofont.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Icofont
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <div id="Maps" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                            <a href="#"
                                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                aria-expanded="false" aria-controls="Maps-flush">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                <span>Maps</span>
                                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                            </a>
                                        </div>
                                        <div id="Maps-flush" class=" hidden  overflow-hidden "
                                            aria-labelledby="Maps">
                                            <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                <li class="nav-item relative block">
                                                    <a href="maps-google.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Google Maps
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="maps-leaflet.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Leaflet Maps
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="maps-vector.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Vector Maps
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <div id="Email-Temp" data-fc-type="collapse" data-fc-parent="UI_Kit-accordion">
                                            <a href="#"
                                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                                aria-expanded="false" aria-controls="Email-Temp-flush">
                                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                <span>Email Templates</span>
                                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400 fc-collapse-open:rotate-180"></i>
                                            </a>
                                        </div>
                                        <div id="Email-Temp-flush" class=" hidden  overflow-hidden "
                                            aria-labelledby="Email-Temp">
                                            <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                                <li class="nav-item relative block">
                                                    <a href="email-templates-alert.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Alert Email
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="email-templates-basic.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Basic Email
                                                    </a>
                                                </li>
                                                <li class="nav-item relative block">
                                                    <a href="email-templates-billing.html"
                                                        class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                                        <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                                        Billing Email
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <a href="#"
                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                data-fc-type="collapse" data-fc-parent="parent-accordion">
                                <span data-lucide="file-plus"
                                    class="w-5 h-5 text-center text-slate-800 dark:text-slate-400 me-2 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                <span>Pages</span>
                                <i class="icofont-thin-down ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400  fc-collapse-open:rotate-180 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></i>
                            </a>

                            <div id="Pages-flush" class="hidden  overflow-hidden">
                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                    <li class="nav-item relative block">
                                        <a href="pages-blogs.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Blogs
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="pages-pricing.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Pricing
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="vista-perfil.php"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Profile
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="pages-starter.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Starter
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="pages-timeline.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Timeline
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <a href="#"
                                class="nav-link hover:bg-transparent hover:text-black  rounded-md dark:hover:text-slate-200   flex items-center  decoration-0 px-3 py-3 cursor-pointer group-data-[sidebar=dark]:hover:text-slate-200 group-data-[sidebar=brand]:hover:text-slate-200"
                                data-fc-type="collapse" data-fc-parent="parent-accordion">
                                <span data-lucide="lock"
                                    class="w-5 h-5 text-center text-slate-800 me-2 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></span>
                                <span>Authentication</span>
                                <i class="icofont-thin-down  fc-collapse-open:rotate-180 ms-auto inline-block text-[14px] transform transition-transform duration-300 text-slate-800 dark:text-slate-400 group-data-[sidebar=dark]:text-slate-400 group-data-[sidebar=brand]:text-slate-400"></i>
                            </a>
                            <div id="Authentication-flush" class="hidden  overflow-hidden"
                                aria-labelledby="Authentication">
                                <ul class="nav flex-col flex flex-wrap ps-0 mb-0 ms-2">
                                    <li class="nav-item relative block">
                                        <a href="auth-login.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Log In
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="auth-register.php"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Register
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="auth-recover-pw.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Recover Password
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="auth-lock-screen.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            Lock Screen
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="auth-404.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            404
                                        </a>
                                    </li>
                                    <li class="nav-item relative block">
                                        <a href="auth-500.html"
                                            class="nav-link  hover:text-primary-500  rounded-md dark:hover:text-primary-500 relative group-data-[sidebar=brand]:hover:text-slate-200   flex items-center decoration-0 px-3 py-3">
                                            <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                            500
                                        </a>
                                    </li>
                                </ul>
                            </div> -->
                        </div>
                    </li>
                </ul>
                <!-- <div class="rounded-md py-4 px-3 mt-12  mb-4 relative bg-primary-300/10 text-center">
                    <a href="javascript: void(0);" class="float-right close-btn text-slate-400">
                        <i class="mdi mdi-close"></i>
                    </a>
                    <h5 class="my-3 text-lg font-medium text-slate-700 dark:text-slate-300 group-data-[sidebar=dark]:text-slate-300 group-data-[sidebar=brand]:text-slate-300">Mannat Themes</h5>
                    <p class="mb-3 text-sm text-slate-400">We Design and Develop Clean and High Quality Web Applications</p>
                    <button class="px-2 py-1 mb-2 text-orange-400 hover:text-white border border-orange-300 hover:bg-orange-300 focus:outline-none  rounded text-sm  text-center dark:border-orange-300 dark:text-orange-300 dark:hover:text-white dark:hover:bg-orange-300 ">Upgrade your plan</button>

                </div> -->
            </div>
        </div>
    </div>

</div>

<nav id="topbar" class="topbar border-b  dark:border-slate-700/40  fixed inset-x-0  duration-300
             block print:hidden z-50">
    <div class="mx-0 flex max-w-full flex-wrap items-center lg:mx-auto relative top-[50%] start-[50%] transform -translate-x-1/2 -translate-y-1/2">
        <div class="ltr:mx-2  rtl:mx-2">
            <button id="toggle-menu-hide" class="button-menu-mobile flex rounded-full md:me-0 relative">
                <i class="ti ti-chevrons-left text-3xl  top-icon"></i>
                <i data-lucide="menu" class="top-icon w-5 h-5"></i>
            </button>
        </div>
        <div class="flex items-center md:w-[40%] lg:w-[30%] xl:w-[20%]">
            <div class="relative ltr:mx-2 rtl:mx-2 self-center">
                <!-- <button class="px-2 py-1 bg-primary-500/10 border border-transparent collapse:bg-green-100 text-primary text-sm rounded hover:bg-blue-600 hover:text-white"><i class="ti ti-plus me-1"></i> New Task</button> -->
            </div>
        </div>

        <div class="order-1 ltr:ms-auto rtl:ms-0 rtl:me-auto flex items-center md:order-2">
            <div class="ltr:me-2 ltr:md:me-4 rtl:me-0 rtl:ms-2 rtl:lg:me-0 rtl:md:ms-4 dropdown relative">
                <button
                    type="button"
                    class="dropdown-toggle flex rounded-full md:me-0"
                    aria-expanded="false"
                    data-fc-autoclose="both" data-fc-type="dropdown">
                    <span data-lucide="search" class="top-icon w-5 h-5"></span>
                </button>

                <div
                    class="left-auto right-0 z-50 my-1 hidden min-w-[300px]
                    list-none divide-y  divide-gray-100 rounded-md border-slate-700
                    md:border-white text-base shadow dark:divide-gray-600 bg-white
                    dark:bg-slate-800" onclick="event.stopPropagation()">
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center
                        pl-3">
                            <i class="ti ti-search text-gray-400 z-10"></i>
                        </div>
                        <input
                            type="text"
                            id="email-adress-icon"
                            class="block w-full rounded-lg border border-slate-200 dark:border-slate-700/60 bg-slate-200/10 p-1.5
                        pl-10 text-slate-600 dark:text-slate-400 outline-none focus:border-slate-300
                        focus:ring-slate-300 dark:bg-slate-800/20 sm:text-sm"
                            placeholder="Search..." />
                    </div>
                </div>
            </div>
            <div class="ltr:me-2 ltr:md:me-4 rtl:me-0 rtl:ms-2 rtl:lg:me-0 rtl:md:ms-4">

                <button id="toggle-theme" class="flex rounded-full md:me-0 relative">
                    <span data-lucide="moon" class="top-icon w-5 h-5 light "></span>
                    <span data-lucide="sun" class="top-icon w-5 h-5 dark hidden"></span>
                </button>
            </div>
            <div class="ltr:me-2 ltr:lg:me-4 rtl:me-0 rtl:ms-2 rtl:lg:me-0 rtl:md:ms-4 dropdown relative">
                <button
                    type="button"
                    class="dropdown-toggle flex rounded-full md:me-0"
                    id="Notifications"
                    aria-expanded="false"
                    data-fc-autoclose="both" data-fc-type="dropdown">
                    <span data-lucide="bell" class="top-icon w-5 h-5"></span>
                </button>

                <div
                    class="left-auto right-0 z-50 my-1 hidden w-64
                    list-none divide-y h-52 divide-gray-100 rounded border border-slate-700/10
                   text-base shadow dark:divide-gray-600 bg-white
                    dark:bg-slate-800"
                    id="navNotifications" data-simplebar>
                    <ul class="py-1" aria-labelledby="navNotifications">
                        <li class="py-2 px-4">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <div class="flex">
                                    <div class="h-8 w-8 rounded-full bg-primary-500/20 inline-flex me-3">
                                        <span data-lucide="shopping-cart" class="w-4 h-4 inline-block text-primary-500 dark:text-primary-400 self-center mx-auto"></span>
                                    </div>
                                    <div class="flex-grow flex-1 ms-0.5 overflow-hidden">
                                        <p class="text-sm font-medium text-gray-900 truncate
                                dark:text-gray-300">Karen Robinson</p>
                                        <p class="text-gray-500 mb-0 text-xs truncate
                                dark:text-gray-400">
                                            Hey ! i'm available here
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="py-2 px-4">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <div class="flex">
                                    <img class="object-cover rounded-full h-8 w-8 shrink-0 me-3"
                                        src="assets/images/users/avatar-3.png" alt="logo" />
                                    <div class="flex-grow flex-1 ms-0.5 overflow-hidden">
                                        <p class="text-sm font-medium text-gray-900 truncate
                                dark:text-gray-300">Your order is placed</p>
                                        <p class="text-gray-500 mb-0 text-xs truncate
                                dark:text-gray-400">
                                            Dummy text of the printing and industry.
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="py-2 px-4">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <div class="flex">
                                    <div class="h-8 w-8 rounded-full bg-primary-500/20 inline-flex me-3">
                                        <span data-lucide="user" class="w-4 h-4 inline-block text-primary-500 dark:text-primary-400 self-center mx-auto"></span>
                                    </div>
                                    <div class="flex-grow flex-1 ms-0.5 overflow-hidden">
                                        <p class="text-sm font-medium text-gray-900 truncate
                                dark:text-gray-300">Robert McCray</p>
                                        <p class="text-gray-500 mb-0 text-xs truncate
                                dark:text-gray-400">
                                            Good Morning!
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="py-2 px-4">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <div class="flex">
                                    <img class="object-cover rounded-full h-8 w-8 shrink-0 me-3"
                                        src="assets/images/users/avatar-9.png" alt="logo" />
                                    <div class="flex-grow flex-1 ms-0.5 overflow-hidden">
                                        <p class="text-sm font-medium  text-gray-900 truncate
                                dark:text-gray-300">Meeting with designers</p>
                                        <p class="text-gray-500 mb-0 text-xs truncate
                                dark:text-gray-400">
                                            It is a long established fact that a reader.
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="me-2  dropdown relative">
                <button
                    type="button"
                    class="dropdown-toggle flex items-center rounded-full text-sm
                    focus:bg-none focus:ring-0 dark:focus:ring-0 md:me-0"
                    id="user-profile"
                    aria-expanded="false"
                    data-fc-autoclose="both" data-fc-type="dropdown">
                    <img
                        class="h-8 w-8 rounded-full"
                        src="assets/images/users/avatar-1.png"
                        alt="user photo" />
                    <span class="ltr:ms-2 rtl:ms-0 rtl:me-2 hidden text-left xl:block">
                        <span class="block font-medium text-slate-600 dark:text-gray-300"><?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></span>
                        <span class="-mt-0.5 block text-xs text-slate-500 dark:text-gray-400">Admin</span>
                    </span>
                </button>

                <div
                    class="left-auto right-0 z-50 my-1 hidden list-none
                    divide-y divide-gray-100 rounded border border-slate-700/10
                    text-base shadow dark:divide-gray-600 bg-white dark:bg-slate-800 w-40"
                    id="navUserdata">

                    <ul class="py-1" aria-labelledby="navUserdata">
                        <li>
                            <a
                                href="vista-perfil.php"
                                class="flex items-center py-2 px-3 text-sm text-gray-700 hover:bg-gray-50
                          dark:text-gray-200 dark:hover:bg-gray-900/20
                          dark:hover:text-white">
                                <span data-lucide="user"
                                    class="w-4 h-4 inline-block text-slate-800 dark:text-slate-400 me-2"></span>
                                PERFIL</a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="flex items-center py-2 px-3 text-sm text-gray-700 hover:bg-gray-50
                          dark:text-gray-200 dark:hover:bg-gray-900/20
                          dark:hover:text-white">
                                <span data-lucide="settings"
                                    class="w-4 h-4 inline-block text-slate-800 dark:text-slate-400 me-2"></span>
                                AJUSTES</a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="flex items-center py-2 px-3 text-sm text-gray-700 hover:bg-gray-50
                          dark:text-gray-200 dark:hover:bg-gray-900/20
                          dark:hover:text-white">
                                <span data-lucide="dollar-sign"
                                    class="w-4 h-4 inline-block text-slate-800 dark:text-slate-400 me-2"></span>
                                Earnings</a>
                        </li>
                        <li>
                            <a href="logout.php" class="nav-link hover:text-primary-500 rounded-md dark:hover:text-primary-500 relative flex items-center decoration-0 px-3 py-3 group-data-[sidebar=brand]:hover:text-slate-200">
                                <i class="icofont-dotted-right me-2 text-slate-600 text-[8px] group-data-[sidebar=brand]:text-slate-400"></i>
                                Sign out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>


<div class="ltr:flex flex-1 rtl:flex-row-reverse">
    <div class="page-wrapper relative ltr:ml-auto rtl:mr-auto rtl:ml-0 w-[calc(100%-260px)] px-4 pt-[64px] duration-300">




        <style>
            .floating-button {
                position: absolute;
                top: 10px;
                left: 10px;
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                font-size: 24px;
                cursor: pointer;
                z-index: 10000;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: background-color 0.3s ease;
            }

            .floating-button:hover {
                background-color: #0056b3;
            }

            /* Sidebar */
            #sidebar {
                position: fixed;
                left: 0;
                top: 0;
                height: 100%;
                /* Full viewport height */
                width: 260px;
                background-color: #fff;
                overflow-y: hidden;
                /* Prevent scrolling */
                transition: transform 0.3s ease;
                z-index: 9999;
                box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            }

            .sidebar-hidden {
                transform: translateX(-100%);
            }

            .sidebar-visible {
                transform: translateX(0);
            }

            /* Mostrar siempre en pantallas grandes */
            @media (min-width: 768px) {
                .floating-button {
                    display: none;
                }

                #sidebar {
                    transform: translateX(0);
                }
            }
        </style>

        <!-- JS para abrir/cerrar -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleBtn = document.getElementById('toggleSidebar');
                const sidebar = document.getElementById('sidebar');

                function toggleSidebar() {
                    sidebar.classList.toggle('sidebar-hidden');
                    sidebar.classList.toggle('sidebar-visible');
                }

                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSidebar();
                });

                document.addEventListener('click', function(e) {
                    const isClickInside = sidebar.contains(e.target) || toggleBtn.contains(e.target);
                    const isMobile = window.innerWidth < 768;

                    if (!isClickInside && isMobile && sidebar.classList.contains('sidebar-visible')) {
                        toggleSidebar();
                    }
                });

                // Abrir la pestaña Admin por defecto
                const adminLink = document.querySelector('[data-fc-parent="parent-accordion"]');
                if (adminLink) {
                    adminLink.classList.add('fc-collapse-open');
                    const icon = adminLink.querySelector('i.icofont-thin-down');
                    if (icon) {
                        icon.style.transform = 'rotate(180deg)';
                    }
                }
            });
        </script>