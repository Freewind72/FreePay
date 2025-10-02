<?php
require_once 'admin/config_encrypt.php';
$encryptor = new ConfigEncrypt();

// 从 SQLite 数据库读取配置信息
try {
    $db = new SQLite3('config/payment_config.db');
    
    // 确保表存在
    $db->exec("CREATE TABLE IF NOT EXISTS merchant_config (
        id INTEGER PRIMARY KEY,
        pid INTEGER NOT NULL,
        merchant_key TEXT NOT NULL,
        api_url TEXT NOT NULL,
        notify_url TEXT NOT NULL,
        return_url TEXT NOT NULL
    )");
    
    // 检查是否存在记录，如果不存在则插入默认值
    $result = $db->query('SELECT COUNT(*) as count FROM merchant_config');
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($row['count'] == 0) {
        // 插入默认配置（加密敏感信息）
        $encrypted_default_key = $encryptor->encrypt('aphcyguMzaBYQ7HEkKQFdsnFQ2yKL0aO');
        $encrypted_default_api_url = $encryptor->encrypt('https://pay.scorcsun.com/submit.php');
        
        $stmt = $db->prepare("INSERT INTO merchant_config (id, pid, merchant_key, api_url, notify_url, return_url) 
                             VALUES (:id, :pid, :merchant_key, :api_url, :notify_url, :return_url)");
        $stmt->bindValue(':id', 1, SQLITE3_INTEGER);
        $stmt->bindValue(':pid', 3, SQLITE3_INTEGER);
        $stmt->bindValue(':merchant_key', $encrypted_default_key, SQLITE3_TEXT);
        $stmt->bindValue(':api_url', $encrypted_default_api_url, SQLITE3_TEXT);
        $stmt->bindValue(':notify_url', 'https://bmwy72.top/Pay/notify_url.php', SQLITE3_TEXT);
        $stmt->bindValue(':return_url', 'https://bmwy72.top/Pay/return_url.php', SQLITE3_TEXT);
        $stmt->execute();
    }
    
    $result = $db->query('SELECT * FROM merchant_config WHERE id = 1');
    $db_config = $result->fetchArray(SQLITE3_ASSOC);
    
    if (!$db_config) {
        die('配置信息不存在，请先初始化数据库');
    }
    
    // 解密敏感配置
    $config = [
        'pid'         => $db_config['pid'],
        'key'         => $encryptor->decrypt($db_config['merchant_key']),
        'api_url'     => $encryptor->decrypt($db_config['api_url']),
        'notify_url'  => $db_config['notify_url'],
        'return_url'  => $db_config['return_url']
    ];
    
    $db->close();
} catch (Exception $e) {
    die('数据库连接失败: ' . $e->getMessage());
}

// 生成订单号
function createOrderNo() {
    return date('YmdHis') . substr(implode('', array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'pid'          => $config['pid'],
        'type'         => $_POST['type'],
        'out_trade_no' => createOrderNo(),
        'notify_url'   => $config['notify_url'],
        'return_url'   => $config['return_url'],
        'name'         => substr($_POST['name'], 0, 127),
        'money'        => number_format($_POST['amount'], 2, '.', ''),
        'timestamp'    => time()
    ];

// 修改后的签名生成代码（需保持与支付平台约定的一致性）
ksort($data);
$sign_str = urldecode(http_build_query($data)) . $config['key']; // 注意urldecode
$data['sign'] = md5($sign_str);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在线支付 - 安全便捷的支付服务</title>
    <link href="https://cdn.bootcss.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .payment-box { max-width: 600px; margin: 5% auto; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .payment-header { background: #2c3e50; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
        .payment-body { padding: 30px; background: #fff; }
        .btn-wechat { background: #09bb07; color: white; }
        .btn-alipay { background: #1677ff; color: white; }
        .amount-input { font-size: 24px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!isset($data)) : ?>
        <form method="post" class="payment-box">
            <div class="payment-header text-center">
                <h2>在线支付</h2>
                <p>安全便捷的支付体验</p>
            </div>
            <div class="payment-body">
                <div class="mb-4">
                    <label class="form-label">支付金额（元）</label>
                    <input type="number" class="form-control amount-input" name="amount" 
                           min="0.01" step="0.01" value="0.01" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">支付留言</label>
                    <input type="text" class="form-control" name="name" 
                           value="支付留言" required>
                </div>

                <div class="d-grid gap-3">
                    <button type="submit" name="type" value="wxpay" 
                            class="btn btn-lg btn-wechat">
                        <i class="bi bi-wechat"></i> 微信支付
                    </button>
                    <button type="submit" name="type" value="alipay" 
                            class="btn btn-lg btn-alipay">
                        <i class="bi bi-wallet2"></i> 支付宝支付
                    </button>
                </div>
            </div>
        </form>

        <?php else : ?>
        <!-- 自动提交表单到支付网关 -->
        <form id="payForm" action="<?= $config['api_url'] ?>" method="post">
            <?php foreach ($data as $k => $v) : ?>
                <input type="hidden" name="<?= $k ?>" value="<?= $v ?>">
            <?php endforeach; ?>
        </form>
        <script>document.getElementById('payForm').submit();</script>
        <?php endif; ?>



    <!-- Bootstrap 图标 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</body>
</html>