<?php

// Get language from session (default to English)
// $lang = $_SESSION['lang'] ?? 'en'; // this line is not working without global scoop in function if you need it use global $lang in function; 

// Translation messages for all APIs
$apiTranslations = [
    'en' => [
        // Login API
        'invalid_data' => 'Invalid data format',
        'invalid_credentials' => 'Invalid email or password',
        'login_successful' => 'Login successful',
        'account_disabled' => 'Your account is disabled. Please contact your manager.',

        // Registration API
        'required_fields' => 'All fields are required',
        'email_exists' => 'Email already exists',
        'pass_not_match' => 'Passwords do not match Confirm password',
        'pass_less_6' => 'Password must be at least 6 characters',
        'registration_success' => 'Registration successful',
        'registration_failed' => 'Registration failed',
        'register_per' => 'You do not have permission to register new users',
        'user_disabled_success' => 'User disabled successfully',
        'user_enabled_success' => 'User enabled successfully',
        'user_deleted_success' => 'User deleted successfully',
        'user_action_failed' => 'Failed to update user',
        'cannot_modify_self' => 'You cannot modify your own account',

        // Profile API
        'profile_updated' => 'Profile updated successfully',
        'profile_update_failed' => 'Failed to update profile',
        'invalid_password' => 'Current password is incorrect',

        // General errors
        'unauthorized' => 'Unauthorized access',
        'forbidden' => 'Access forbidden',
        'not_found' => 'Resource not found',
        'server_error' => 'Internal server error',
        'invalid_request' => 'Invalid request format'
    ],
    'ar' => [
        // Login API
        'account_disabled' => 'ط­ط³ط§ط¨ظƒ ظ…ط¬ظ…ط¯. ظٹط±ط¬ظ‰ ط§ظ„طھظˆط§طµظ„ ظ…ط¹ ط§ظ„طمدظٹط±.',
        'user_disabled_success' => 'تم تعطيل المستخدم بنجاح',
        'user_enabled_success' => 'تم تفعيل المستخدم بنجاح',
        'user_deleted_success' => 'تم حذف المستخدم بنجاح',
        'user_action_failed' => 'فشل تحديث المستخدم',
        'cannot_modify_self' => 'لا يمكنك تعديل حسابك',
        'invalid_data' => 'تنسيق البيانات غير صالح',
        'invalid_credentials' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
        'login_successful' => 'تم تسجيل الدخول بنجاح',

        // Registration API
        'required_fields' => 'الرجاء مليء جميع المدخلات',
        'pass_not_match' => 'كلمة السر لا تطابق مع تاكيد كلمة السر',
        'pass_less_6' => 'اقل حد مسموح لكلمة المرور هو 6 خانات',
        'email_exists' => 'البريد الإلكتروني موجود بالفعل',
        'registration_success' => 'تم التسجيل بنجاح',
        'registration_failed' => 'فشل التسجيل',
        'register_per' => 'ليس لديك صلاحيات لانشاء مستخدم جيد',

        // Profile API
        'profile_updated' => 'تم تحديث الملف الشخصي بنجاح',
        'profile_update_failed' => 'فشل تحديث الملف الشخصي',
        'invalid_password' => 'كلمة المرور الحالية غير صحيحة',

        // General errors
        'unauthorized' => 'وصول غير مصرح به',
        'forbidden' => 'الوصول ممنوع',
        'not_found' => 'المورد غير موجود',
        'server_error' => 'خطأ في الخادم الداخلي',
        'invalid_request' => 'تنسيق الطلب غير صالح'
    ]
];

// Helper function to get translated message
function getApiMessage($key)
{
    global $apiTranslations;
    $lang = $_SESSION['lang'] ?? 'en';
    return $apiTranslations[$lang][$key] ?? $apiTranslations['en'][$key] ?? $key;
}
?>