<?php

use Tinhl\Bai01QuanlySv\Core\FlashMessage;

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptDir === '/' || $scriptDir === '.' || $scriptDir === '\\') {
    $scriptDir = '';
}

$avatarBaseUrl = $scriptDir . '/uploads/avatars';
$defaultAvatarAbsolutePath = __DIR__ . '/../public/uploads/avatars/default-avatar.png';
$defaultAvatarUrl = $avatarBaseUrl . '/default-avatar.png';
$isEditing = !empty($editingStudent);
$formAction = $isEditing ? 'index.php?action=update' : 'index.php?action=add';
$formTitle = $isEditing ? 'Sửa thông tin sinh viên' : 'Thêm sinh viên mới';
$submitLabel = $isEditing ? 'Cập nhật' : 'Thêm mới';
$helperText = $isEditing
    ? 'Chọn ảnh mới nếu muốn thay avatar hiện tại. Bỏ trống để giữ nguyên.'
    : 'Ảnh đại diện là tùy chọn. Hỗ trợ JPG, PNG, GIF, WEBP, tối đa 2MB.';
$editName = $editingStudent['name'] ?? '';
$editEmail = $editingStudent['email'] ?? '';
$editPhone = $editingStudent['phone'] ?? '';
$editAvatar = $editingStudent['avatar'] ?? '';
$editAvatarAbsolutePath = __DIR__ . '/../public/uploads/avatars/' . $editAvatar;
$editAvatarUrl = $avatarBaseUrl . '/' . rawurlencode($editAvatar);
$editCourse = $editingStudent['course'] ?? '';
$editClassName = $editingStudent['class_name'] ?? '';
$editMajor = $editingStudent['major'] ?? '';
$currentPage = (int) ($currentPage ?? 1);
$totalPages = (int) ($totalPages ?? 1);
$totalStudents = (int) ($totalStudents ?? 0);
$listStart = (int) ($listStart ?? 0);
$listEnd = (int) ($listEnd ?? 0);
$keyword = (string) ($keyword ?? '');
$sortby = (string) ($sortby ?? 'id');
$order = strtolower((string) ($order ?? 'desc')) === 'asc' ? 'asc' : 'desc';
$nextOrder = $order === 'asc' ? 'desc' : 'asc';

$buildListUrl = static function (int $page, ?string $sortColumn = null, ?string $sortDirection = null) use ($keyword, $sortby, $order): string {
    $params = [];
    $sortColumn = $sortColumn ?? $sortby;
    $sortDirection = $sortDirection ?? $order;

    if ($page > 1) {
        $params['page'] = $page;
    }

    if ($keyword !== '') {
        $params['keyword'] = $keyword;
    }

    if ($sortColumn !== 'id' || $sortDirection !== 'desc') {
        $params['sortby'] = $sortColumn;
        $params['order'] = $sortDirection;
    }

    return 'index.php' . (!empty($params) ? '?' . http_build_query($params) : '');
};

$cancelUrl = $buildListUrl($currentPage);

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
            max-width: 1000px;
            margin: auto;
        }

        .topbar,
        .toolbar,
        .pagination-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
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

        form button,
        .button-link,
        .action-link,
        .page-link {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }

        form button {
            background-color: #28a745;
        }

        .button-link {
            background-color: #17a2b8;
        }

        .button-link.cancel {
            background-color: #6c757d;
        }

        .action-link {
            padding: 8px 12px;
            background-color: #007bff;
        }

        .delete {
            background-color: #dc3545 !important;
        }

        .detail {
            background-color: #198754;
        }

        .action-cell {
            white-space: nowrap;
        }

        .current-avatar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .current-avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
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

        .helper-text,
        .pagination-summary {
            color: #666;
            font-size: 13px;
        }

        .helper-text {
            margin-top: -4px;
            margin-bottom: 12px;
        }

        .pagination {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .page-link {
            background-color: #6c757d;
            padding: 8px 12px;
        }

        .page-link.active {
            background-color: #0d6efd;
            font-weight: bold;
        }

        /* views/sinhvien_list.php -> bên trong thẻ <style> */
        th a {
            text-decoration: none;
            color: #333;
            display: block;
            position: relative;
        }

        th a .sort-arrow {
            display: none;
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
        }

        th a.sort-asc .sort-arrow::after {
            content: " ▲";
        }

        th a.sort-desc .sort-arrow::after {
            content: " ▼";
        }

        th a.active .sort-arrow {
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php FlashMessage::display(); ?>
    </div>

    <div class="container">
        <div class="topbar" style="margin-bottom: 15px;">
            <div>
                Chào mừng, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!
            </div>
            <a href="index.php?action=logout" class="button-link cancel">Đăng xuất</a>
        </div>

        <div class="toolbar" style="margin-bottom: 15px;">
            <h1 style="margin: 0;">
                <?php if ($keyword !== ''): ?>
                    Kết quả tìm kiếm cho: '<?php echo htmlspecialchars($keyword); ?>'
                <?php else: ?>
                    Danh sách sinh viên
                <?php endif; ?>
            </h1>
            <a href="index.php?action=dashboard" class="button-link">Xem thống kê</a>
        </div>

        <form action="index.php" method="GET">
            <input type="text" name="keyword" placeholder="Tìm kiếm theo tên..." value="<?php echo htmlspecialchars($keyword); ?>">
            <input type="hidden" name="sortby" value="<?php echo htmlspecialchars($sortby); ?>">
            <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
            <button type="submit">Tìm kiếm</button>
        </form>

        <form action="<?php echo htmlspecialchars($formAction); ?>" method="POST" enctype="multipart/form-data">
            <h3><?php echo htmlspecialchars($formTitle); ?></h3>
            <input type="hidden" name="page" value="<?php echo $currentPage; ?>">
            <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
            <input type="hidden" name="sortby" value="<?php echo htmlspecialchars($sortby); ?>">
            <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">

            <?php if ($isEditing): ?>
                <input type="hidden" name="id" value="<?php echo (int) $editingStudent['id']; ?>">
                <div class="current-avatar">
                    <?php if (!empty($editAvatar) && is_file($editAvatarAbsolutePath)): ?>
                        <img src="<?php echo htmlspecialchars($editAvatarUrl); ?>" alt="Avatar hiện tại">
                    <?php elseif (is_file($defaultAvatarAbsolutePath)): ?>
                        <img src="<?php echo htmlspecialchars($defaultAvatarUrl); ?>" alt="Avatar mặc định">
                    <?php else: ?>
                        <span class="avatar-placeholder">N/A</span>
                    <?php endif; ?>
                    <span>Ảnh hiện tại</span>
                </div>
            <?php endif; ?>

            <input type="text" name="name" placeholder="Họ và tên" value="<?php echo htmlspecialchars($editName); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($editEmail); ?>" required>
            <input type="text" name="phone" placeholder="Số điện thoại" value="<?php echo htmlspecialchars($editPhone); ?>" required>
            <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif,.webp,image/*">
            <div class="helper-text"><?php echo htmlspecialchars($helperText); ?></div>

            <input type="text" name="course" placeholder="Khóa học" value="<?php echo htmlspecialchars($editCourse); ?>">
            <input type="text" name="class_name" placeholder="Tên lớp" value="<?php echo htmlspecialchars($editClassName); ?>">
            <input type="text" name="major" placeholder="Ngành học" value="<?php echo htmlspecialchars($editMajor); ?>">

            <button type="submit"><?php echo htmlspecialchars($submitLabel); ?></button>
            <?php if ($isEditing): ?>
                <a href="<?php echo htmlspecialchars($cancelUrl); ?>" class="button-link cancel">Hủy sửa</a>
            <?php endif; ?>
        </form>

        <div class="pagination-summary" style="margin-bottom: 10px;">
            <h2 style="margin: 0;">Danh sách sinh viên</h2>
            <span>
                <?php if ($totalStudents > 0): ?>
                    Hiển thị <?php echo $listStart; ?>-<?php echo $listEnd; ?> / <?php echo $totalStudents; ?> sinh viên
                <?php else: ?>
                    Chưa có sinh viên nào
                <?php endif; ?>
            </span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>
                        <?php


                        $currentColOrder = ($sortby === 'id') ?
                            $nextOrder : 'asc';
                        $activeClass = ($sortby === 'id') ? 'active sort-' . $order : '';

                        ?>

                        <a href="?keyword=<?php echo
                                            urlencode($keyword ?? ''); ?>&page=<?php echo $currentPage;
                                                                                ?>&sortby=id&order=<?php echo $currentColOrder; ?>"
                            class="<?php echo $activeClass; ?>">
                            ID <span class="sort-arrow"></span>
                        </a>
                    </th>
                    <th>Ảnh đại diện</th>
                    <th>
                        <?php

                        $currentColOrder = ($sortby === 'name') ?
                            $nextOrder : 'asc';
                        $activeClass = ($sortby === 'name') ? 'active sort-' . $order : '';
                        ?>

                        <a href="?keyword=<?php echo
                                            urlencode($keyword ?? ''); ?>&page=<?php echo $currentPage;
                                                                                ?>&sortby=name&order=<?php echo $currentColOrder; ?>"
                            class="<?php echo $activeClass; ?>">
                            Họ và Tên <span class="sort-arrow"></span>
                        </a>
                    </th>
                    <th>
                        <?php
                        $currentColOrder = ($sortby === 'email') ?
                            $nextOrder : 'asc';
                        $activeClass = ($sortby === 'email') ?
                            'active sort-' . $order : '';
                        ?>

                        <a href="?keyword=<?php echo
                                            urlencode($keyword ?? ''); ?>&page=<?php echo $currentPage;
                                    ?>&sortby=email&order=<?php echo $currentColOrder; ?>"
                            class="<?php echo $activeClass; ?>">
                            Email <span class="sort-arrow"></span>
                        </a>
                    </th>
                    <th>
                        <?php

                        $currentColOrder = ($sortby === 'phone') ?
                            $nextOrder : 'asc';
                        $activeClass = ($sortby === 'phone') ?
                            'active sort-' . $order : '';
                        ?>

                        <a href="?keyword=<?php echo
                                            urlencode($keyword ?? ''); ?>&page=<?php echo $currentPage;
                                    ?>&sortby=phone&order=<?php echo $currentColOrder; ?>"
                            class="<?php echo $activeClass; ?>">
                            Số điện thoại <span
                                class="sort-arrow"></span>
                        </a>
                    </th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody> 
                <?php foreach ($students as $student): ?>
                    <?php
                    $avatarFile = $student['avatar'] ?? '';
                    $avatarAbsolutePath = __DIR__ . '/../public/uploads/avatars/' . $avatarFile;
                    $avatarUrl = $avatarBaseUrl . '/' . rawurlencode($avatarFile);
                    $urlParams = ['id' => (int) $student['id']];

                    if ($currentPage > 1) {
                        $urlParams['page'] = $currentPage;
                    }

                    if ($keyword !== '') {
                        $urlParams['keyword'] = $keyword;
                    }

                    if ($sortby !== 'id' || $order !== 'desc') {
                        $urlParams['sortby'] = $sortby;
                        $urlParams['order'] = $order;
                    }

                    $editUrl = 'index.php?' . http_build_query(['action' => 'edit'] + $urlParams);
                    $deleteUrl = 'index.php?' . http_build_query(['action' => 'delete'] + $urlParams);
                    $detailUrl = 'index.php?' . http_build_query(['action' => 'detail'] + $urlParams);
                    ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td>
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
                        <td class="action-cell">
                            <a href="<?php echo htmlspecialchars($editUrl); ?>" class="action-link">Sửa</a>
                            <a
                                href="<?php echo htmlspecialchars($deleteUrl); ?>"
                                class="action-link delete"
                                onclick="return confirm('Bạn có chắc muốn xóa sinh viên này?');">Xóa</a>
                            <a href="<?php echo htmlspecialchars($detailUrl); ?>" class="action-link detail">Chi tiết</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="6">Chưa có sinh viên nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="<?php echo htmlspecialchars($buildListUrl($currentPage - 1)); ?>" class="page-link">Trước</a>
                <?php endif; ?>

                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                    <a
                        href="<?php echo htmlspecialchars($buildListUrl($page)); ?>"
                        class="page-link<?php echo $page === $currentPage ? ' active' : ''; ?>">
                        <?php echo $page; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?php echo htmlspecialchars($buildListUrl($currentPage + 1)); ?>" class="page-link">Sau</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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
