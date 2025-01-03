<?php
$tokenFile = __DIR__ . '/tokens.token';
$dataFile = __DIR__ . '/counter_data.json';
if (!file_exists($tokenFile)) {
    file_put_contents($tokenFile, json_encode([]));
}
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}
$tokens = json_decode(file_get_contents($tokenFile), true);
$data = json_decode(file_get_contents($dataFile), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $authHeader = $_POST['authHeader'] ?? '';

    if ($token && $authHeader) {
        $tokenHash = hash('sha256', $token);
        $authHash = hash('sha256', $authHeader);

        if (!isset($tokens[$tokenHash])) {
            $tokens[$tokenHash] = $authHash;
            file_put_contents($tokenFile, json_encode($tokens));
            $data[$token] = 0;
            file_put_contents($dataFile, json_encode($data));
            $message = "Token 添加成功！";
        } else {
            $message = "Token 已存在！";
        }
    } else {
        $message = "Token 和授权头均为必填项！";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $deleteHash = $_GET['delete'];
    $tokenToDelete = array_search($deleteHash, array_keys($tokens));
    if (isset($tokens[$deleteHash])) {
        unset($tokens[$deleteHash]);
        unset($data[$tokenToDelete]);
        file_put_contents($tokenFile, json_encode($tokens));
        file_put_contents($dataFile, json_encode($data));
        $message = "Token 删除成功！";
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Token 管理</title>
    <link href="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-y/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-y/Chart.js/2.9.4/Chart.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <!-- 表单区域 -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">Token 管理界面</h1>
            <?php if (isset($message)): ?>
                <div class="bg-<?php echo strpos($message, '成功') !== false ? 'green' : 'red'; ?>-100 text-<?php echo strpos($message, '成功') !== false ? 'green' : 'red'; ?>-800 p-2 rounded mb-4">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST" class="space-y-4">
                <div>
                    <label for="token" class="block text-gray-700">Token</label>
                    <input type="text" id="token" name="token" class="w-full p-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="authHeader" class="block text-gray-700">授权头</label>
                    <input type="text" id="authHeader" name="authHeader" class="w-full p-2 border border-gray-300 rounded" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">添加 Token</button>
            </form>
        </div>
        <!-- Token 列表 -->
        <div class="bg-white shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-xl font-bold mb-4">Token 列表</h2>
            <?php if (empty($tokens)): ?>
                <p class="text-gray-600">暂无 Token 数据。</p>
            <?php else: ?>
                <div class="overflow-auto">
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">Token 哈希</th>
                                <th class="border border-gray-300 px-4 py-2">授权哈希</th>
                                <th class="border border-gray-300 px-4 py-2">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tokens as $tokenHash => $authHash): ?>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-xs break-all"><?php echo $tokenHash; ?></td>
                                    <td class="border border-gray-300 px-4 py-2 text-xs break-all"><?php echo $authHash; ?></td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="?delete=<?php echo $tokenHash; ?>" class="text-red-500 hover:underline">删除</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
                <div class="bg-white shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-xl font-bold mb-4">Token 调用次数统计</h2>
            <canvas id="lineChart" width="400" height="200"></canvas>
        </div>
         <script>
        // Token 授权次数折线图
        const lineLabels = <?php echo json_encode(array_keys($data)); ?>;
        const lineData = <?php echo json_encode(array_values($data)); ?>;
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: [{
                    label: '授权次数',
                    data: lineData,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { 
                        grid: { display: false }, 
                        title: { display: true, text: 'Token 名称', font: { size: 14, weight: 'bold' } }
                    },
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(200, 200, 200, 0.3)' },
                        title: { display: true, text: '调用次数', font: { size: 14, weight: 'bold' } }
                    }
                }
            }
        });
        </script>
</body>
</html>
