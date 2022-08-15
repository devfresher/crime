<?php 
	require_once './components/head.php';
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
            <!-- <div class="row">
                <div class="col-md-3"> -->
                    <?php include_once COMPONENT_DIR.'sideBar.php'; ?>
                <!-- </div>
                <div class="col-md-9">

                </div> -->
            </div>
        </section>

    </main>
    <?php include_once COMPONENT_DIR.'footer.php'; ?>

    <?php include_once COMPONENT_DIR.'js.php'; ?>
</body>
</html>