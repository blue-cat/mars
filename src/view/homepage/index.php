<?php
// 定义变量
$profileImage = '头像图片路径';
$username = '@Yang-b602_';
$images = ['图片1路径', '图片2路径', '图片3路径', '图片4路径', '图片5路径', '图片6路径']; // 确保最多为六张图片
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
            margin: 0;
            font-size: small;
        }
        .profile {
            margin-bottom: 20px;
        }
        .profile img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
        }
        .profile-image-container {
            width: 150px; /* 固定宽度 */
            height: 150px; /* 固定高度 */
            background-color: #f0f0f0; /* 灰色背景 */
            border-radius: 50%; /* 圆形边角 */
            overflow: hidden; /* 隐藏溢出部分 */
            display: inline-block; /* 使元素保持行内块级特性 */
        }
        .username {
            font-weight: bold;
            margin-top: 10px;
        }
        .images {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 三列布局 */
            gap: 1px; /* 图片间距为1px */
            width: 80%; /* 设置宽度为80%以便占据屏幕的0.8 */
            margin: 0 auto; /* 居中 */
        }
        .image-container {
            position: relative;
            width: 100%; /* 占满整个格子 */
            padding-top: 177.78%; /* 9:16的比例 */
            background-color: #f0f0f0; /* 淡灰色背景 */
            overflow: hidden; /* 隐藏溢出部分 */
        }
        .image-container img {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: auto;
            min-height: 100%;
            min-width: 100%;
            transform: translate(-50%, -50%); /* 居中对齐 */
            object-fit: cover; /* 保持宽高比，裁剪多余部分 */
        }
        .upload {
            position: absolute;
            top: 50%; /* 垂直居中 */
            left: 50%; /* 水平居中 */
            transform: translate(-50%, -50%); /* 让上传按钮居中 */
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            padding: 2px 5px;
            z-index: 1; /* 确保在其他元素之上 */
        }
        .content {
            display: flex; /* 水平排列左右部分 */
            margin-top: 20px; /* 上部留空 */
            width: 80%; /* 总宽度80% */
            margin: 20px auto; /* 中心对齐 */
        }
        .left {
            width: 50%; /* 描述和细节部分宽度 */
            text-align: left;
            padding-right: 5px; /* 右侧留空间 */
        }
        .right {
            width: 50%; /* QR码部分宽度 */
            text-align: center; /* QR码内容居中 */
            position: relative; /* 使二维码和上传按钮相对定位 */
        }
        .qrcode {
    position: relative;
    padding-top: 133.33%; /* 固定高度与宽度之比为3:4 */
    background-color: #f0f0f0; /* 灰色背景 */
    overflow: hidden; /* 隐藏溢出部分 */
    margin: 0; /* 居中对齐 */
    
    /* 更改背景样式以模拟二维码 */
    background-image: 
        linear-gradient(90deg, #ccc 25%, transparent 25%, transparent 75%, #ccc 75%, #ccc),
        linear-gradient(transparent, transparent),
        linear-gradient(0deg, #ccc 25%, transparent 25%, transparent 75%, #ccc 75%, #ccc),
        linear-gradient(0, #ccc, #ccc);
        
    /* 添加方块的大小和位置 */
    background-size: 20px 20px; /* 方块的大小 */
}

.qrcode .corner {
    position: absolute;
    background-color: #ccc; /* 浅灰色 */
}

/* 定义左上角的块 */
.qrcode .corner-tl {
    width: 40px; /* 左上角块的宽度 */
    height: 40px; /* 左上角块的高度 */
    top: 0;
    left: 0;
}

/* 定义右上角的块 */
.qrcode .corner-tr {
    width: 40px; /* 右上角块的宽度 */
    height: 40px; /* 右上角块的高度 */
    top: 0;
    right: 0;
}

/* 定义左下角的块 */
.qrcode .corner-bl {
    width: 40px; /* 左下角块的宽度 */
    height: 40px; /* 左下角块的高度 */
    bottom: 55%;
    left: 0;
}



        .qrcode img {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: auto;
            min-height: 100%;
            min-width: 100%;
            transform: translate(-50%, -50%); /* 居中对齐 */
            object-fit: cover; /* 保持真实比例 */
        }
        .qrcode .upload {
            position: absolute; /* 绝对定位 */
            top: 50%; /* 垂直居中 */
            left: 50%; /* 水平居中 */
            transform: translate(-50%, -50%); /* 使其居中 */
            background-color: rgba(255, 255, 255, 0.7); /* 背景色，保持原样 */
            border: 1px solid #ccc; /* 边框，保持原样 */
            border-radius: 5px; /* 圆角，保持原样 */
            cursor: pointer; /* 鼠标指针 */
            padding: 2px 5px; /* 内边距，保持原样 */
            z-index: 1; /* 确保在图片之上 */
        }

    </style>
</head>
<body>

    <div class="profile">
        <div class="profile-image-container">
            <img src="<?php echo $profileImage; ?>" alt="Profile Image" onerror="this.style.display='none';" style="width: 100%; height: auto; min-height: 100%; min-width: 100%; object-fit: cover;">
        </div>
        <div class="username"><?php echo $username; ?></div>
    </div>

    <div class="images">
        <?php foreach ($images as $index => $image): ?>
            <div class="image-container" id="image-container-<?php echo $index; ?>">
                <img src="<?php echo $image; ?>" alt="image" onerror="imageError(<?php echo $index; ?>)" id="img-<?php echo $index; ?>">
                <div class="upload" onclick="uploadImage(<?php echo $index; ?>)"><?php echo $image ? '修改' : '上传'; ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="content">
        <div class="left">
            <div class="description"><?php echo $description; ?></div>
            <div class="details">
                <?php foreach ($details as $detail): ?>
                    <div><?php echo $detail; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="right">
            <div class="qrcode">
                <img src="<?php echo $qrcodeImage; ?>" alt="QR Code" id="qr-code" onerror="qrCodeError()">
                <div class="corner corner-tl"></div> <!-- 左上角 -->
                <div class="corner corner-tr"></div> <!-- 右上角 -->
                <div class="corner corner-bl"></div> <!-- 左下角 -->
                <div class="upload" onclick="uploadQRCode()"><?php echo $qrcodeImage ? '修改' : '上传'; ?></div>
            </div>
        </div>
    </div>

    <script>
        function uploadQRCode() {
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
                        const newQRCodeURL = data.data;
                        const imgElement = document.getElementById('qr-code');
                        imgElement.src = newQRCodeURL; // 更新二维码地址

                        // 显示新上传的二维码图片
                        imgElement.style.display = 'block'; // 确保图片可见
                        imgElement.onerror = null; // 清除之前的错误处理

                        // 更新上传按钮文本为“修改”
                        const uploadButton = document.querySelector('.qrcode .upload');
                        uploadButton.textContent = '修改'; // 将按钮文字更改为“修改”
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
                        const imgElement = document.getElementById(`img-${index}`);
                        
                        imgElement.src = newImageURL; // 更新图片地址
                        imgElement.style.display = 'block'; // 显示图片

                        // 获取上传按钮并将其文字更改为“修改”
                        const uploadButton = document.querySelector(`#image-container-${index} .upload`);
                        uploadButton.textContent = '修改'; // 修改按钮文字
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
            const imgElement = document.getElementById(`img-${index}`);
            const uploadButton = document.querySelector(`#image-container-${index} .upload`);
            
            imgElement.style.display = 'none'; // 不显示破损的图片
            uploadButton.textContent = '上传'; // 将按钮文字更改为“上传”
        }

        function qrCodeError() {
            const imgElement = document.getElementById('qr-code');
            imgElement.style.display = 'none'; // 隐藏破损的二维码图片

            // 修改上传按钮文本为“上传”
            const uploadButton = document.querySelector('.qrcode .upload');
            uploadButton.textContent = '上传'; // 将按钮文字更改为“上传”
            
            // 显示二维码区域的背景图案
            const qrCodeContainer = document.querySelector('.qrcode');
            qrCodeContainer.style.backgroundImage = 'radial-gradient(circle, #ccc 5%, transparent 5%), radial-gradient(circle, transparent 5%, #ccc 5%, #ccc 10%, transparent 10%, transparent)';
        }

    </script>

</body>
</html>
