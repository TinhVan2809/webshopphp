<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css"
        rel="stylesheet" />
         <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
             <link rel="stylesheet" href="../src/styles/main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+NO:wght@100..400&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>
    <header class="flex w-full justify-between items-center p-7">
        <div class="flex  justify-center items-center gap-10">
            <div class="flex justify-center items-center">
                <img src="../asset/wf.png" class="w-20">
                <p class="logo-name">Haseki Store</p>
            </div>
            <nav class="flex ml-10">
                <ul class="flex justify-center items-center gap-5">
                    <li class="hover:underline cursor-pointer"><a href="index.php">Home</a></li>
                    <li class="hover:underline cursor-pointer">Products</li>
                    <li class="hover:underline cursor-pointer">Blogs</li>
                    <li class="hover:underline cursor-pointer">Contact</li>
                </ul>
            </nav>
            <div class="flex bg-gray-300/50 rounded-sm gap-1 w-100 px-2 py-1">
                <i class="ri-search-line"></i>
                <input type="text" placeholder="Search for..." class="w-full outline-0">
            </div>
        </div>

        <div class="flex items-center gap-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium">Chào, <b><?= $_SESSION['user_name'] ?></b></span>
                    <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'staff'): ?>
                        <a href="index.php?action=admin_dashboard" class="text-xs bg-blue-500 text-white px-2 py-1 rounded">Admin</a>
                    <?php endif; ?>
                    <a href="index.php?action=logout" class="text-gray-500 hover:text-red-600 transition-colors"><i class="ri-logout-box-r-line text-xl"></i></a>
                </div>
            <?php else: ?>
                <a href="index.php?action=login" class="bg-black text-white px-6 py-2 rounded-full font-bold hover:bg-gray-800 transition-all">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </header>
</body>

</html>