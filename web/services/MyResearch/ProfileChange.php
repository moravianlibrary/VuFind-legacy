<?php

require_once 'services/MyResearch/MyResearch.php';

class ProfileChange extends MyResearch
{

    public function __construct() {
        parent::__construct();
    }

    /**
     * Process parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $interface;
        global $user;

        if ($patron = UserAccount::catalogLogin()) {
            if (PEAR::isError($patron)) {
                PEAR::raiseError($patron);
            }
        }
        $operation = $_REQUEST['op'];
        if ($operation == 'password') {
            if (isset($_GET['success'])) {
                $interface->assign('userMsg', 'Password has been changed');
                $interface->assign('showPasswordChangeForm', false);
            } else {
                $interface->assign('showPasswordChangeForm', true);
            }
            $interface->setPageTitle('Password change');
            $interface->assign('operation', 'password');
            $interface->assign('hmac', $this->getHMAC());
            if (isset($_POST['submit'])) {
                $this->processChangePassword($patron);
            }
        } else if ($operation == 'nickname') {
            if (isset($_GET['success'])) {
                $interface->assign('userMsg', 'Nickname has been changed');
                $interface->assign('showNicknameChangeForm', false);
            } else {
                $interface->assign('showNicknameChangeForm', true);
            }
            $nick = $this->catalog->getUserNickname($patron);
            if (!PEAR::isError($nick)) {
                $interface->assign('nickname', $this->catalog->getUserNickname($patron));
            }
            $interface->setPageTitle('Nickname change');
            $interface->assign('operation', 'nickname');
            $interface->assign('hmac', $this->getHMAC());
            if (isset($_POST['submit'])) {
                $this->processChangeNickname($patron);
            }
        } else if ($operation == 'email') {
            if (isset($_GET['success'])) {
                $interface->assign('userMsg', 'Email has been changed');
                $interface->assign('showEmailChangeForm', false);
            } else {
                $interface->assign('showEmailChangeForm', true);
            }
            if ($user->email) {
                $interface->assign('email', $user->email);
            }
            $interface->setPageTitle('Email address change');
            $interface->assign('operation', 'email');
            $interface->assign('hmac', $this->getHMAC());
            if (isset($_POST['submit'])) {
                $this->processChangeEmailAddress($patron);
            }
        }
        $interface->setTemplate('profile-change.tpl');
        $interface->display('layout.tpl');
    }
    
    protected function processChangePassword($patron) {
        global $interface;
        $hmac = $_POST['hmac'];
        if ($hmac != $this->getHMAC()) {
            $interface->assign('userErrorMsg', 'Your session has timed out, please reload page to login again');
            return;
        }
        $oldPassword = $_POST['old_password'];
        $newPassword = $_POST['new_password'];
        $newPasswordCheck = $_POST['new_password_repeat'];
        if ($newPassword != $newPasswordCheck) {
            $interface->assign('operation', 'password');
            $interface->assign('userErrorMsg', 'Password fields do not match or empty');
            return;
        }
        if (trim($newPassword) == '') {
            $interface->assign('operation', 'password');
            $interface->assign('userErrorMsg', 'New password is empty');
            return;
        }
        $result = $this->catalog->changeUserPassword($patron, $oldPassword, $newPassword);
        if (PEAR::isError($result)) {
            $interface->assign('userErrorMsg', 'Password change failed, bad password?');
        } else {
            header("Location: " . $configArray['Site']['url'] . '/MyResearch/ProfileChange?op=password&success=true');
        }
    }
    
    protected function processChangeNickname($patron) {
        global $interface;
        $hmac = $_POST['hmac'];
        if ($hmac != $this->getHMAC()) {
            $interface->assign('userErrorMsg', 'Your session has timed out, please reload page to login again');
            return;
        }
        $nickname = trim($_POST['nickname']);
        if (empty($nickname)) {
            $interface->assign('userErrorMsg', 'Nickname is empty');
            return;
        }
        if (strlen($nickname) > 20) {
            $interface->assign('userErrorMsg', 'Max nickname length is 20 characters');
            return;
        }
        if (!preg_match('/^[\w_]*$/', $nickname)) {
            $interface->assign('userErrorMsg', 'Nickname must contain only alphanumeric characters and underscores');
            return;
        }
        $result = $this->catalog->changeUserNickname($patron, $nickname);
        if (PEAR::isError($result)) {
            $interface->assign('userErrorMsg', 'Nickname change failed, nickname is already used');
        } else {
            header("Location: " . $configArray['Site']['url'] . '/MyResearch/ProfileChange?op=nickname&success=true');
        }
    }
    
    protected function processChangeEmailAddress($patron) {
        global $interface;
        global $user;
        $hmac = $_POST['hmac'];
        if ($hmac != $this->getHMAC()) {
            $interface->assign('userErrorMsg', 'Your session has timed out, please reload page to login again');
            return;
        }
        $email = $_POST['email'];
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
            $interface->assign('userErrorMsg', 'Bad email address');
            return;
        }
        $result = $this->catalog->changeUserEmailAddress($patron, $email);
        if (PEAR::isError($result)) {
            $interface->assign('userErrorMsg', 'Email change failed');
        } else {
            $user->email = $email;
            $user->update();
            UserAccount::updateSession($user);
            header("Location: " . $configArray['Site']['url'] . '/MyResearch/ProfileChange?op=email&success=true'); 
        }
    }
    
    protected function getHMAC() {
        return hash_hmac('md5', session_id(), $configArray['Security']['HMACkey']);
    }

}