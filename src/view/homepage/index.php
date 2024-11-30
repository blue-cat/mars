<?php
// 定义变量
$profileImage = '头像图片路径';
$username = '@Yang-b602_';
$images = ['图片1路径', '图片2路径', '图片3路径', '图片4路径', '图片5路径', '图片6路径'];
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
        .image-container {
            position: relative;
            width: 100px;
            height: 100px;
            background-color: #f0f0f0; /* 淡灰色背景 */
            border: 1px solid #ccc;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .images img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; /* 保持宽高比例 */
        }
        .message {
            display: none;
            position: absolute;
            color: #999;
        }
        .upload {
            position: absolute;
            right: 5px;
            bottom: 5px;
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            padding: 2px 5px;
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
        <?php foreach ($images as $index => $image): ?>
            <div class="image-container" id="image-container-<?php echo $index; ?>">
                <img src="<?php echo $image; ?>" alt="image" onerror="imageError(<?php echo $index; ?>)">
                <div class="message" id="message-<?php echo $index; ?>">请上传图片</div>
                <div class="upload" onclick="uploadImage(<?php echo $index; ?>)">上传</div>
            </div>
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

    <script>
        function uploadImage(index) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';

            input.onchange = async (event) => {
                const file = event.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                try {
                    const response = await fetch('/?s=App.Homepage_Homepage.upload', {
                        method: 'POST',
                        body: formData,
                    });

                    const data = await response.json();

                    if (data.ret === 200) {
                        const newImageURL = data.data;
                        const images = document.querySelectorAll('.images img');
                        const message = document.getElementById(`message-${index}`);
                        message.style.display = 'none';
                        images[index].src = newImageURL;
                    } else {
                        alert(data.msg);
                    }
                } catch (error) {
                    console.error('上传出错:', error);
                    alert('上传失败，请重试。');
                }
            };

            input.click();
        }

        function imageError(index) {
            const message = document.getElementById(`message-${index}`);
            message.style.display = 'block'; // 显示提示信息
        }
    </script>

</body>
</html>
