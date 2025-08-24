import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import '../css/app.css';                 // 共通 CSS

// 一般ユーザー
import '../css/auth.css';
import '../css/attendance/index.css';
import '../css/attendance/create.css';
import '../css/attendance/show.css';
import '../css/requests/index.css';

// 管理者
import '../css/admin_auth.css';
import '../css/admin/attendances/index.css';
import '../css/admin/attendances/show.css';
import '../css/admin/staffs/index.css';
import '../css/admin/staff_attendances/index.css';
import '../css/admin/stamp_correction_requests/index.css';
