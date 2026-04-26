<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once PROJECT_ROOT . '/app/PHPMailer/Exception.php';
require_once PROJECT_ROOT . '/app/PHPMailer/PHPMailer.php';
require_once PROJECT_ROOT . '/app/PHPMailer/SMTP.php';
require_once PROJECT_ROOT . '/config.php';
require_once PROJECT_ROOT . '/app/Database.php';

class EmailService
{
    public function sendOrderConfirmation($orderId)
    {
        $db = (new Database())->getConnection();

        // Get Order Details
        $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = :id");
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) return false;

        // Get User Email
        $stmtUser = $db->prepare("SELECT gmail FROM users WHERE user_id = :id");
        $stmtUser->execute(['id' => $order['user_id']]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        
        $toEmail = $user['gmail'];
        if (!$toEmail) return false; // Không có email

        // Get Order Items
        $stmtItems = $db->prepare("SELECT * FROM order_items WHERE order_id = :id");
        $stmtItems->execute(['id' => $orderId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            //Recipients
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($toEmail, $order['recipient_name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Xac nhan don hang #' . $order['order_code'] . ' tu Haseki Store';

            $itemsHtml = '';
            foreach ($items as $item) {
                $itemsHtml .= "
                    <tr>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$item['product_name']}</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$item['quantity']}</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>" . number_format($item['price'], 0, ',', '.') . "₫</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>" . number_format($item['total_price'], 0, ',', '.') . "₫</td>
                    </tr>
                ";
            }

            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;'>
                    <h2 style='text-align: center; color: #000;'>Cảm ơn bạn đã đặt hàng!</h2>
                    <p>Xin chào <strong>{$order['recipient_name']}</strong>,</p>
                    <p>Haseki Store đã nhận được đơn đặt hàng của bạn và đang tiến hành xử lý.</p>
                    
                    <h3 style='border-bottom: 2px solid #000; padding-bottom: 5px;'>Thông tin đơn hàng #{$order['order_code']}</h3>
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                        <thead>
                            <tr style='background-color: #f5f5f5;'>
                                <th style='padding: 10px; text-align: left;'>Sản phẩm</th>
                                <th style='padding: 10px; text-align: center;'>SL</th>
                                <th style='padding: 10px; text-align: right;'>Đơn giá</th>
                                <th style='padding: 10px; text-align: right;'>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemsHtml}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='3' style='padding: 10px; text-align: right; font-weight: bold;'>Tạm tính:</td>
                                <td style='padding: 10px; text-align: right;'>" . number_format($order['subtotal'], 0, ',', '.') . "₫</td>
                            </tr>
                            <tr>
                                <td colspan='3' style='padding: 10px; text-align: right; font-weight: bold;'>Phí giao hàng:</td>
                                <td style='padding: 10px; text-align: right;'>" . number_format($order['shipping_fee'], 0, ',', '.') . "₫</td>
                            </tr>
                            <tr>
                                <td colspan='3' style='padding: 10px; text-align: right; font-weight: bold; font-size: 16px; color: #e53e3e;'>Tổng cộng:</td>
                                <td style='padding: 10px; text-align: right; font-weight: bold; font-size: 16px; color: #e53e3e;'>" . number_format($order['total_amount'], 0, ',', '.') . "₫</td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <h3 style='border-bottom: 2px solid #000; padding-bottom: 5px;'>Thông tin giao hàng</h3>
                    <p><strong>Người nhận:</strong> {$order['recipient_name']}</p>
                    <p><strong>Số điện thoại:</strong> {$order['recipient_phone']}</p>
                    <p><strong>Địa chỉ:</strong> {$order['specific_address']}, {$order['ward_name']}, {$order['district_name']}, {$order['province_name']}</p>
                    
                    <p style='margin-top: 30px; text-align: center; color: #666; font-size: 12px;'>
                        Đây là email tự động, vui lòng không trả lời. <br>
                        Nếu bạn cần hỗ trợ, vui lòng liên hệ qua hotline hoặc email của cửa hàng.
                    </p>
                </div>
            ";

            $mail->Body = $body;
            $mail->AltBody = 'Cảm ơn bạn đã đặt hàng. Đơn hàng ' . $order['order_code'] . ' đang được xử lý.';

            $mail->send();
            return true;
        } catch (Exception $e) {
            $errorMsg = "Không thể gửi Email. Lỗi Mailer: {$mail->ErrorInfo}";
            error_log($errorMsg);
            throw new Exception($errorMsg);
        }
    }
}
