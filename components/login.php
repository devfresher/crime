                <div class="col-md-3 login-form">
				    <h4 class="text-center text-white mb-2 fs-18">Sign in your account</h4>
                    <form action="<?php echo BASE_URL ?>controllers/auth.php" method="POST">
                        <div class="form-group">
                            <input type="text" name="username" class="form-control" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <button type="submit" name="login" class="btn btn-block">Login</button>
                        </div>
                    </form>

                    <p class="m-0">You don't have an account and you want to be a member? Contact the admin.</p>
                </div>