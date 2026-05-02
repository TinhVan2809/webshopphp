<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Các thẻ thống kê nhanh (Ví dụ) -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500 uppercase">Doanh thu</p>
        <h3 class="text-2xl font-bold mt-1"><?= number_format($totalRevenue ?? 0, 0, ',', '.') ?>₫</h3>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-red-500">
        <p class="text-sm font-medium text-gray-500 uppercase">Sản phẩm sắp hết hàng</p>
        <h3 class="text-2xl font-bold mt-1 text-red-600"><?= count($lowStockProducts) ?> sản phẩm</h3>
    </div>
</div>

<!-- Bảng sản phẩm tồn kho thấp -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="ri-error-warning-line text-red-500"></i> Cảnh báo tồn kho thấp
        </h2>
        <a href="index.php?action=admin_products" class="text-sm text-blue-600 hover:underline">Quản lý kho -></a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4">Sản phẩm</th>
                    <th class="px-6 py-4">SKU</th>
                    <th class="px-6 py-4">Hiện có</th>
                    <th class="px-6 py-4">Mức tối thiểu</th>
                    <th class="px-6 py-4">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($lowStockProducts)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">Tuyệt vời! Không có sản phẩm nào sắp hết hàng.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lowStockProducts as $item): ?>
                        <tr class="hover:bg-red-50/30 transition-colors">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img src="../asset/<?= $item['thumbnail'] ?>" class="w-10 h-10 rounded object-cover border">
                                <span class="text-sm font-medium text-gray-800"><?= htmlspecialchars($item['name']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?= $item['variant_sku'] ?: $item['product_sku'] ?></td>
                            <td class="px-6 py-4 font-bold text-red-600"><?= $item['available_quantity'] ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= $item['min_stock_level'] ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700 uppercase">
                                    <?= $item['available_quantity'] == 0 ? 'Hết hàng' : 'Sắp hết' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>