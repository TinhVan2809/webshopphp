<main class="container mx-auto px-7 py-20 mt-20 text-center min-h-[60vh] flex flex-col items-center justify-center">
    <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-6 mx-auto">
        <i class="ri-close-line text-5xl text-red-500"></i>
    </div>
    <h1 class="text-4xl font-bold mb-4 text-gray-800">Đặt hàng hoặc Thanh toán thất bại!</h1>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-lg mb-8 max-w-md mx-auto">
            <i class="ri-error-warning-line mr-2"></i> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600 mb-8">Rất tiếc, đã có lỗi xảy ra trong quá trình xử lý đơn hàng của bạn. Vui lòng thử lại sau.</p>
    <?php endif; ?>
    
    <div class="flex justify-center gap-4">
        <a href="index.php?action=checkout" class="px-8 py-3 bg-black text-white rounded-full font-medium hover:bg-gray-800 transition-colors">Thử lại</a>
        <a href="index.php" class="px-8 py-3 bg-white text-black border border-gray-300 rounded-full font-medium hover:bg-gray-50 transition-colors">Về trang chủ</a>
    </div>
</main>
