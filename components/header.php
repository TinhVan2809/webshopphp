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
    <header class="fixed z-1000 top-0 flex w-full justify-between items-center p-4 md:p-7 z-50 bg-white">
        <div class="flex justify-center items-center gap-4 lg:gap-10">
            <div class="flex justify-center items-center">
                <img src="../asset/wf.png" class="w-12 md:w-20">
                <p class="logo-name text-lg md:text-xl">Haseki Store</p>
            </div>
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex ml-10">
                <ul class="flex justify-center items-center gap-5">
                    <li class="hover:underline cursor-pointer"><a href="index.php">Home</a></li>
                    <li class="hover:underline cursor-pointer"><a href="index.php?action=admin_products">Products</a></li>
                    <li class="hover:underline cursor-pointer">Blogs</li>
                    <li class="hover:underline cursor-pointer">Contact</li>
                </ul>
            </nav>
            <!-- Desktop Search -->
            <div class="hidden lg:flex bg-gray-300/50 rounded-sm gap-1 w-64 px-2 py-1">
                <i class="ri-search-line"></i>
                <input type="text" placeholder="Search for..." class="w-full outline-0">
            </div>
        </div>

        <div class="flex items-center gap-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="flex items-center gap-2 md:gap-3">
                    <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'staff'): ?>
                        <a href="index.php?action=admin_dashboard" class="text-xs bg-blue-500 text-white px-2 py-1 rounded">Admin</a>
                    <?php endif; ?>
                    <!-- <a href="index.php?action=logout" class="text-gray-500 hover:text-red-600 transition-colors"><i class="ri-logout-box-r-line text-xl"></i></a> -->
                     <div class="flex gap-3">
                        <i class="ri-shopping-cart-2-line text-2xl cursor-pointer"></i>
                        <i class="ri-heart-add-2-line text-2xl cursor-pointer"></i>
                        <a href="index.php?action=profile&id=<?php echo $_SESSION['user_id']; ?>" class="flex items-center">
                            <?php if (isset($_SESSION['user_avatar']) && $_SESSION['user_avatar'] !== 'default_avatar.png'): ?>
                                <img src="/web-shop-php/asset/<?php echo $_SESSION['user_avatar']; ?>" class="w-8 h-8 rounded-full object-cover">
                            <?php else: ?>
                                <i class="ri-user-3-line text-2xl cursor-pointer"></i>
                            <?php endif; ?>
                        </a>
                     </div> 
                </div>
            <?php else: ?>
                <a href="index.php?action=login" class="bg-black text-white px-4 py-2 md:px-6 md:py-2 rounded-full text-sm md:font-bold hover:bg-gray-800 transition-all">Đăng nhập</a>
            <?php endif; ?>

            <!-- Mobile Menu Toggle -->
            <button id="menu-toggle" class="md:hidden text-2xl focus:outline-none">
                <i class="ri-menu-3-line"></i>
            </button>
        </div>
    </header>

    <!-- Mobile Menu Drawer -->
    <div id="mobile-menu" class="hidden fixed inset-0 z-40 bg-white pt-24 px-7 transition-all duration-300">
        <nav>
            <ul class="flex flex-col gap-6 text-xl font-medium">
                <li><a href="index.php" class="hover:text-green-700">Home</a></li>
                <li><a href="#" class="hover:text-green-700">Products</a></li>
                <li><a href="#" class="hover:text-green-700">Blogs</a></li>
                <li><a href="#" class="hover:text-green-700">Contact</a></li>
            </ul>
        </nav>
        <div class="mt-10 bg-gray-100 flex items-center p-3 rounded-xl">
            <i class="ri-search-line mr-2 text-gray-400"></i>
            <input type="text" placeholder="Search for..." class="bg-transparent w-full outline-none">
        </div>
    </div>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = menuToggle.querySelector('i');

        menuToggle.addEventListener('click', () => {
            const isHidden = mobileMenu.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden'); // Chống cuộn trang khi mở menu
            
            // Đổi icon từ menu sang close
            if (isHidden) {
                menuIcon.classList.replace('ri-close-line', 'ri-menu-3-line');
            } else {
                menuIcon.classList.replace('ri-menu-3-line', 'ri-close-line');
            }
        });
    </script>
</body>