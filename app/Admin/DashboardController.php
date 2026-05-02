<?php

require_once __DIR__ . '/AdminBaseController.php';

class DashboardController extends AdminBaseController
{
    public function index()
    {
        // 1. Lấy thống kê cơ bản cho các thẻ trên cùng
        $stats = [
            'total_users' => $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_products' => $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'total_orders' => $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'total_revenue' => $this->db->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?? 0
        ];

        // 2. Truy vấn lấy danh sách sản phẩm sắp hết hàng (available_quantity <= min_stock_level)
        $query = "SELECT p.name, pv.sku as variant_sku, p.sku as product_sku, 
                         i.available_quantity, i.min_stock_level, p.thumbnail, i.status, p.product_id
                  FROM inventory i
                  JOIN products p ON i.product_id = p.product_id
                  LEFT JOIN product_variants pv ON i.variant_id = pv.variant_id
                  WHERE i.available_quantity <= i.min_stock_level
                  ORDER BY i.available_quantity ASC
                  LIMIT 10";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $lowStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Xử lý dữ liệu biểu đồ doanh thu 7 ngày gần nhất
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('d/m', strtotime("-$i days"));
            $chartData[$date] = ['label' => $label, 'value' => 0];
        }

        $queryRev = "SELECT DATE(created_at) as order_date, SUM(total_amount) as daily_total 
                     FROM orders 
                     WHERE status = 'completed' 
                     AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                     GROUP BY DATE(created_at)";
        $resRev = $this->db->query($queryRev)->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resRev as $row) {
            if (isset($chartData[$row['order_date']])) {
                $chartData[$row['order_date']]['value'] = (float)$row['daily_total'];
            }
        }

        // 4. Render view dashboard với đầy đủ dữ liệu
        $this->render('dashboard', [
            'stats' => $stats,
            'lowStockProducts' => $lowStockProducts,
            'chartLabels' => array_column($chartData, 'label'),
            'chartValues' => array_column($chartData, 'value')
        ]);
    }
}