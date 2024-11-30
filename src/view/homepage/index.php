<?php
// 定义变量
$profileImage = '头像图片路径';
$username = '@Yang-b602_';
$images = ['图片1路径', '图片2路径', '图片3路径', '图片4路径', '图片5路径'];
$description = 'id即备注 生活>追星';
$details = [
    '👆 capper Mingyu Sunoo',
    '🍑 ningning Giselle 李羲承',
    '尹亘汉 南柱赫 许光汉',
    '时代峰峻234代',
    '次9同担同梦 某人每追',
    '🎸 超绝 ehp svt 团魂。女团博爱',
    '克拉忞静快来带我玩🤲'
];
$qrcodeImage = '二维码图片路径';
$location = '中国 湖北省 武汉市';
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>微信展示页面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .profile {
            margin-bottom: 20px;
        }
        .profile img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
        }
        .username {
            font-weight: bold;
            margin-top: 10px;
        }
        .images {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        .images img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .description {
            font-size: 18px;
            margin: 20px 0;
            font-weight: bold;
        }
        .details {
            text-align: left;
            margin: 0 auto;
            max-width: 300px;
        }
        .qrcode {
            margin-top: 20px;
        }
        .location {
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="profile">
        <img src="<?php echo $profileImage; ?>" alt="Profile Image">
        <div class="username"><?php echo $username; ?></div>
    </div>

    <div class="images">
        <?php foreach ($images as $image): ?>
            <img src="<?php echo $image; ?>" alt="image">
        <?php endforeach; ?>
    </div>

    <div class="description"><?php echo $description; ?></div>

    <div class="details">
        <?php foreach ($details as $detail): ?>
            <div><?php echo $detail; ?></div>
        <?php endforeach; ?>
    </div>

    <div class="qrcode">
        <img src="<?php echo $qrcodeImage; ?>" alt="QR Code">
    </div>

    <div class="location"><?php echo $location; ?></div>

</body>
</html>
