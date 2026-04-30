<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold"><?= $product ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' ?></h2>
        <p class="text-gray-500 text-sm mt-1">Vui lòng nhập đầy đủ thông tin bên dưới</p>
    </div>
    <a href="index.php?action=admin_products" class="text-gray-500 hover:text-black flex items-center gap-2 font-medium">
        <i class="ri-arrow-left-line"></i> Quay lại
    </a>
</div>

<form action="index.php?action=save_product" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?? '' ?>">
    <input type="hidden" name="current_thumbnail" value="<?= $product['thumbnail'] ?? '' ?>">

    <!-- Left Column: Basic Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tên sản phẩm</label>
                <input type="text" name="name" value="<?= $product['name'] ?? '' ?>" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="Ví dụ: Nike Air Max 2024">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả chi tiết</label>
                <textarea name="description" rows="10"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="Nhập mô tả sản phẩm..."><?= $product['description'] ?? '' ?></textarea>
            </div>
        </div>

        <!-- Biến thể sản phẩm -->
        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold">Biến thể sản phẩm (Size, Màu sắc...)</h3>
                <button type="button" onclick="addVariantRow()" class="text-sm bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-bold hover:bg-blue-100">
                    + Thêm biến thể
                </button>
            </div>
            
            <div id="variant-container" class="space-y-4">
                <?php if (!empty($variants)): ?>
                    <?php foreach ($variants as $idx => $v): 
                        $attrs = [];
                        if (!empty($v['attr_string'])) {
                            foreach(explode(',', $v['attr_string']) as $pair) {
                                if(strpos($pair, ':') !== false) {
                                    list($name, $val) = explode(':', $pair);
                                    $attrs[strtolower(trim($name))] = trim($val);
                                }
                            }
                        }
                    ?>
                        <div class="variant-row grid grid-cols-12 gap-4 p-4 bg-gray-50 rounded-xl relative group items-center">
                            <input type="hidden" name="variants[<?= $idx ?>][current_image]" value="<?= $v['image'] ?>">
                            <div class="col-span-1">
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Ảnh</label>
                                <div class="relative w-full aspect-square bg-white border border-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                    <img src="/web-shop-php/asset/<?= $v['image'] ?: 'placeholder.png' ?>" class="w-full h-full object-cover">
                                    <input type="file" name="variant_images[<?= $idx ?>]" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label class="text-[10px] font-bold text-gray-400 uppercase">SKU</label>
                                <input type="text" name="variants[<?= $idx ?>][sku]" value="<?= $v['sku'] ?>" class="w-full bg-white border border-gray-100 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Size</label>
                                <input type="text" name="variants[<?= $idx ?>][attrs][size]" value="<?= $attrs['size'] ?? '' ?>" oninput="updateVariantSKU(<?= $idx ?>)" class="w-full bg-white border border-gray-100 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Màu</label>
                                <input type="text" name="variants[<?= $idx ?>][attrs][color]" value="<?= $attrs['color'] ?? '' ?>" oninput="updateVariantSKU(<?= $idx ?>)" class="w-full bg-white border border-gray-100 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Giá (₫)</label>
                                <input type="number" name="variants[<?= $idx ?>][price]" value="<?= $v['price'] ?>" class="w-full bg-white border border-gray-100 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Kho</label>
                                <input type="number" name="variants[<?= $idx ?>][stock]" value="<?= $v['stock'] ?>" class="w-full bg-white border border-gray-100 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="col-span-1 text-red-400 hover:text-red-600 mt-4"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Giá gốc (₫)</label>
                <input type="number" name="price" value="<?= $product['price'] ?? '' ?>" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Giá khuyến mãi (₫)</label>
                <input type="number" name="discount_price" value="<?= $product['discount_price'] ?? '' ?>"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="Để trống nếu không có">
            </div>
        </div>
    </div>

    <!-- Right Column: Meta & Image -->
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh đại diện</label>
                <?php if(isset($product['thumbnail'])): ?>
                    <img src="/web-shop-php/asset/<?= $product['thumbnail'] ?>" class="w-full h-48 object-cover rounded-xl mb-4">
                <?php endif; ?>
                <input type="file" name="thumbnail" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-black file:text-white hover:file:bg-gray-800">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Danh mục</label>
                <select name="category_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= $cat['category_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Thương hiệu</label>
                <select name="manufacturer_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
                    <?php foreach($manufacturers as $man): ?>
                        <option value="<?= $man['manufacturer_id'] ?>" <?= (isset($product['manufacturer_id']) && $product['manufacturer_id'] == $man['manufacturer_id']) ? 'selected' : '' ?>>
                            <?= $man['manufacturer_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mã SKU</label>
                <input type="text" name="sku" id="parent_sku" value="<?= $product['sku'] ?? '' ?>"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="Vd: NIKE-AIR-01">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái</label>
                <select name="status" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
                    <option value="active" <?= (isset($product['status']) && $product['status'] == 'active') ? 'selected' : '' ?>>Đang kinh doanh</option>
                    <option value="inactive" <?= (isset($product['status']) && $product['status'] == 'inactive') ? 'selected' : '' ?>>Ngừng kinh doanh</option>
                    <option value="out_of_stock" <?= (isset($product['status']) && $product['status'] == 'out_of_stock') ? 'selected' : '' ?>>Hết hàng</option>
                </select>
            </div>
        </div>

        <?php if (!empty($extra_images)): ?>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <label class="block text-sm font-bold text-gray-700 mb-4">Ảnh phụ hiện tại</label>
            <div class="grid grid-cols-3 gap-3">
                <?php foreach ($extra_images as $img): ?>
                    <div class="relative group">
                        <img src="/web-shop-php/asset/<?= $img['image'] ?>" class="w-full h-20 object-cover rounded-lg border border-gray-100">
                        <a href="index.php?action=delete_product_image&id=<?= $img['image_id'] ?>&product_id=<?= $product['product_id'] ?>" 
                           class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:bg-red-600 shadow-lg"
                           onclick="return confirm('Bạn có chắc muốn xóa ảnh này?')">
                            <i class="ri-close-line text-sm"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Ảnh phụ (Gallery)</label>
        <!-- Quan trọng: name phải có [] và thuộc tính multiple -->
        <input type="file" name="extra_images[]" multiple 
               class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-black outline-none">
        <p class="text-gray-500 text-xs mt-1">Bạn có thể chọn nhiều ảnh cùng lúc (định dạng jpg, png, webp).</p>
    </div>
    
        <button type="submit" class="w-full bg-black text-white py-4 rounded-2xl font-bold shadow-xl shadow-black/10 hover:shadow-black/20 hover:scale-[1.01] transition-all">
            <?= $product ? 'Cập nhật sản phẩm' : 'Lưu sản phẩm' ?>
        </button>
    </div>
</form>

<script>
let variantIdx = <?= !empty($variants) ? count($variants) : 0 ?>;

// Hàm xóa dấu tiếng Việt và chuẩn hóa chuỗi cho SKU
function slugifySKU(str) {
    if (!str) return "";
    return str.normalize('NFD')
              .replace(/[\u0300-\u036f]/g, '') // Xóa dấu
              .replace(/đ/g, 'd').replace(/Đ/g, 'D')
              .replace(/[^a-zA-Z0-9]/g, '-') // Thay ký tự đặc biệt bằng gạch ngang
              .replace(/-+/g, '-') // Xóa gạch ngang thừa
              .replace(/^-+|-+$/g, '') // Xóa gạch ngang ở đầu/cuối
              .toUpperCase();
}

function updateVariantSKU(idx) {
    const parentSku = document.getElementById('parent_sku').value.trim();
    const sizeInput = document.querySelector(`input[name="variants[${idx}][attrs][size]"]`);
    const colorInput = document.querySelector(`input[name="variants[${idx}][attrs][color]"]`);
    const variantSkuInput = document.querySelector(`input[name="variants[${idx}][sku]"]`);

    let parts = [parentSku];
    if (sizeInput && sizeInput.value) parts.push(sizeInput.value);
    if (colorInput && colorInput.value) parts.push(colorInput.value);

    // Chỉ cập nhật nếu có ít nhất mã cha hoặc thuộc tính
    variantSkuInput.value = slugifySKU(parts.join('-'));
}

function addVariantRow() {
    const container = document.getElementById('variant-container');
    const html = `
        <div class="variant-row grid grid-cols-12 gap-4 p-4 bg-gray-50 rounded-xl relative animate-in fade-in zoom-in duration-300 items-center">
            <div class="col-span-1">
                <label class="text-[10px] font-bold text-gray-400 uppercase">Ảnh</label>
                <div class="relative w-full aspect-square bg-white border border-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                    <i class="ri-image-add-line text-gray-300"></i>
                    <input type="file" name="variant_images[${variantIdx}]" class="absolute inset-0 opacity-0 cursor-pointer">
                </div>
            </div>
            <div class="col-span-2">
                <label class="text-[10px] font-bold text-gray-400 uppercase">SKU Biến thể</label>
                <input type="text" name="variants[${variantIdx}][sku]" required class="w-full bg-white border border-gray-100 rounded-lg px-3 py-2 text-sm" placeholder="NIKE-42-RED">
            </div>
            <div class="col-span-2">
                <label class="text-[10px] font-bold text-gray-400 uppercase">Size</label>
                <input type="text" name="variants[${variantIdx}][attrs][size]" oninput="updateVariantSKU(${variantIdx})" class="w-full bg-white border border-gray-100 rounded-lg px-3 py-2 text-sm" placeholder="Vd: 42">
            </div>
            <div class="col-span-2">
                <label class="text-[10px] font-bold text-gray-400 uppercase">Màu</label>
                <input type="text" name="variants[${variantIdx}][attrs][color]" oninput="updateVariantSKU(${variantIdx})" class="w-full bg-white border border-gray-100 rounded-lg px-3 py-2 text-sm" placeholder="Vd: Đỏ">
            </div>
            <div class="col-span-2">
                <label class="text-[10px] font-bold text-gray-400 uppercase">Giá riêng (₫)</label>
                <input type="number" name="variants[${variantIdx}][price]" class="w-full bg-white border border-gray-100 rounded-lg px-3 py-2 text-sm" placeholder="Mặc định">
            </div>
            <div class="col-span-2">
                <label class="text-[10px] font-bold text-gray-400 uppercase">Kho</label>
                <input type="number" name="variants[${variantIdx}][stock]" value="0" class="w-full bg-white border border-gray-100 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="col-span-1 text-red-400 hover:text-red-600 mt-4"><i class="ri-delete-bin-line"></i></button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    variantIdx++;
}
</script>
