const translations = {
  en: {
    app: "DocuFlow",
    // Login Page
    LoginBoxHead: "Login",
    LoginEmailLabel: "Email address",
    LoginEmailHint: "We'll never share your email with anyone else.",
    LoginPasswordLabel: "Password",
    BrandLetter: "D",
    BrandName: "ocFlow",
    EmailPlaceholder: "Example@gmail.com",
    PasswordPlaceholder: "Password (min 6 characters)",
    IReadAndAccept: "I read and accept",
    ResetPassword: "reset password",
    SubmitButton: "Submit",
    PolicyTitle: "Privacy Policy",
    PolicyText:
      "Our Privacy Commitment\n\nAt DocuFlow, we are committed to protecting your privacy and personal data with the highest security standards.\n\n What We Collect:\n• Personal information necessary for document management\n• Activity and registration information\n• Data used to improve our services\n\n How We Protect Your Data:\n• We never share your data with third parties\n• We use advanced security measures\n• We comply with applicable laws and regulations\n\n Your Rights:\n• Full access to your data\n• Request corrections or deletions\n• Withdraw consent at any time\n\nBy using our services, you agree to these terms.",
    CloseButton: "close",
    AcceptButton: "accept",

    // Error messages
    typeValidEmail: "Type A Valid Email",
    passwordRequirements: "Password contein A-Z Capitel a-z Small 0-9 and Spiacel Character between 6 and 35",
    emailHelpText: "We'll never share your email with anyone else.",

    dashboard: "Dashboard",
    documents: "Documents",
    upload: "Upload",
    addUser: "Add User",
    activity: "Activity",
    chat: "AI Chat",
    manageUsers: "Manage Users",
    profile: "My Profile",
    logout: "Logout",
    search: "Search documents...",
    newUpload: "New Upload",
    totalDocs: "Total Documents",
    department: "Department",
    myDoc: "My Documents",
    users: "Active Users",
    recentDocs: "Recent Documents",
    title: "Title",
    date: "Date",
    activityLog: "Activity Log",
    manageUsersHint: "Manage users created under your account",
    userName: "Name",
    userEmail: "Email",
    userRole: "Role",
    userStatus: "Status",
    statusActive: "Active",
    statusInactive: "Inactive",
    statusDisabled: "Disabled",
    enable: "Enable",
    disable: "Disable",
    deleteUser: "Delete",
    confirmDelete: "Are you sure you want to delete this user?",
    allStatuses: "All statuses",
    actionSuccess: "Action completed successfully",
    actionFailed: "Action failed. Please try again.",
    visibility: "Visibility",
    noRecentDocs: "No recent documents",
    noActivity: "No recent activity",
    statusOverview: "Status Overview",
    lastUpdated: "Last updated",
    refresh: "Refresh",
    dashboardHint: "Stats are refreshed from the database and reflect your group access.",
    logUpload: "📄 Uploaded document",
    logDelete: "🗑 Deleted document",
    logLogin: "🔐 Logged in",
    personalInfo: "Personal Information",
    documentInfo: "Document Information",
    images: "Document Images",
    btnBack: "Back",
    labelName: "Customer Name (Required)",
    placeholderlabelName: "Ahmed Mohamed Ali",
    labelNN: "National Number (Required)",
    labelPhone: "Phone Number (Required)",
    labelPassport: "Passport Number (Required)",
    labelContact: "Contact (Required)",
    contactplaceholder: "Name or Phone Number",
    labelPrice: "Price (Required)",
    Priceplaceholder: "Example 1500LYD",
    labelStatus: "Status (Optional)",
    labelIbm: "IBAN (Optional)",
    labelNotes: "Notes (Optional)",
    labelPassportImage: "Passport Image (Required)",
    labelNNImage: "National Number Image (Required)",
    moreInfo: "More Information",

    statusNew: "New",
    statusWaiting: "Waiting Reservation",
    statusOn: "On Reservation",
    statusEnough: "Enough",
    statusPendingDelivery: "Pending Delivery",
    statusDelivered: "Delivered",

    reset: "Reset",
    submit: "Send",
    // list page mode update
    update: "update",
    // table page
    documentId: "Id",
    documentName: "Name",
    documentNN: "National Number",
    documentPn: "Passport Number",
    documentPhone: "Phone Number",
    documentCreated: "Date",
    actions: "Actions",
    showing: "Showing",
    advancedSearch: "Advanced Search",
    quickSearch: "Quick Search",
    advancedOptions: "Advanced",
    showAdvanced: "Show Advanced",
    hideAdvanced: "Hide Advanced",
    searchField: "Search Field",
    searchAll: "All Fields",
    matchMode: "Match",
    matchContains: "Contains",
    matchStarts: "Starts With",
    matchEnds: "Ends With",
    matchExact: "Exact",
    dateFrom: "Date From",
    timeFrom: "Time From",
    dateTo: "Date To",
    timeTo: "Time To",
    groupByDay: "Group By Day",
    selectAllPage: "Select all on page",
    selectedLabel: "selected",
    bulkDelete: "Delete Selected",
    bulkExport: "Send to Automation",
    itemsPerPage: "Items per page",
    all: "All",
    noSearchResults: "No matching documents found",
    noDocumentsSelected: "No documents selected",
    confirmDeleteSelected: "Delete selected documents?",
    automationSuccess: "Sent to automation",
    automationFailed: "Automation failed",

    // Profile page translations
    editProfile: "Edit Profile",
    viewMode: "View Mode",
    fullName: "Full Name",
    email: "Email Address",
    role: "Role",
    joinedDate: "Joined Date",
    changePassword: "Change Password",
    editPassword: "Edit Password",
    currentPassword: "Current Password",
    newPassword: "New Password",
    confirmPassword: "Confirm Password",
    saveChanges: "Save Changes",
    cancel: "Cancel",
    
    // User Registration Page
    createAccount: "Create Account",
    fullName: "Full Name",
    emailAddress: "Email Address",
    password: "Password",
    confirmPassword: "Confirm Password",
    roleOption: "Role Option",
    groupManager: "Group Manager",
    merchant: "Merchant",
    employee: "Employee",
    fullNamePlaceholder: "Enter your full name",
    emailPlaceholder: "Example@gmail.com",
    passwordPlaceholder: "Password (min 6 characters)",
    confirmPasswordPlaceholder: "Confirm your password",
    emailHelp: "We'll never share your email with anyone else.",
    
    // Toggle Mode Button
    editMode: "Edit Mode",
    viewMode: "View Mode",
    
    // Activity Page
    activityLog: "Activity Log",
    filterByAction: "Filter by Action",
    allActions: "All Actions",
    login: "Login",
    logout: "Logout",
    upload: "Upload",
    edit: "Edit",
    delete: "Delete",
    create: "Create",
    create_document: "Upload Document",
    update_document: "Update Document",
    delete_document: "Delete Document",
    create_user: "Create User",
    filterByUser: "Filter by User",
    searchUser: "Search user...",
    filterByDate: "Filter by Date",
    applyFilters: "Apply",
    searchActivities: "Search activities...",
    search: "Search",
    totalActivities: "Total Activities",
    todayActivities: "Today's Activities",
    activeUsers: "Active Users",
    previous: "Previous",
    next: "Next",
    
    // Additional Information Section
    additionalInfo: "Additional Information",
    uploadDate: "Upload Date",
    uploadedBy: "Uploaded By",
    activityHistory: "Activity History",
    documentCreated: "Document Created",
    dataUpdated: "Data Updated",
    documentUpdated: "Document Updated",
    
    // Chat Page
    aiAssistant: "AI Assistant",
    chatDescription: "Ask me anything about documents, users, or system operations",
    online: "Online",
    newChat: "New Chat",
    clearChat: "Clear Chat",
    welcomeTitle: "Welcome to DocuFlow AI Assistant!",
    welcomeMessage: "I can help you with:",
    helpCreateDoc: "Creating new documents",
    helpSearchDoc: "Searching for documents",
    helpUpdateDoc: "Updating existing documents",
    helpUsers: "Managing users",
    helpReports: "Generating reports",
    startChat: "Just type your question below or upload a file to get started!",
    messagePlaceholder: "Type your message here...",
    send: "Send",
    aiTyping: "AI is typing...",
  },
  ar: {
    app: "DocuFlow",
    visibility: "الظهور",
    noRecentDocs: "لا توجد مستندات حديثة",
    noActivity: "لا يوجد نشاط حديث",
    statusOverview: "نظرة عامة على الحالات",
    lastUpdated: "آخر تحديث",
    refresh: "تحديث",
    dashboardHint: "يتم تحديث الإحصائيات من قاعدة البيانات وتعكس صلاحيات مجموعتك.",
    manageUsers: "إدارة المستخدمين",
    manageUsersHint: "إدارة المستخدمين الذين تم إنشاؤهم تحت حسابك",
    userName: "الاسم",
    userEmail: "البريد الإلكتروني",
    userRole: "الدور",
    userStatus: "الحالة",
    statusActive: "نشط",
    statusInactive: "غير نشط",
    statusDisabled: "مُعطّل",
    enable: "تفعيل",
    disable: "تعطيل",
    deleteUser: "حذف",
    confirmDelete: "هل أنت متأكد أنك تريد حذف هذا المستخدم؟",
    allStatuses: "كل الحالات",
    actionSuccess: "تمت العملية بنجاح",
    actionFailed: "فشلت العملية. حاول مرة أخرى.",
    // Login Page
    LoginBoxHead: "تسجيل الدخول",
    LoginEmailLabel: "الايميل",
    LoginEmailHint: "نحن لا نشارك  الايميل الخاص بك مع طرف ثالث",
    LoginPasswordLabel: "كلمة السر",
    BrandLetter: "D",
    BrandName: "ocFlow",
    EmailPlaceholder: "Example@gmail.com",
    PasswordPlaceholder: "كلمة السر (6 أحرف على الأقل)",
    IReadAndAccept: "أقر وأوافق",
    ResetPassword: "استعادة كلمة المرور",
    SubmitButton: "إرسال",
    PolicyTitle: "سياسة الخصوصية",
    PolicyText:
      " التزامنا بالخصوصية\n\nنحن في DocuFlow نلتزم بحماية خصوصيتك وبياناتك الشخصية بأعلى معايير الأمان.\n\n ما نجمعه من بيانات:\n• البيانات الشخصية اللازمة لإدارة المستندات\n• معلومات النشاطات والتسجيل\n• البيانات المستخدمة لتحسين الخدمات\n\n كيف نحمي بياناتك:\n• لا نشارك بياناتك مع أطراف ثالثة\n• نستخدم تدابير أمنية متقدمة\n• نلتزم بالقوانين واللوائح المعمول بها\n\n حقوقك:\n• الوصول الكامل لبياناتك\n• طلب تصحيح أو حذف المعلومات\n• سحب الموافقة في أي وقت\n\nباستخدامك لخدماتنا، أنت توافق على هذه الشروط.",
    CloseButton: "إغلاق",
    AcceptButton: "موافق",

    // Error messages
    typeValidEmail: "أدخل بريداً إلكترونياً صحيحاً",
    passwordRequirements: "كلمة المرور يجب أن تحتوي على A-Z a-z 0-9 ورمز خاص بين 6 و 35",
    emailHelpText: "نحن لا نشارك  الايميل الخاص بك مع طرف ثالث",

    dashboard: "لوحة التحكم",
    documents: "المستندات",
    upload: "رفع ملف",
    addUser: "إضافة مستخدم",
    activity: "النشاطات",
    profile: "حسابي",
    logout: "تسجيل الخروج",
    search: "ابحث عن المستندات...",
    newUpload: "رفع جديد",
    totalDocs: "إجمالي المستندات",
    department: "القسم",
    myDoc: "مستنداتي",
    users: "المستخدمون النشطون",
    recentDocs: "أحدث المستندات",
    title: "العنوان",
    date: "التاريخ",
    activityLog: "سجل النشاط",
    logUpload: "📄 تم رفع مستند",
    logDelete: "🗑 تم حذف مستند",
    logLogin: "🔐 تم تسجيل الدخول",
    personalInfo: "بيانات العميل",
    personalInfo: "البيانات الشخصية",
    documentInfo: "بيانات المستند",
    images: "صور المستندات",
    btnBack: "رجوع",
    labelName: "اسم العميل (اجباري)",
    placeholderlabelName: "احمد محمد علي",
    labelNN: "الرقم الوطني (اجباري)",
    labelPhone: "رقم الهاتف (اجباري)",
    labelPassport: "رقم الجواز (اجباري)",
    labelContact: "وسيلة التواصل (اجباري)",
    contactplaceholder: "الاسم او رقم الهاتف",
    labelPrice: "السعر (اجباري)",
    Priceplaceholder: "مثلا 1500د.ل",
    labelStatus: "الحالة (اختياري)",
    labelIbm: "رقم الآيبان (اختياري)",
    labelNotes: "ملاحظات (اختياري)",
    labelPassportImage: "صورة الجواز (اجباري)",
    labelNNImage: "صورة الرقم الوطني (اجباري)",
    moreInfo: "معلومات اضافية",

    statusNew: "جديد",
    statusWaiting: "بانتظار الحجز",
    statusOn: "قيد الحجز",
    statusEnough: "مكتمل",
    statusPendingDelivery: "انتظار التسليم",
    statusDelivered: "تم التسليم",

    reset: "إعادة تعيين",
    submit: "إرسال",
    // list page mod update
    update: "تحديث",

    // Table page
    documentId: "Id",
    documentName: "الاسم",
    documentNN: "الرقم الوطني",
    documentPn: "رقم الجواز",
    documentPhone: "رقم الهاتف",
    documentCreated: "التاريخ",
    actions: "Actions",
    showing: "عرض",
    advancedSearch: "بحث متقدم",
    quickSearch: "بحث سريع",
    advancedOptions: "متقدم",
    showAdvanced: "إظهار المتقدم",
    hideAdvanced: "إخفاء المتقدم",
    searchField: "حقل البحث",
    searchAll: "كل الحقول",
    matchMode: "طريقة المطابقة",
    matchContains: "يحتوي",
    matchStarts: "يبدأ بـ",
    matchEnds: "ينتهي بـ",
    matchExact: "مطابقة تامة",
    dateFrom: "من تاريخ",
    timeFrom: "من وقت",
    dateTo: "إلى تاريخ",
    timeTo: "إلى وقت",
    groupByDay: "تجميع حسب اليوم",
    selectAllPage: "تحديد الكل في الصفحة",
    selectedLabel: "محدد",
    bulkDelete: "حذف المحدد",
    bulkExport: "إرسال للأتمتة",
    itemsPerPage: "عدد العناصر",
    all: "الكل",
    noSearchResults: "لا توجد مستندات مطابقة للبحث",
    noDocumentsSelected: "لم يتم تحديد مستندات",
    confirmDeleteSelected: "هل تريد حذف المستندات المحددة؟",
    automationSuccess: "تم الإرسال للأتمتة",
    automationFailed: "فشل الإرسال للأتمتة",

    // Profile page translations
    editProfile: "تعديل الملف الشخصي",
    viewMode: "عرض فقط",
    fullName: "الاسم الكامل",
    email: "البريد الإلكتروني",
    role: "المنصب",
    joinedDate: "تاريخ الانضمام",
    changePassword: "تغيير كلمة المرور",
    editPassword: "تعديل كلمة المرور",
    currentPassword: "كلمة المرور الحالية",
    newPassword: "كلمة المرور الجديدة",
    confirmPassword: "تأكيد كلمة المرور",
    saveChanges: "حفظ التغييرات",
    cancel: "إلغاء",
    
    // User Registration Page
    createAccount: "إنشاء حساب",
    fullName: "الاسم الكامل",
    emailAddress: "البريد الإلكتروني",
    password: "كلمة المرور",
    confirmPassword: "تأكيد كلمة المرور",
    roleOption: "خيار الدور",
    groupManager: "مدير المجموعة",
    merchant: "تاجر",
    employee: "موظف",
    fullNamePlaceholder: "أدخل اسمك الكامل",
    emailPlaceholder: "Example@gmail.com",
    passwordPlaceholder: "كلمة المرور (6 أحرف على الأقل)",
    confirmPasswordPlaceholder: "تأكيد كلمة المرور",
    emailHelp: "نحن لا نشارك بريدك الإلكتروني مع أي شخص آخر.",
    
    // Toggle Mode Button
    editMode: "وضع التعديل",
    viewMode: "وضع العرض",
    
    // Activity Page
    activityLog: "سجل النشاط",
    filterByAction: "فلترة حسب النوع",
    allActions: "كل الأنواع",
    login: "تسجيل الدخول",
    logout: "تسجيل الخروج",
    upload: "رفع ملف",
    edit: "تعديل",
    delete: "حذف",
    create: "إنشاء",
    create_document: "رفع مستند",
    update_document: "تحديث مستند",
    delete_document: "حذف مستند",
    create_user: "إنشاء مستخدم",
    filterByUser: "فلترة حسب المستخدم",
    searchUser: "البحث عن مستخدم...",
    filterByDate: "فلترة حسب التاريخ",
    applyFilters: "تطبيق",
    searchActivities: "البحث في النشاطات...",
    search: "بحث",
    totalActivities: "إجمالي النشاطات",
    todayActivities: "نشاطات اليوم",
    activeUsers: "المستخدمون النشطون",
    previous: "السابق",
    next: "التالي",
    
    // Additional Information Section
    additionalInfo: "معلومات إضافية",
    uploadDate: "تاريخ الرفع",
    uploadedBy: "تم الرفع بواسطة",
    activityHistory: "سجل التعديلات",
    documentCreated: "تم الإنشاء",
    dataUpdated: "تعديل البيانات",
    documentUpdated: "تحديث المستند",
    
    // Chat Page
    aiAssistant: "مساعد الذكاء الاصطناعي",
    chatDescription: "اسألني أي شيء عن المستندات أو المستخدمين أو عمليات النظام",
    online: "متصل",
    newChat: "محادثة جديدة",
    clearChat: "مسح المحادثة",
    welcomeTitle: "مرحباً بك في مساعد DocuFlow الذكي!",
    welcomeMessage: "يمكنني مساعدتك في:",
    helpCreateDoc: "إنشاء مستندات جديدة",
    helpSearchDoc: "البحث عن المستندات",
    helpUpdateDoc: "تحديث المستندات الموجودة",
    helpUsers: "إدارة المستخدمين",
    helpReports: "إنشاء تقارير",
    startChat: "فقط اكتب سؤالك أدناه أو ارفع ملف للبدء!",
    messagePlaceholder: "اكتب رسالتك هنا...",
    send: "إرسال",
    aiTyping: "الذكاء الاصطناعي يكتب...",
  },
};

let currentLang = localStorage.getItem("lang") ?? "en";

/**
 * Sync current language with API server
 * - Sends language to API to store in session
 */
function syncLanguageWithAPI() {
  fetch('/doc/api/translate_api.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ lang: currentLang })
  })
  // .then(response => response.json())
  // .then(data => {
  //   if (data.status === 'error') {
  //     // console.warn('Failed to sync language with API:', data.message);
  //   }
  // })
  .catch(error => {
    console.warn('Language sync error:', error);
  });
}

/**
 * Apply selected language to all page elements
 * - Set page direction (rtl for Arabic, ltr for English)
 * - Translate text elements with data-i18n attribute
 * - Translate placeholder text in input fields data-i18n-placeholder attribute
 * - Sync language with API
 */
function applyLang() {
  // Set language and direction for the page
  document.documentElement.lang = currentLang;
  document.documentElement.dir = currentLang === "ar" ? "rtl" : "ltr";

  // Save language to localStorage for persistence
  localStorage.setItem("lang", currentLang);

  // Sync language with API server
  syncLanguageWithAPI();

  // Translate regular text elements
  document.querySelectorAll("[data-i18n]").forEach((el) => {
    const key = el.dataset.i18n;
    if (translations[currentLang][key]) {
      el.textContent = translations[currentLang][key];
    }
  });

  // Translate placeholder text in input fields
  document.querySelectorAll("[data-i18n-placeholder]").forEach((el) => {
    const key = el.dataset.i18nPlaceholder;
    if (translations[currentLang][key]) {
      el.placeholder = translations[currentLang][key];
    }
  });
}

/**
 * Toggle language between English and Arabic
 * - Switch current language
 * - Save new language to localStorage
 * - Apply new language to the page
 */
function toggleLang() {
  // Switch between English and Arabic
  currentLang = currentLang === "en" ? "ar" : "en";
  // Save new language to localStorage
  localStorage.setItem("lang", currentLang);
  // Apply new language to the page
  applyLang();
}
applyLang();
