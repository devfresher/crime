            <nav class="navbar navbar-expand-lg navbar-light mb-4">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-0">
                            <li class="nav-item <?php echo PAGE_NAME == 'home' ? 'active':'' ?>">
                                <a class="nav-link" href="<?php echo BASE_URL ?>home">Home</a>
                            </li>
                            <?php if ($user->isLoggedIn() === true) { ?>
                                <li class="nav-item <?php echo PAGE_NAME == 'dashboard' ? 'active':'' ?>">
                                    <a class="nav-link" href="<?php echo BASE_URL ?>dashboard">Dashboard</a>
                                </li>
                            <?php } ?>
                            <li class="nav-item <?php echo PAGE_NAME == 'members' ? 'active':'' ?>">
                                <a class="nav-link" href="<?php echo BASE_URL ?>members">Excecutives / Members</a>
                            </li>
                            <li class="nav-item <?php echo PAGE_NAME == 'profile' ? 'active':'' ?>">
                                <a class="nav-link" href="<?php echo BASE_URL ?>profile">Profile</a>
                            </li>
                            <li class="nav-item <?php echo PAGE_NAME == 'news' ? 'active':'' ?>">
                                <a class="nav-link" href="<?php echo BASE_URL ?>news">News</a>
                            </li>
                            <li class="nav-item <?php echo PAGE_NAME == 'about' ? 'active':'' ?>">
                                <a class="nav-link" href="<?php echo BASE_URL ?>about">About us</a>
                            </li>
                            <li class="nav-item <?php echo PAGE_NAME == 'contact' ? 'active':'' ?>">
                                <a class="nav-link" href="<?php echo BASE_URL ?>contact">Contact us</a>
                            </li>

                            <?php if ($user->isLoggedIn() === true) { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo BASE_URL ?>logout">Logout</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
    </section>
