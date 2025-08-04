# Progress Manager Module

یک ماژول زیبا و مدرن برای نمایش progress bar در تمام صفحات پروژه.

## 🚀 ویژگی‌ها

- ✨ **طراحی مدرن و زیبا** با انیمیشن‌های نرم
- 📱 **Responsive** و سازگار با موبایل
- 🌙 **پشتیبانی از Dark Mode**
- ⚡ **سریع و سبک**
- 🔧 **قابل تنظیم** و انعطاف‌پذیر
- 🎯 **سهولت استفاده**

## 📦 نصب

### روش 1: استفاده از Blade Component (توصیه شده)

1. فایل `resources/views/components/progress-overlay.blade.php` را در پروژه قرار دهید
2. در صفحه مورد نظر:

```php
@extends('dashboard.master.master')

@section('content')
    <!-- Progress Overlay Component -->
    <x-progress-overlay 
        id="my-progress"
        message="در حال پردازش..."
        submessage="لطفاً صبر کنید"
        icon="fa-spinner"
    />
    
    <!-- محتوای صفحه -->
@endsection
```

### روش 2: استفاده از فایل‌های جداگانه

1. فایل‌های CSS و JS را در پروژه قرار دهید:
   - `public/assets/css/progress-manager.css`
   - `public/assets/js/progress-manager.js`

2. در layout اصلی یا صفحه مورد نظر:

```html
<!-- در head -->
<link rel="stylesheet" href="{{ asset('assets/css/progress-manager.css') }}">

<!-- قبل از closing body -->
<script src="{{ asset('assets/js/progress-manager.js') }}"></script>
```

## 🎯 نحوه استفاده

### استفاده ساده

```javascript
// نمایش progress bar
progressManager.show('در حال بارگذاری...', 'لطفاً صبر کنید', 'fa-spinner');

// به‌روزرسانی progress
progressManager.updateProgress(50, 'نیمی از کار انجام شد...');

// مخفی کردن
progressManager.hide();

// نمایش خطا
progressManager.error('خطایی رخ داد');

// نمایش موفقیت
progressManager.success('عملیات با موفقیت انجام شد');
```

### استفاده با AJAX

```javascript
// روش ساده
$.ajax({
    url: '/api/data',
    type: 'POST',
    data: formData,
    beforeSend: function() {
        progressManager.show('در حال ارسال...', 'لطفاً صبر کنید', 'fa-upload');
    },
    success: function(result) {
        progressManager.hide();
        // پردازش نتیجه
    },
    error: function() {
        progressManager.error('خطا در ارسال');
    }
});

// روش پیشرفته با progress tracking
$.ajax({
    url: '/api/upload',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    xhr: function() {
        const xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(evt) {
            if (evt.lengthComputable) {
                const percentComplete = (evt.loaded / evt.total) * 100;
                progressManager.updateProgress(percentComplete, `آپلود... ${Math.round(percentComplete)}%`);
            }
        }, false);
        return xhr;
    },
    beforeSend: function() {
        progressManager.show('آپلود فایل', 'در حال ارسال...', 'fa-upload');
    },
    success: function(result) {
        progressManager.hide();
    },
    error: function() {
        progressManager.error('خطا در آپلود');
    }
});
```

### استفاده با Helper Function

```javascript
// استفاده از ajaxWithProgress helper
ajaxWithProgress('/api/upload', formData, {
    progressMessage: 'آپلود فایل',
    progressSubmessage: 'در حال ارسال...',
    progressIcon: 'fa-upload',
    onSuccess: function(result) {
        console.log('موفقیت!', result);
    },
    onError: function(error) {
        console.error('خطا!', error);
    }
});
```

### Button Loading State

```javascript
// فعال کردن loading state برای دکمه
const button = document.getElementById('submit-btn');
setButtonLoading(button, true);

// غیرفعال کردن loading state
setButtonLoading(button, false);
```

## 🎨 سفارشی‌سازی

### تغییر رنگ‌ها

```css
/* تغییر رنگ اصلی */
.progress-fill {
    background: linear-gradient(90deg, #28a745, #20c997); /* سبز */
}

/* تغییر رنگ خطا */
.progress-fill.error {
    background: linear-gradient(90deg, #dc3545, #c82333);
}
```

### تغییر اندازه

```css
/* تغییر اندازه container */
.progress-container {
    max-width: 500px; /* بزرگتر */
    padding: 50px; /* padding بیشتر */
}

/* تغییر اندازه دایره */
.progress-circle {
    width: 100px;
    height: 100px;
}
```

### ایجاد Instance جدید

```javascript
// ایجاد progress manager جدید با ID متفاوت
const customProgress = new ProgressManager('custom-progress');

// استفاده
customProgress.show('عملیات سفارشی...', 'لطفاً صبر کنید', 'fa-cog');
```

## 📱 Responsive Design

این ماژول به صورت خودکار responsive است و در موبایل و تبلت به خوبی کار می‌کند.

## 🌙 Dark Mode

پشتیبانی خودکار از Dark Mode در سیستم‌هایی که از آن استفاده می‌کنند.

## 🔧 API Reference

### ProgressManager Methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `show(message, submessage, icon)` | `string, string, string` | نمایش progress bar |
| `updateProgress(percent, message)` | `number, string?` | به‌روزرسانی درصد پیشرفت |
| `hide()` | - | مخفی کردن progress bar |
| `error(message)` | `string` | نمایش خطا |
| `success(message)` | `string` | نمایش موفقیت |

### Helper Functions

| Function | Parameters | Description |
|----------|------------|-------------|
| `setButtonLoading(button, loading)` | `Element, boolean` | مدیریت loading state دکمه |
| `ajaxWithProgress(url, data, options)` | `string, object, object` | AJAX با progress tracking |

## 🎯 مثال‌های کاربردی

### فرم آپلود فایل

```javascript
$('#uploadForm').submit(function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const button = $(this).find('button[type="submit"]')[0];
    
    setButtonLoading(button, true);
    progressManager.show('آپلود فایل', 'در حال ارسال...', 'fa-upload');
    
    $.ajax({
        url: '/upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    const percent = (evt.loaded / evt.total) * 100;
                    progressManager.updateProgress(percent, `آپلود... ${Math.round(percent)}%`);
                }
            }, false);
            return xhr;
        },
        success: function(result) {
            setButtonLoading(button, false);
            progressManager.success('فایل با موفقیت آپلود شد');
        },
        error: function() {
            setButtonLoading(button, false);
            progressManager.error('خطا در آپلود فایل');
        }
    });
});
```

### لود کردن داده‌ها

```javascript
function loadData() {
    progressManager.show('بارگذاری داده‌ها', 'در حال دریافت...', 'fa-database');
    
    axios.get('/api/data')
        .then(response => {
            progressManager.hide();
            // پردازش داده‌ها
        })
        .catch(error => {
            progressManager.error('خطا در بارگذاری داده‌ها');
        });
}
```

## 🐛 عیب‌یابی

### مشکل: Progress bar نمایش داده نمی‌شود

1. مطمئن شوید که فایل‌های CSS و JS لود شده‌اند
2. console را چک کنید تا خطاهای JavaScript را ببینید
3. مطمئن شوید که `progressManager` در `window` تعریف شده است

### مشکل: انیمیشن‌ها کار نمی‌کنند

1. مطمئن شوید که CSS animations پشتیبانی می‌شوند
2. در برخی مرورگرهای قدیمی ممکن است نیاز به prefix باشد

## 📄 لایسنس

این ماژول برای استفاده در پروژه‌های شخصی و تجاری آزاد است.

## 🤝 مشارکت

برای بهبود این ماژول، پیشنهادات خود را ارائه دهید! 