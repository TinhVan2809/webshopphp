<?php
// src/Controllers/PageController.php
namespace Tinhl\Bai01QuanlySv\Controllers;

use Tinhl\Bai01QuanlySv\Models\ContactModel;
use Tinhl\Bai01QuanlySv\Core\FlashMessage;

class PageController
{
    private $contactModel;
    public function __construct()
    {
        $this->contactModel = new ContactModel();
    }
    /**
     * Hiển thị form liên hệ
     */
    public function showContactForm()
    {
        require_once PROJECT_ROOT . '/views/contact.php';
    }
    /**
     * Xử lý dữ liệu từ form liên hệ
     */
    public function submitContact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';
            if (
                empty($name) || empty($email) || empty($message)

                || !filter_var($email, FILTER_VALIDATE_EMAIL)
            ) {

                FlashMessage::set('contact_form', 'Vui lòng điền đầy đủ và đúng định dạng thông tin.', 'error');
            } else {
                if ($this->contactModel->saveContact(
                    $name,

                    $email,
                    $message
                )) {

                    FlashMessage::set('contact_form', 'Tin nhắn của bạn đã được gửi thành công!', 'success');
                } else {
                    FlashMessage::set('contact_form', 'Đã có lỗi xảy ra. Vui lòng thử lại.', 'error');
                }
            }
        }


        header('Location: index.php?action=contact');

        exit();
    }
}
