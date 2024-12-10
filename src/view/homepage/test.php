<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $userInfo['nickname']?? '佚名';?>的Homepage</title>
    <style>
        @font-face {
            font-family: 'LM Roman Bold';  
            src: url('/font/lmroman-bold.woff') format('woff'); 
        }
        body {
            font-family: 'LM Roman Bold', Arial, sans-serif; 
            text-align: center;
            padding: 20px;
            margin: 0;
            font-size: 12px !important;
        }
        .profile {
            margin-bottom: 20px;
        }
        .profile img {
            border-radius: 50%;
            width: 130px;
            height: 130px;
        }
        .profile-image-container {
            position: relative; 
            width: 130px; 
            height: 130px; 
            background-color: #efefef; 
            border-radius: 50%; 
            overflow: hidden; 
            display: inline-block; 
        }
        .username-container {
            display: flex; 
            justify-content: center; 
            align-items: center; 
            margin-top: 10px; 
        }
        .username-edit {
            display: flex; 
            align-items: center; 
        }
        .input-container {
            position: relative; 
        }
        #username-input {
            width: 130px; 
            border: 1px solid #efefef; 
            border-radius: 5px; 
            padding: 5px; 
            padding-right: 40px; 
            font-size: 16px; 
            margin-right: 10px; 
            font-family: 'LM Roman Bold', Arial, sans-serif; 
        }
        .username-length {
            position: absolute; 
            right: 15px; 
            top: 50%; 
            transform: translateY(-50%); 
            font-size: 16px; 
            color: #999; 
        }
        button {
            border: 1px solid #efefef; 
            border-radius: 5px; 
            padding: 2px 6px; 
            cursor: pointer; 
            font-size: 14px; 
            background-color: white; 
            color: black; 
        }
        .username-display {
            font-weight: bold; 
            font-size: 20px; 
        }
        .images {
            display: grid;
            grid-template-columns: repeat(3, 1fr); 
            gap: 1px; 
            width: 80%; 
            margin: 0 auto; 
        }
        .image-container {
            position: relative;
            width: 100%; 
            padding-top: 177.78%; 
            background-color: #efefef; 
            overflow: hidden; 
        }
        .image-container img, .image-container  .image-placeholder{
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: auto;
            min-height: 100%;
            min-width: 100%;
            transform: translate(-50%, -50%); 
            object-fit: cover; 
        }
        .upload {
            position: absolute;
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid #efefef;
            border-radius: 5px;
            cursor: pointer;
            padding: 2px 5px;
            z-index: 1; 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis; 
        }
        .content {
            display: flex; 
            margin-top: 20px; 
            width: 80%; 
            margin: 20px auto; 
        }
        .left {
            width: 50%; 
            text-align: left;
            padding-right: 5px; 
        }
        .details-edit {
            margin-bottom: 10px; 
        }
        #details-input {
            border: 1px solid #efefef; 
            border-radius: 5px; 
            padding: 5px; 
            margin-right: 5px;
            font-size: 12px; 
            width: calc(100% - 13px); 
            font-family: 'LM Roman Bold', Arial, sans-serif; 
        }
        .details-length {
            float: right; 
            font-size: 16px; 
            color: #999; 
        }
        .right {
            width: 50%; 
            text-align: center; 
            position: relative; 
        }
        .qrcode {
            position: relative;
            padding-top: 133.33%; 
            overflow: hidden; 
            margin: 0; 
            background-image: url(https://h5store.nearby.dulcim.com/img/qrcode2.jpg);
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;

            display: flex; /* 设置为 Flex 容器 */
            flex-direction: column; /* 垂直排列子元素 */
            align-items: center;
        }
        .corner {
            position: absolute;
            background-color: #ccc; 
            top: 12.5%; 
        }
        .corner.hidden {
            display: none; 
        }
        .qrcode img, .qrcode .image-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: auto;
            min-height: 100%;
            min-width: 100%;
            transform: translate(-50%, -50%); 
            object-fit: cover; 
            z-index: 1;
        }
        .button-container {
            position: absolute;
            left: 50%;
            z-index: 2; 

            display: flex; /* 使用 Flexbox 布局 */
            flex-direction: row; /* 水平排列子元素 */
            justify-content: center; /* 水平居中对齐 */
            align-items: center;

            top: 50%;
            transform: translate(-50%, -50%);
        }

        .edit-button, .delete-button {
            border: 1px solid #efefef;
            border-radius: 5px;
            padding: 2px 5px;
            cursor: pointer;
            font-size: 14px;
            background-color: white;
            color: black;

            margin: 0 5px;
        }
        .footer {
            text-align: center; 
            margin-top: 50px; 
            font-size: 12px; 
        }
        .footer a {
            text-decoration: none; 
            color: #000; 
        }
        .footer a:hover {
            text-decoration: underline; 
        }
        .footer span {
            display: inline-block; /* 确保在一行内显示 */
            vertical-align: middle; /* 可选，确保对齐 */
        }

        #url-qrcode img {
            display: inline; /* 确保二维码图片被设置为inline显示 */
            vertical-align: middle; /* 可选，对齐 */
            margin: 5px;
        }
    </style>
</head>
<body>

    <div class="profile">
        <div class="profile-image-container">
            <img src="<?php echo $userInfo['avatar']; ?>" alt="Profile Image" onerror="this.style.display='none';" style="width: 100%; height: auto; min-height: 100%; min-width: 100%; object-fit: cover;">
            <?php if ($isMe): ?>
                <div class="upload" onclick="uploadImage('avatar')">修改头像</div>
            <?php endif; ?>
        </div>
        <div class="username-container">
            <?php if ($isMe): ?>
                <div class="username-edit">
                    <div class="input-container">
                        <input type="text" id="username-input" value="<?php echo $userInfo['nickname']; ?>" maxlength="20" oninput="checkUsernameLength()" />
                        <span id="username-length" class="username-length"></span>
                    </div>
                    <button onclick="updateUserInfo(1, document.getElementById('username-input').value)">保存</button>
                </div>
            <?php else: ?>
                <span class="username-display">@<?php echo $userInfo['nickname'] ?? 'Nobody'; ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="images">
        <?php foreach ($images as $index => $image): ?>
            <div class="image-container" id="image-container-<?php echo $index; ?>">
                <?php if (!empty($image)): ?>
                    <img src="<?php echo $image; ?>" onerror="imageError(<?php echo $index; ?>)" id="img-<?php echo $index; ?>">
                <?php else: ?>
                    <div id="div-<?php echo $index; ?>" class="image-placeholder"></div>
                <?php endif; ?>
                <?php if ($isMe): ?>
                    <div class="upload" onclick="uploadImage('image', <?php echo $index; ?>)"><?php echo $image ? '修改' : '上传'; ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="content">
        <div class="left">
            <div class="details">
                <?php if ($isMe): ?>
                    <div class="details-edit">
                        <textarea id="details-input" maxlength="300" oninput="checkDetailsLength()" style="height: 160px;" placeholder="写点什么吧"><?php echo htmlspecialchars($details); ?></textarea>
                        <span id="details-length" class="details-length">0/300</span>
                        <button onclick="updateUserInfo(2, document.getElementById('details-input').value)">保存</button>
                    </div>
                <?php else: ?>
                    <div style="display: flex; justify-content: left;margin-left:5px; align-items: center; height: 160px;">
                        <?php if (empty($details)): ?>
                            <span>该用户暂无slogan</span>
                        <?php else: ?>
                            <?php echo nl2br(htmlspecialchars($details)); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="right">
            <div class="qrcode" id="qrcode-container">
                <?php if ($qrcodeImage): ?>
                    <img src="<?php echo $qrcodeImage; ?>" id="qr-code" onerror="qrCodeError()">
                    <div class="button-container">
                        <?php if ($isMe): ?>
                            <button class="delete-button" onclick="deleteQrcode()">删除</button> <!-- 添加删除按钮 -->
                            <div class="edit-button qr-upload" onclick="uploadImage('qrcode')"><?php echo $qrcodeImage ? '修改' : '上传'; ?></div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div id="div-qrcode" class="image-placeholder"></div>
                    <?php if ($isMe): ?>
                        <div class="upload qr-upload" onclick="uploadImage('qrcode')"><?php echo $qrcodeImage ? '修改' : '上传'; ?></div>
                    <?php endif; ?>
                <?php endif; ?>
                
            </div>
        </div>
    </div>

    <div class="footer">
        <a href="#" id="create-homepage-btn">登录我的Homepage</a> | 
        <a href="https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzkxMDYzMTM0NA==&scene=110#wechat_redirect
">火星殖民计划</a> |
        <a href="javascript:void(0);" id="share-image-btn">分享为图片</a>
        <br />
        <span>识别二维码生成我的Homepage<span id="url-qrcode"></span></span>
    </div>
    <script src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
    <script src="https://h5store.nearby.dulcim.com/static/html2canvas.min.js"></script>
    <script src="https://h5store.nearby.dulcim.com/static/qrcode.min.js"></script>
    <script>
        // 检查URL中是否有code和state参数
        const urlParams = new URLSearchParams(window.location.search);
        const code = urlParams.get('code');
        const state = urlParams.get('state');

        const url = '<?php echo $url; ?>';
        wx.config({
            debug: false, // 开启调试
            appId: '<?php echo $appid; ?>', // 企业号的唯一标识
            timestamp: <?php echo $timestamp; ?>, // 生成签名的时间戳
            nonceStr: '<?php echo $nonceStr; ?>', // 生成签名的随机串
            signature: '<?php echo $signature; ?>', // 签名
            jsApiList: ['updateAppMessageShareData', 'updateTimelineShareData'] // 必填，需要使用的 JS 接口
        });

        document.addEventListener('DOMContentLoaded', function() {
            const title = '<?php echo $userInfo["nickname"]; ?>的Homepage';
            const imageUrl = '<?php echo $userInfo["avatar"]; ?>'; // 用户头像
            const description = '<?php echo $details ? str_replace(["\n", "\r", "\t"], ' ', htmlspecialchars($details)) : "暂无slogan"; ?>';// 用户描述

            wx.ready(function () {
                // 分享到朋友圈
                wx.updateTimelineShareData({
                    title: title,
                    link: window.location.href, // 当前页面链接
                    imgUrl: imageUrl // 分享时的图标
                });
            });
            wx.ready(function () {
                // 分享给朋友
                wx.updateAppMessageShareData({
                    title: title,
                    desc: description,
                    link: window.location.href, // 当前页面链接
                    imgUrl: imageUrl
                });
            });
        });

        if (code && state) {
            fetch(`/?s=App.User_User.Code2UserInfo&code=${code}&scene=1&state=${state}`)
                .then(response => response.json())
                .then(data => {
                    if (data.ret === 200) {
                        const { user_str, user_id, token, profile } = data.data;

                        document.cookie = `token=${token}; expires=${new Date(Date.now() +  24 * 60 * 60 * 1000).toUTCString()}`;
                        document.cookie = `user_id=${user_id}; expires=${new Date(Date.now() +  24 * 60 * 60 * 1000).toUTCString()}`;
                        
                        localStorage.setItem('token', token);
                        localStorage.setItem('user_id', user_id);
                        localStorage.setItem('user_str', user_str);
                        localStorage.setItem('profile', JSON.stringify(profile));

                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('id', user_str);
                        currentUrl.searchParams.delete('code');
                        currentUrl.searchParams.delete('state');
                        window.location.href = currentUrl.href;
                    } else {
                        alert(data.msg);
                    }
                })
                .catch(error => {
                    console.error('请求失败:', error);
                    alert('请求失败，请重试。');
                });
        }

            document.getElementById('create-homepage-btn').onclick = function() {
                var selfId = '<?php echo $selfId; ?>';

                if (selfId) {
                    // 如果$selfId不为空，将url中的id参数设置为$selfId，并跳转
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('id', selfId);
                    const currentUrl = window.location.origin + window.location.pathname + '?' + urlParams.toString();
                    window.location.href = currentUrl;
                } else {
                    // 如果为空，进行微信授权跳转
                    const state = Math.random().toString(36).substring(7);
                    const redirectUri = encodeURIComponent(window.location.href);
                    const scope = 'snsapi_userinfo';

                    const authUrl = `https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?php echo $appid; ?>&redirect_uri=${redirectUri}&response_type=code&scope=${scope}&state=${state}#wechat_redirect`;
                    window.location.href = authUrl;
                }
            };

        document.addEventListener('DOMContentLoaded', function() {
            var selfId = '<?php echo $selfId; ?>';

            // 检查$selfId，如果为0，则触发点击事件。或者url中没有id的参数
            if ((selfId == "" && urlParams.get('id') == null && !state) 
                || (selfId != "" && urlParams.get('id') == null)){
                document.getElementById('create-homepage-btn').click();
            }
        });

        function compressImage(file, maxWidth, callback) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const img = new Image();
                img.src = event.target.result;
                img.onload = function () {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    const scaleFactor = maxWidth / img.width; 
                    canvas.width = maxWidth; 
                    canvas.height = img.height * scaleFactor; 
                    
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob(function (blob) {
                        callback(blob); 
                    }, 'image/jpeg', 0.9); 
                };
            };
            reader.readAsDataURL(file);
        }

        // 向上传函数传递不同的类型
        function uploadImage(type, index = 0) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.style.display = 'none';

            document.body.appendChild(input);

            input.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (!file) {
                    document.body.removeChild(input); // 移除输入框
                    return;
                }

                compressImage(file, 250, function (compressedBlob) {
                    const formData = new FormData();
                    formData.append('file', compressedBlob, file.name);
                    formData.append('index', index);
                    formData.append('type', type === 'avatar' ? 3 : (type === 'qrcode' ? 2 : 1)); // 根据类型设定type值

                    const uploadButtonSelector = type === 'avatar' ? '.profile-image-container .upload' : (type === 'qrcode' ? '.qrcode .qr-upload' : `#image-container-${index} .upload`);
                    const uploadButton = document.querySelector(uploadButtonSelector);
                    uploadButton.textContent = '上传中';

                    fetch('/?s=App.Homepage_Homepage.upload', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.ret === 200) {
                            const newURL = data.data;
                            if (type === 'avatar') {
                                const imgElement = document.querySelector('.profile-image-container img');
                                imgElement.src = newURL;
                            } else if (type === 'qrcode') {
                                const imgElement = document.getElementById('qr-code');
                                if (imgElement) {
                                    imgElement.src = newURL; // 如果存在，更换 src
                                } else {
                                    // 刷新页面
                                    window.location.reload();
                                    return;
                                    // 替换占位符为新的 img 标签
                                    const placeholder = document.getElementById('div-qrcode');
                                    const newImgElement = document.createElement('img');
                                    newImgElement.id = 'qr-code';
                                    newImgElement.src = newURL; // 设置新图源

                                    // 添加 onerror 事件
                                    newImgElement.setAttribute('onerror', 'qrCodeError()');

                                    placeholder.parentElement.replaceChild(newImgElement, placeholder); // 替换占位符
                                }
                            } else {
                                const elementId = `img-${index}`;
                                let imgElement = document.getElementById(elementId);

                                if (!imgElement) {
                                    // 获取包含 div 的父元素
                                    const parentElement = document.getElementById(`div-${index}`); // 假设 div 的 id 是 div-索引
                                    if (parentElement) {
                                        // 创建新的 img 标签
                                        imgElement = document.createElement('img');
                                        imgElement.id = elementId;
                                        imgElement.src = newURL; // 设置图片源
                                        imgElement.setAttribute('onerror', `imageError(${index})`); // 设置 onerror 属性
                                        // 替换 div 元素为 img
                                        parentElement.parentElement.replaceChild(imgElement, parentElement);
                                    }
                                } else {
                                    imgElement.src = newURL; // 如果已存在，更新 src
                                }
                            }
                            uploadButton.textContent = type === 'avatar' ? '修改头像' : (type === 'qrcode' ? '修改' : '修改');
                        } else {
                            alert(data.msg);
                            uploadButton.textContent = type === 'avatar' ? '修改头像' : (type === 'qrcode' ? '上传' : '上传');
                        }
                    })
                    .catch(error => {
                        console.error('上传出错:', error);
                        alert('上传失败，请重试。');
                        uploadButton.textContent = type === 'avatar' ? '修改头像' : (type === 'qrcode' ? '上传' : '上传');
                    });
                });
            });

            input.click();
        }

        function imageError(index) {
            const imgElement = document.getElementById(`img-${index}`);
            const uploadButton = document.querySelector(`#image-container-${index} .upload`);
            
            imgElement.style.display = 'none'; 
            uploadButton.textContent = '上传'; 
        }

        function qrCodeError() {
            const imgElement = document.getElementById('qr-code');
            imgElement.style.display = 'none'; 

            const corners = document.querySelectorAll('.qrcode .corner');
            corners.forEach(corner => {
                corner.classList.remove('hidden'); 
            });

            const qrCodeContainer = document.getElementById('qrcode-container');
            qrCodeContainer.style.backgroundImage = 'radial-gradient(circle, #ccc 5%, transparent 5%), radial-gradient(circle, transparent 5%, #ccc 5%, #ccc 10%, transparent 10%, transparent)';

            const uploadButton = document.querySelector('.qrcode .qr-upload');
            if (uploadButton) {
                uploadButton.textContent = '上传'; 
            }
        }

        function updateUserInfo(type, content) {
            const formData = new FormData();
            formData.append('type', type);
            formData.append('content', content); 

            fetch('/?s=App.Homepage_Homepage.updateUserinfo', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.ret === 200) {
                    alert('修改成功！'); 
                    location.reload();
                } else {
                    alert(data.msg); 
                }
            })
            .catch(error => {
                console.error('请求失败:', error);
                alert('请求失败，请重试。');
            });
        }

        function checkUsernameLength() {
            const input = document.getElementById('username-input');
            if (!input) return;
            const maxLength = 20;
            const currentLength = input.value.length; 

            document.getElementById('username-length').innerText = `${currentLength}/20`;

            const saveButton = document.querySelector('.username-edit button');
            saveButton.disabled = currentLength > maxLength; 
        }

        document.addEventListener("DOMContentLoaded", function() {
            checkUsernameLength(); 
        });

        function checkDetailsLength() {
            const input = document.getElementById('details-input');
            if (!input) return;  // 若 input 元素不存在，返回
            const maxLength = 300;
            const currentLength = input.value.length;

            document.getElementById('details-length').innerText = `${currentLength}/300`;

            const saveButton = document.querySelector('.details-edit button');
            saveButton.disabled = currentLength > maxLength; 
        }

        document.addEventListener("DOMContentLoaded", function() {
            checkDetailsLength(); 
        });

        function deleteQrcode() {
            // 提示用户确认删除
            if (confirm('您确定要删除二维码吗？')) {
                // 发送删除请求
                fetch('/?s=App.Homepage_Homepage.deleteQrcode', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.ret === 200) {
                        alert('删除成功！');
                        location.reload(); // 刷新页面
                    } else {
                        alert(data.msg); // 提示错误信息
                    }
                })
                .catch(error => {
                    console.error('删除请求失败:', error);
                    alert('删除请求失败，请重试。');
                });
            }
        }

        document.getElementById('share-image-btn').onclick = function() {
            const isMe = <?php echo json_encode($isMe); ?>; // 获取 isMe 的值

            if (isMe) {
                // 获取当前 URL
                const url = new URL(window.location.href);
                // 添加 preview 参数
                url.searchParams.set('preview', 'true');
                
                // 跳转到新的 URL
                window.location.href = url.toString(); // 跳转到带有 preview 参数的 URL
            } else {
                // 如果不是自己，则直接生成分享图片
                generateShareImage();
            }
        };

        window.onload = function() {

            // 创建二维码容器
            const qrCodeContainer = document.getElementById('url-qrcode');

            // 清空原有内容
            qrCodeContainer.innerHTML = '';

            // 创建二维码
            const qrCode = new QRCode(qrCodeContainer, {
                text: window.location.href,
                width: 65,
                height: 65,
            });

            const url = new URL(window.location.href);
    
            // 检查是否包含 preview 参数
            if (url.searchParams.has('preview')) {
                // 生成分享图片
                generateShareImage();

                // 移除 preview 参数，并更新 URL
                url.searchParams.delete('preview'); // 删除 preview 参数
                window.history.replaceState({}, '', url.toString()); // 更新 URL
            }
        };

        // 定义生成分享图片的函数
        function generateShareImage() {
            html2canvas(document.body, { useCORS: true, scale: 2 }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.href = imgData;
                link.download = 'homepage.png';
                link.click();
            }).catch(error => {
                console.error('生成图片失败:', error);
            });
        }
    </script>
</body>
</html>
