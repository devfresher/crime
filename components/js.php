    <!--**********************************
        Scripts
    ***********************************-->
	<script type="text/javascript" src="<?php echo BASE_URL ?>assets/vendor/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo BASE_URL ?>assets/vendor/bootstrap/dist/js/bootstrap.min.js"></script>
    <script  type="text/javascript" src="<?php echo BASE_URL ?>assets/vendor/toastr/js/toastr.min.js"></script>
	<script type="text/javascript" src="<?php echo BASE_URL ?>assets/js/script.js"></script>

    <script>
        const BASE_URL = "<?php echo BASE_URL?>";

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>

    <?php $utility->displayFlashMessage() ?>