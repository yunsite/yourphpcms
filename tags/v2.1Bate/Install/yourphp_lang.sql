
INSERT INTO `yourphp_config` VALUES ('52', 'site_name', '网站名称', '2', 'Yourphp cms', '2');
INSERT INTO `yourphp_config` VALUES ('53', 'site_url', '网站网址', '2', 'http://www.yourphp.cn', '2');
INSERT INTO `yourphp_config` VALUES ('54', 'logo', '网站LOGO', '2', './Public/Images/logo.gif', '2');
INSERT INTO `yourphp_config` VALUES ('55', 'site_email', '站点邮箱', '2', 'admin@yourphp.cn', '2');
INSERT INTO `yourphp_config` VALUES ('56', 'seo_title', '网站标题', '2', 'yourphp cms', '2');
INSERT INTO `yourphp_config` VALUES ('57', 'seo_keywords', '关键词', '2', 'Yourphp cms', '2');
INSERT INTO `yourphp_config` VALUES ('58', 'seo_description', '网站简介', '2', 'Yourphp1', '2');

INSERT INTO `yourphp_block` VALUES ('4', 'about', 'index about', '2', 'Yourphp site management system, is a completely free open source PHP+MYSQL system. The core uses Thinkphp frame and many other open source software, at the same time the core function is also released as open source software. Set many open source projects in a characteristic, make the system from the security, efficiency, ease of use and scalability is more outstanding. Program built-in SEO optimization mechanism, make the enterprise website is easier to spread. Has the enterprise web site commonly used modules ( company profile module, news module, module, module, the picture download module, recruitment, online messages, links, membership and rights management ).');
INSERT INTO `yourphp_block` VALUES ('5', 'contact', 'contact us', '2', '<li><label>Tel:</label>0317-5022625</li> <li><label>Mobile:</label>13292793176</li> <li><label>Contact:</label>liuxun</li> <li><label>Email:</label>admin@yourphp.cn</li> <li><label>Site:</label>http://demo2.yourphp.cn</li> <li><label>Address:</label>China Hebei suning</li> ');
INSERT INTO `yourphp_block` VALUES ('6', 'footer', 'footer', '2', 'Powered by <a href=\"http://www.yourphp.cn\" target=\"_blank\">Yourphp</a>&nbsp;&nbsp;Copyright &copy; 2008-2011, All right reserved<br />');

INSERT INTO `yourphp_slide_data` VALUES ('4', '1', 'flash1', '', 'http://www.yourphp.cn/Public/Images/flash_en1.jpg', 'http://www.yourphp.cn', '', '', '0', '1', '2');
INSERT INTO `yourphp_slide_data` VALUES ('5', '1', 'flash2', '', 'http://www.yourphp.cn/Public/Images/flash_en2.jpg', 'http://www.yourphp.cn', '', '', '0', '1', '2');


INSERT INTO `yourphp_config` VALUES ('59', 'member_register', '允许新会员注册', '3', '1', '2');
INSERT INTO `yourphp_config` VALUES ('60', 'member_emailcheck', '新会员注册需要邮件验证', '3', '0', '2');
INSERT INTO `yourphp_config` VALUES ('61', 'member_registecheck', '新会员注册需要审核', '3', '1', '2');
INSERT INTO `yourphp_config` VALUES ('62', 'member_login_verify', '注册登陆开启验证码', '3', '1', '2');
INSERT INTO `yourphp_config` VALUES ('63', 'member_emailchecktpl', '邮件认证模板', '3', ' Welcome to register as yourphp user, you need to mail account authentication, click the following link authentication: {click} \ r \ n or copy the URL into your browser: {url}', '2');
INSERT INTO `yourphp_config` VALUES ('64', 'member_getpwdemaitpl', '密码找回邮件内容', '3', 'Dear user {username}, please click <a href="{url}"> Reset Password </ a>, or copy the URL into your browser: {url} (link 3 days valid). <br> Thank you for your support site. <br> 　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　{sitename} <br> This message is automatically e-mail, no reply.', '2');