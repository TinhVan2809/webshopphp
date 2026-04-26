<?php

require_once PROJECT_ROOT . '/app/Database.php';

class Controller
{
    public function index()
    {
        $database = new Database();
        $db = $database->getConnection();

        // Truy vấn lấy sản phẩm kèm tên danh mục
        $query = "SELECT p.*, c.category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  WHERE p.status = 'active' 
                  ORDER BY p.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Include header (đã có sẵn HTML, Head, Body mở)
        include_once PROJECT_ROOT . '/components/header.php';
?>

        <main class="container mx-auto px-7 py-10 mt-30">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($products as $product): ?>
                    <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <a href="index.php?action=detail&id=<?php echo $product['product_id']; ?>" class="block relative h-64 bg-gray-100">
                            <img src="/web-shop-php/asset/<?php echo $product['thumbnail']; ?>"
                                alt="<?php echo $product['name']; ?>"
                                class="w-full h-full object-cover">
                            <?php if ($product['is_new']): ?>
                                <span class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">Mới</span>
                            <?php endif; ?>
                            <div class="absolute top-0 right-0 p-3">
                                <i class="ri-heart-3-line text-2xl"></i>
                            </div>
                        </a>

                        <div class="p-4">
                            <p class="text-xs text-gray-500 uppercase"><?php echo $product['category_name']; ?></p>
                            <a href="index.php?action=detail&id=<?php echo $product['product_id']; ?>">
                                <h2 class="font-bold text-lg mb-2 truncate hover:text-blue-600 transition-colors"><?php echo $product['name']; ?></h2>
                            </a>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2"><?php echo $product['short_description']; ?></p>

                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-600 font-bold"><?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?>₫</p>
                                    <?php if ($product['discount_price']): ?>
                                        <p class="text-gray-400 text-xs line-through"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</p>
                                    <?php endif; ?>
                                </div>
                                <button class="bg-black text-white p-2 rounded-full hover:bg-gray-800 transition-colors btn-add-to-cart" data-id="<?php echo $product['product_id']; ?>">
                                    <i class="ri-shopping-cart-line pointer-events-none"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <?php
        // Footer
        include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php");
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT p.*, c.category_name, m.manufacturer_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  LEFT JOIN manufacturers m ON p.manufacturer_id = m.manufacturer_id
                  WHERE p.product_id = :id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/header.php';

        if (!$product): ?>
            <div class="container mx-auto px-7 py-20 text-center">
                <h1 class="text-2xl font-bold mb-4">Sản phẩm không tồn tại!</h1>
                <a href="index.php" class="text-blue-600 hover:underline">Quay lại trang chủ</a>
            </div>
        <?php else: ?>
            <main class="container mx-auto px-7 py-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div>
                        <img src="/web-shop-php/asset/<?php echo $product['thumbnail']; ?>" alt="<?php echo $product['name']; ?>" class="w-full rounded-2xl shadow-lg">
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold mb-2"><?php echo $product['name']; ?></h1>
                        <p class="text-gray-500 mb-6 uppercase tracking-wider text-sm"><?php echo $product['category_name']; ?> | <?php echo $product['manufacturer_name'] ?? 'Haseki Store'; ?></p>
                        <div class="text-3xl font-bold text-red-600 mb-6">
                            <?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?>₫
                            <?php if ($product['discount_price']): ?>
                                <span class="text-gray-400 text-xl line-through ml-3"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                            <?php endif; ?>
                        </div>
                        <div class="prose max-w-none text-gray-700 mb-8 leading-relaxed">
                            <?php echo nl2br($product['description'] ?: $product['short_description']); ?>
                        </div>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center border border-gray-300 rounded-full px-4 py-2 w-32">
                                <button type="button" class="text-xl px-2 hover:text-red-500" onclick="document.getElementById('product-quantity').stepDown()">-</button>
                                <input type="number" id="product-quantity" value="1" min="1" class="w-full text-center outline-none bg-transparent font-medium">
                                <button type="button" class="text-xl px-2 hover:text-green-500" onclick="document.getElementById('product-quantity').stepUp()">+</button>
                            </div>
                            <button class="bg-black text-white px-10 py-4 rounded-full font-bold hover:bg-gray-800 transition-all btn-add-to-cart flex-1" data-id="<?php echo $product['product_id']; ?>">
                                THÊM VÀO GIỎ HÀNG
                            </button>
                            <button class="py-3 px-3.5 border border-gray-200 rounded-full hover:bg-gray-50 transition-colors"><i class="ri-heart-line text-2xl"></i></button>
                        </div>
                    </div>
                </div>
            </main>
<?php endif;
       include_once PROJECT_ROOT . '/components/footer.php';
    }


      public function getProfileByUser()
    {
        // 1. Kiểm tra xác thực: Nếu chưa đăng nhập thì không cho vào
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $id = $_GET['id'] ?? null;

        // 2. Kiểm tra phân quyền: Đảm bảo người dùng chỉ xem được hồ sơ của chính mình
        if (!$id || (int)$id !== (int)$_SESSION['user_id']) {
            header("Location: index.php?action=profile&id=" . $_SESSION['user_id']);
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM users
                  WHERE user_id = :id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/header.php';

       if(!$user): ?>
            <div class="container mx-auto py-20 text-center">
                <p class="text-xl font-medium">Trang không tồn tại hoặc tài khoản đã bị khóa.</p>
            </div>
       <?php else: ?>
            <main class="container mx-auto px-7 py-20 mt-10">
                <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                    <h1 class="text-3xl font-bold mb-8 flex items-center gap-3">
                        <i class="ri-user-settings-line"></i> Thông tin cá nhân
                    </h1>
                    
                    <div class="space-y-5">
                        <div class="flex flex-col items-center mb-6">
                            <img src="/web-shop-php/asset/<?php echo $user['avatar'] ?: 'default_avatar.png'; ?>" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm">
                        </div>
                        <div class="flex border-b border-gray-50 pb-3">
                            <span class="w-40 text-gray-500">Họ và tên:</span>
                            <span class="font-semibold"><?php echo htmlspecialchars($user['name']); ?></span>
                        </div>
                        <div class="flex border-b border-gray-50 pb-3">
                            <span class="w-40 text-gray-500">Tên đăng nhập:</span>
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="flex border-b border-gray-50 pb-3">
                            <span class="w-40 text-gray-500">Email:</span>
                            <span><?php echo htmlspecialchars($user['gmail'] ?? 'Chưa cập nhật'); ?></span>
                        </div>
                        <div class="flex">
                            <span class="w-40 text-gray-500">Số điện thoại:</span>
                            <span><?php echo htmlspecialchars($user['number_phone'] ?? 'Chưa cập nhật'); ?></span>
                        </div>
                    </div>

                    <div class="mt-12 flex gap-4">
                        <a href="index.php?action=edit_profile&id=<?php echo $user['user_id']; ?>" class="bg-black text-white px-8 py-2.5 rounded-lg font-bold hover:bg-gray-800 transition-all text-center">Chỉnh sửa</a>
                        <a href="index.php?action=logout" class="border border-red-500 text-red-500 px-8 py-2.5 rounded-lg font-bold hover:bg-red-50 transition-all text-center">Đăng xuất</a>
                    </div>
                </div>
            </main>
       <?php endif;

       include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function editProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id || (int)$id !== (int)$_SESSION['user_id']) {
            header("Location: index.php?action=profile&id=" . $_SESSION['user_id']);
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        include_once PROJECT_ROOT . '/components/header.php';
        ?>
        <main class="container mx-auto px-7 py-20 mt-10">
            <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <h1 class="text-3xl font-bold mb-8 italic">Chỉnh sửa thông tin</h1>
                
                <form action="index.php?action=update_profile" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    
                    <div class="flex flex-col items-center mb-4">
                        <img src="/web-shop-php/asset/<?php echo $user['avatar'] ?: 'default_avatar.png'; ?>" class="w-24 h-24 rounded-full object-cover mb-4 border shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thay đổi ảnh đại diện</label>
                        <input type="file" name="avatar" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800 cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" 
                               class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-black outline-none" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                        <input type="text" name="number_phone" value="<?php echo htmlspecialchars($user['number_phone']); ?>" 
                               class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-black outline-none">
                    </div>

                    <div class="pt-4 flex gap-4">
                        <button type="submit" class="bg-black text-white px-8 py-2.5 rounded-lg font-bold hover:bg-gray-800 transition-all">Lưu thay đổi</button>
                        <a href="index.php?action=profile&id=<?php echo $user['user_id']; ?>" class="bg-gray-200 text-gray-800 px-8 py-2.5 rounded-lg font-bold hover:bg-gray-300 transition-all">Hủy</a>
                    </div>
                </form>
            </div>
        </main>
        <?php
        include_once PROJECT_ROOT . '/components/footer.php';
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? null;
            $name = $_POST['name'] ?? '';
            $number_phone = $_POST['number_phone'] ?? '';

            if (!$user_id || (int)$user_id !== (int)$_SESSION['user_id']) {
                header("Location: index.php");
                exit;
            }

            $database = new Database();
            $db = $database->getConnection();

            // Xử lý Avatar
            $avatar_name = $_SESSION['user_avatar'] ?? 'default_avatar.png';
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $avatar_name = "avatar_" . $user_id . "_" . time() . "." . $file_ext;
                    move_uploaded_file($_FILES['avatar']['tmp_name'], PROJECT_ROOT . '/asset/' . $avatar_name);
                }
            }

            $query = "UPDATE users SET name = :name, number_phone = :phone, avatar = :avatar WHERE user_id = :id";
            $stmt = $db->prepare($query);
            $stmt->execute([
                'name' => $name,
                'phone' => $number_phone,
                'avatar' => $avatar_name,
                'id' => $user_id
            ]);

            $_SESSION['user_name'] = $name;
            $_SESSION['user_avatar'] = $avatar_name;

            header("Location: index.php?action=profile&id=" . $user_id);
            exit;
        }
    }

    public function register()
    {
        include_once PROJECT_ROOT . '/views/register.php';
    }

    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $gender = $_POST['gender'] ?? '0';
            $number_phone = $_POST['number_phone'] ?? '';
            $gmail = $_POST['gmail'] ?? '';

            $database = new Database();
            $db = $database->getConnection();

            // Kiểm tra username đã tồn tại chưa
            $checkQuery = "SELECT user_id FROM users WHERE username = :username";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->execute(['username' => $username]);

            if ($checkStmt->rowCount() > 0) {
                $error = "Tên đăng nhập đã tồn tại!";
                include_once PROJECT_ROOT . '/views/register.php';
                return;
            }

            // Hash the password before saving
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                $query = "INSERT INTO users (name, username, password, gender, number_phone, gmail, role, status) 
                          VALUES (:name, :username, :password, :gender, :phone, :gmail, 'customer', 'active')";
                
                $stmt = $db->prepare($query);
                $result = $stmt->execute([
                    'name' => $name,
                    'username' => $username,
                    'password' => $hashedPassword,
                    'gender' => $gender,
                    'phone' => $number_phone,
                    'gmail' => $gmail
                ]);

                if ($result) {
                    header("Location: index.php?action=login&register_success=1");
                    exit;
                }
            } catch (PDOException $e) {
                $error = "Có lỗi xảy ra: " . $e->getMessage();
                include_once PROJECT_ROOT . '/views/register.php';
            }
        }
    }

    public function login()
    {
        if (isset($_GET['redirect'])) {
            $_SESSION['redirect'] = $_GET['redirect'];
        }
        include_once PROJECT_ROOT . '/views/login.php';
    }

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $database = new Database();
            $db = $database->getConnection();

            $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the hashed password
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_avatar'] = $user['avatar'] ?? 'default_avatar.png';

                require_once PROJECT_ROOT . '/app/CartController.php';
                $cartCtrl = new CartController();
                $cartCtrl->syncSessionCartToDb($user['user_id']);

                // Chuyển hướng dựa trên role
                if (isset($_SESSION['redirect'])) {
                    $redirect = $_SESSION['redirect'];
                    unset($_SESSION['redirect']);
                    header("Location: index.php?action=" . $redirect);
                    exit;
                }

                if ($user['role'] === 'admin' || $user['role'] === 'staff') {
                    header("Location: index.php?action=admin_dashboard");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
                include_once PROJECT_ROOT . '/views/login.php';
            }
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
