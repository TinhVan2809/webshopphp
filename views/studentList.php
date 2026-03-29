<?php

use Tinhl\Bai01QuanlySv\Core\FlashMessage;

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptDir === '/' || $scriptDir === '.' || $scriptDir === '\\') {
    $scriptDir = '';
}

$avatarBaseUrl = $scriptDir . '/uploads/avatars';
$defaultAvatarAbsolutePath = __DIR__ . '/../public/uploads/avatars/default-avatar.png';
$defaultAvatarUrl = $avatarBaseUrl . '/default-avatar.png';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sinh viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        form {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form input {
            display: block;
            width: 95%;
            margin-bottom: 10px;
            padding: 8px;
        }

        input[type="file"] {
            padding: 6px 0;
        }

        form button {
            padding: 10px 15px;
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
        }

        .flash-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #fff;
            font-weight: bold;
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .flash-success {
            background-color: #28a745;
        }

        .flash-error {
            background-color: #dc3545;
        }

        .avatar-image,
        .avatar-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .avatar-image {
            display: block;
            object-fit: cover;
        }

        .avatar-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef;
            color: #495057;
            font-size: 12px;
            font-weight: bold;
        }

        .helper-text {
            margin-top: -4px;
            margin-bottom: 12px;
            color: #666;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php FlashMessage::display(); ?>
    </div>

    <div class="container">
        <div style="text-align: right; margin-bottom: 15px;">
            Chào mừng, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!
            <a href="index.php?action=logout" style="margin-left: 15px;">Đăng xuất</a>
        </div>

        <h1>
            <?php
            if (isset($keyword) && !empty($keyword)) {
                echo "Kết quả tìm kiếm cho: '" . htmlspecialchars($keyword) . "'";
            } else {
                echo 'Danh sách sinh viên';
            }
            ?>
        </h1>

        <form action="index.php" method="GET">
            <input type="text" name="keyword" placeholder="Tìm kiếm theo tên..." value="<?php echo htmlspecialchars($keyword ?? ''); ?>">
            <button type="submit">Tìm kiếm</button>
        </form>

        <form action="index.php?action=add" method="POST" enctype="multipart/form-data">
            <h3>Thêm sinh viên mới</h3>
            <input type="text" name="name" placeholder="Họ và tên" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Số điện thoại" required>
            <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif,.webp,image/*">
            <div class="helper-text">Ảnh đại diện là tùy chọn. Hỗ trợ JPG, PNG, GIF, WEBP, tối đa 2MB.</div>
            <button type="submit">Thêm mới</button>
        </form>

        <h2>Danh sách sinh viên</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh đại diện</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td>
                            <?php
                            $avatarFile = $student['avatar'] ?? '';
                            $avatarAbsolutePath = __DIR__ . '/../public/uploads/avatars/' . $avatarFile;
                            $avatarUrl = $avatarBaseUrl . '/' . rawurlencode($avatarFile);
                            ?>
                            <?php if (!empty($avatarFile) && is_file($avatarAbsolutePath)): ?>
                                <img
                                    src="<?php echo htmlspecialchars($avatarUrl); ?>"
                                    alt="Avatar của <?php echo htmlspecialchars($student['name']); ?>"
                                    class="avatar-image">
                            <?php elseif (is_file($defaultAvatarAbsolutePath)): ?>
                                <img
                                    src="<?php echo htmlspecialchars($defaultAvatarUrl); ?>"
                                    alt="Avatar mặc định"
                                    class="avatar-image">
                            <?php else: ?>
                                <span class="avatar-placeholder">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="5">Chưa có sinh viên nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        const flashMessages = document.querySelectorAll('.flash-message');

        if (flashMessages.length > 0) {
            setTimeout(() => {
                flashMessages.forEach((message) => {
                    message.style.opacity = '0';

                    setTimeout(() => {
                        message.style.display = 'none';
                    }, 500);
                });
            }, 5000);
        }
    </script>
</body>

</html>
