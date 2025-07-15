<nav id="topbar" class="topbar border-b dark:border-slate-700/40 fixed inset-x-0 duration-300 block print:hidden z-50">
            <div class="mx-0 flex max-w-full flex-wrap items-center lg:mx-auto relative top-[50%] start-[50%] transform -translate-x-1/2 -translate-y-1/2">
              <div class="ltr:mx-2  rtl:mx-2">
                <button id="toggle-menu-hide" class="button-menu-mobile flex rounded-full md:me-0 relative">
                  <!-- <i class="ti ti-chevrons-left text-3xl  top-icon"></i> -->
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="menu" class="lucide lucide-menu top-icon w-5 h-5"><line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="18" y2="18"></line></svg>
                </button>
              </div>
              <div class="flex items-center md:w-[40%] lg:w-[30%] xl:w-[20%]">
                <div class="relative ltr:mx-2 rtl:mx-2 self-center">
                  <button class="px-2 py-1 bg-primary-500/10 border border-transparent collapse:bg-green-100 text-primary text-sm rounded hover:bg-blue-600 hover:text-white"><i class="ti ti-plus me-1"></i> New Task</button>
                </div>
              </div>
      
              <div class="order-1 ltr:ms-auto rtl:ms-0 rtl:me-auto flex items-center md:order-2">
                <div class="ltr:me-2 ltr:md:me-4 rtl:me-0 rtl:ms-2 rtl:lg:me-0 rtl:md:ms-4 dropdown relative">
                  <button type="button" class="dropdown-toggle flex rounded-full md:me-0 fc-dropdown" aria-expanded="false" data-fc-autoclose="both" data-fc-type="dropdown">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="search" class="lucide lucide-search top-icon w-5 h-5"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                  </button>

                  <div class="left-auto right-0 z-50 my-1 hidden min-w-[300px] list-none divide-y divide-gray-100 rounded-md border-slate-700 md:border-white text-base shadow dark:divide-gray-600 bg-white dark:bg-slate-800 fc-dropdown" onclick="event.stopPropagation()">
                    <div class="relative">
                      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center
                        pl-3">
                        <i class="ti ti-search text-gray-400 z-10"></i>
                      </div>
                      <input type="text" id="email-adress-icon" class="block w-full rounded-lg border border-slate-200 dark:border-slate-700/60 bg-slate-200/10 p-1.5
                        pl-10 text-slate-600 dark:text-slate-400 outline-none focus:border-slate-300
                        focus:ring-slate-300 dark:bg-slate-800/20 sm:text-sm" placeholder="Search...">
                    </div>
                  </div>
                </div>
                <div class="ltr:me-2 ltr:md:me-4 rtl:me-0 rtl:ms-2 rtl:lg:me-0 rtl:md:ms-4">

                  <button id="toggle-theme" class="flex rounded-full md:me-0 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="moon" class="lucide lucide-moon top-icon w-5 h-5 light"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="sun" class="lucide lucide-sun top-icon w-5 h-5 dark hidden"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>
                  </button>
                </div>
                <div class="ltr:me-2 ltr:lg:me-4 rtl:me-0 rtl:ms-2 rtl:lg:me-0 rtl:md:ms-4 dropdown relative">
                  <button type="button" class="dropdown-toggle flex rounded-full md:me-0 fc-dropdown" id="Notifications" aria-expanded="false" data-fc-autoclose="both" data-fc-type="dropdown">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="bell" class="lucide lucide-bell top-icon w-5 h-5"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>
                  </button>

                  <div class="left-auto right-0 z-50 my-1 hidden w-64 list-none divide-y h-52 divide-gray-100 rounded border border-slate-700/10 text-base shadow dark:divide-gray-600 bg-white dark:bg-slate-800 fc-dropdown" id="navNotifications" data-simplebar="init"><div class="simplebar-wrapper" style="margin: 0px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden;"><div class="simplebar-content" style="padding: 0px;">
                    <ul class="py-1" aria-labelledby="navNotifications">
                      <li class="py-2 px-4">
                        <a href="javascript:void(0);" class="dropdown-item">
                          <div class="flex">
                              <div class="h-8 w-8 rounded-full bg-primary-500/20 inline-flex me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="shopping-cart" class="lucide lucide-shopping-cart w-4 h-4 inline-block text-primary-500 dark:text-primary-400 self-center mx-auto"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
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
                            <img class="object-cover rounded-full h-8 w-8 shrink-0 me-3" src="assets/images/users/avatar-3.png" alt="logo">
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
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="user" class="lucide lucide-user w-4 h-4 inline-block text-primary-500 dark:text-primary-400 self-center mx-auto"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
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
                            <img class="object-cover rounded-full h-8 w-8 shrink-0 me-3" src="assets/images/users/avatar-9.png" alt="logo">
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
                  </div></div></div></div><div class="simplebar-placeholder" style="width: 0px; height: 0px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: hidden;"><div class="simplebar-scrollbar" style="height: 0px; display: none;"></div></div></div>
                </div>
                <div class="me-2  dropdown relative">
                  <button type="button" class="dropdown-toggle flex items-center rounded-full text-sm focus:bg-none focus:ring-0 dark:focus:ring-0 md:me-0 fc-dropdown" id="user-profile" aria-expanded="false" data-fc-autoclose="both" data-fc-type="dropdown">
                    <img class="h-8 w-8 rounded-full" src="assets/images/users/avatar-1.png" alt="user photo">
                    <span class="ltr:ms-2 rtl:ms-0 rtl:me-2 hidden text-left xl:block">
                      <span class="block font-medium text-slate-600 dark:text-gray-300">Maria Gibson</span>
                      <span class="-mt-0.5 block text-xs text-slate-500 dark:text-gray-400">Admin</span>
                    </span>
                  </button>

                  <div class="left-auto right-0 z-50 my-1 hidden list-none divide-y divide-gray-100 rounded border border-slate-700/10 text-base shadow dark:divide-gray-600 bg-white dark:bg-slate-800 w-40 fc-dropdown" id="navUserdata">
            
                    <ul class="py-1" aria-labelledby="navUserdata">
                      <li>
                        <a href="#" class="flex items-center py-2 px-3 text-sm text-gray-700 hover:bg-gray-50
                          dark:text-gray-200 dark:hover:bg-gray-900/20
                          dark:hover:text-white">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="user" class="lucide lucide-user w-4 h-4 inline-block text-slate-800 dark:text-slate-400 me-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                          Profile</a>
                      </li>
                      <li>
                        <a href="#" class="flex items-center py-2 px-3 text-sm text-gray-700 hover:bg-gray-50
                          dark:text-gray-200 dark:hover:bg-gray-900/20
                          dark:hover:text-white">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="settings" class="lucide lucide-settings w-4 h-4 inline-block text-slate-800 dark:text-slate-400 me-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                          Settings</a>
                      </li>
                      <li>
                        <a href="#" class="flex items-center py-2 px-3 text-sm text-gray-700 hover:bg-gray-50
                          dark:text-gray-200 dark:hover:bg-gray-900/20
                          dark:hover:text-white">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="dollar-sign" class="lucide lucide-dollar-sign w-4 h-4 inline-block text-slate-800 dark:text-slate-400 me-2"><line x1="12" x2="12" y1="2" y2="22"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                          Earnings</a>
                      </li>
                      <li>
                        <a href="auth-lockscreen.html" class="flex items-center py-2 px-3 text-sm text-red-500 hover:bg-gray-50 hover:text-red-600
                          dark:text-red-500 dark:hover:bg-gray-900/20
                          dark:hover:text-red-500">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="power" class="lucide lucide-power w-4 h-4 inline-block text-red-500 dark:text-red-500 me-2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" x2="12" y1="2" y2="12"></line></svg>
                          Sign out</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </nav>