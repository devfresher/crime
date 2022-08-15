<?php
class ClientLang
{
    public function __construct() {

        $this->invalid_credentials = "Invalid Credentials";
        $this->invalid_email_format = "Invalid Email Format";
        $this->invalid_phone_number = "Enter a valid phone number";
        
        $this->user_not_found = "User not found";
        $this->user_verified = "Your account has been verified";
        $this->account_not_active = "Account not active";
        $this->account_suspended = "This account has been suspended from all transactions";
        $this->account_not_verified = "Account not verified. Check email to verify";
        
        $this->login_success = "Login successful";
        $this->register_success = "Registration is successful";
        
        $this->username_exist = "Username already exist";
        $this->email_exist = "User Email already exist";
        $this->phone_exist = "User Phone Number already exist";
        
        $this->otp_sent = "OTP has been sent on your email";
        $this->incorrect_pin = "Incorrect transaction pin";
        $this->password_not_match = "Password does not match";
        $this->not_match_password = "Password do not match";
        $this->pass_len_6 = "Minimum password length must be 6";
        $this->pass_reset_sent = "Reset password link sent to email provided";
        $this->pass_reset_not_sent = "Reset password link not sent";
        $this->pass_reset_error = "Password reset failed";
        $this->pass_updated = "Password changed successfully";
        $this->password_sent = "Password has been sent on your email";

        $this->invalid_amount = "Enter a valid amount";
        $this->unexpected_error = "Unexpected error occured";

        $this->search_result_not_found = "Keyword is not found Try different keyword";
        
        $this->accept_terms = "You must agree with the Terms & Conditions";

        $this->update_success = "Updated successfully";
        $this->update_fail = "Update failed";

        $this->required_fields = "Fill all required field";

        $this->plan_created = "Plan Created Successfully";
        $this->plan_updated = "Plan Updated Successfully";

        $this->role_created = "Role Created Successfully";
        $this->role_updated = "Role Updated Successfully";
        $this->role_deleted = "You have deleted a role";

        $this->settings_saved = "Settings Saved Successfully";
        $this->receipient_is_sender = "Sender can not be the receipient";

        $this->pricing_error = "Pricing error, contact site admin";
    }
}