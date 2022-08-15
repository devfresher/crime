<?php 
	require_once './components/head.php';

    require_once MODEL_DIR.'News.php';
    $news = new News($db);
    $allNews = $news->getAllNews();
?>
</head>
<body>
    <!--**********************************
        Main wrapper start
    ***********************************-->
    <main class="container wrapper">
		<?php include_once COMPONENT_DIR.'header.php'; ?>
        <?php include_once COMPONENT_DIR.'nav.php'; ?>

        <section class="content-body container-fluid">
            <section class="row">
                <div class="<?php echo ($user->isLoggedIn() === false) ? "col-md-9":"col-md-12" ?> slide">
                    <img src="<?php echo BASE_URL ?>assets/img/01.jpg" class="img-fluid">
                </div>

                
                <?php if ($user->isLoggedIn() === false) {
                    include COMPONENT_DIR.'login.php';
                } ?>
            </section>

            <section class="row">
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Most Wanted</h5><hr>
                            <p></p>
                        </div>
                        <div class="col-md-12">
                            <h5>Our Vision</h5><hr>
                            <p></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 news-update">
                    <h5>News Updates</h5><hr>
                    <div>
                        <?php if ($allNews != FALSE) {
                            foreach ($allNews as $index => $newsItem) { ?>
                                <div class="news mb-2">
                                    <a href = "<?php echo BASE_URL ?>news?news=<?php echo $newsItem['id'] ?>">
                                        <?php echo ucwords($newsItem['title']) ?>
                                        <small class="read-more">Read more >></small>
                                    </a>
                                    <small class="d-block"><?php echo $utility->niceDateFormat($newsItem['date']) ?></small>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </div>
            </section>
        </section>

    </main>
    <?php include_once COMPONENT_DIR.'footer.php'; ?>

    <?php include_once COMPONENT_DIR.'js.php'; ?>
</body>
</html>