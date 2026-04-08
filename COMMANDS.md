# ⚡ COPY-PASTE COMMANDS - KEEP THIS HANDY!

## 🚀 ONE-TIME SETUP (Do this once):

```powershell
# Navigate to project
cd c:\xampp\htdocs\banjara_matrimonial

# Install dependencies (if not already done)
npm install
```

---

## 🎬 EVERY TIME YOU DEVELOP (Do this each session):

### Terminal 1 - BACKEND (Copy & Run)
```powershell
cd c:\xampp\htdocs\banjara_matrimonial && c:\xampp\php\php.exe artisan serve
```
**Keep running** ✅

### Terminal 2 - FRONTEND (Copy & Run in NEW terminal)
```powershell
cd c:\xampp\htdocs\banjara_matrimonial && npm run watch
```
**Keep running** ✅

### Browser
```
http://localhost:8000
```
**Open & refresh with changes** ✅

---

## 📋 COMMON COMMANDS

### Build Frontend
```powershell
# Watch mode (AUTO-REBUILD) - USE THIS!
npm run watch

# One-time development build
npm run dev

# Production optimized build
npm run production
```

### Laravel Commands
```powershell
# Start dev server
c:\xampp\php\php.exe artisan serve

# Create database table
c:\xampp\php\php.exe artisan make:migration create_table_name

# Run all migrations
c:\xampp\php\php.exe artisan migrate

# Clear cache
c:\xampp\php\php.exe artisan cache:clear

# Reset database (CAUTION!)
c:\xampp\php\php.exe artisan migrate:reset

# Interactive shell for testing
c:\xampp\php\php.exe artisan tinker
```

### Create New Components/Models
```powershell
# Create model with migration
c:\xampp\php\php.exe artisan make:model Profile --migration

# Create controller
c:\xampp\php\php.exe artisan make:controller ProfileController

# Create request (for validation)
c:\xampp\php\php.exe artisan make:request StoreProfile

# Create migration
c:\xampp\php\php.exe artisan make:migration create_profiles_table
```

---

## 🎨 FILE LOCATIONS (Quick Reference)

```
Welcome Page:        resources/js/Pages/Welcome.vue
Login Page:          resources/js/Pages/Login.vue
Register Page:       resources/js/Pages/Register.vue
Browse Page:         resources/js/Pages/Browse.vue
Dashboard Page:      resources/js/Pages/Dashboard.vue

Navigation:          resources/js/Components/Navigation.vue
Footer:              resources/js/Components/Footer.vue
Main Layout:         resources/js/Layouts/MainLayout.vue

Styling:             resources/css/app.css
Routes:              routes/web.php
JavaScript Main:     resources/js/app.js

Build Config:        webpack.mix.js
Tailwind Config:     tailwind.config.js
PostCSS Config:      postcss.config.js
Environment:         .env
```

---

## 🎨 QUICK TAILWIND CLASSES (Copy-Paste)

### Common Styling
```html
<!-- Primary Button -->
<button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
  Button Text
</button>

<!-- Secondary Button -->
<button class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
  Button Text
</button>

<!-- Input Field -->
<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 focus:border-transparent" placeholder="Placeholder" />

<!-- Card -->
<div class="bg-white p-6 rounded-lg shadow-lg">
  Card content
</div>

<!-- Grid (3 columns on desktop, 1 on mobile) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <div>Column 1</div>
  <div>Column 2</div>
  <div>Column 3</div>
</div>

<!-- Centered Container -->
<div class="max-w-7xl mx-auto px-4">
  Content here
</div>

<!-- Flexbox Row -->
<div class="flex gap-4 items-center justify-between">
  Left item | Right item
</div>

<!-- Heading -->
<h1 class="text-4xl font-bold text-gray-900 mb-4">Heading</h1>

<!-- Paragraph -->
<p class="text-gray-600 text-base leading-relaxed">Text content</p>
```

---

## 🔧 KEYBOARD SHORTCUTS

```
EDITOR:
Ctrl+S       Save file
Ctrl+/       Toggle comment
Ctrl+P       Quick open file
Ctrl+H       Find & replace
F2           Rename variable
Ctrl+D       Select word
Alt+Up/Down  Move line

BROWSER:
F5           Refresh
Ctrl+Shift+R Hard refresh (clear cache)
F12          Open DevTools
Ctrl+Shift+M Mobile view
```

---

## 🚨 QUICK FIXES

```
# Clear frontend build
npm run dev

# Clear Laravel cache
c:\xampp\php\php.exe artisan cache:clear

# Hard refresh browser
Ctrl+Shift+Delete (to clear cache)

# Port already in use
c:\xampp\php\php.exe artisan serve --port=8001

# npm permission error
npm install --legacy-peer-deps

# Database errors
c:\xampp\php\php.exe artisan migrate:rollback
c:\xampp\php\php.exe artisan migrate
```

---

## ✏️ COMMON EDITS

### Change Homepage Text
File: `resources/js/Pages/Welcome.vue`
Find: `<h2>Find Your Perfect Match</h2>`
Change to: `<h2>Your New Text</h2>`
Save: Ctrl+S → Auto-refresh!

### Add New Button
```vue
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
  My Button
</button>
```

### Add New Form Field
```vue
<div>
  <label class="block text-sm font-medium text-gray-700 mb-2">Label</label>
  <input v-model="form.fieldName" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
</div>
```

### Add New Route
File: `routes/web.php`
```php
Route::get('/my-page', function () {
    return Inertia::render('MyPage');
})->name('my-page');
```

### Create New Page
1. Create: `resources/js/Pages/MyPage.vue`
2. Copy template from Welcome.vue
3. Add to: `resources/js/app.js` pages object
4. Add route in: `routes/web.php`
5. Save → Rebuild auto happens

---

## 📊 FILE TYPES

```
.vue     → Vue components (template + script + style)
.js      → JavaScript files
.css     → Stylesheets
.php     → PHP/Laravel files
.json    → Configuration files
.md      → Documentation files
```

---

## 🎯 DEVELOPMENT WORKFLOW

```
1. Open Terminal 1:
   → cd c:\xampp\htdocs\banjara_matrimonial
   → c:\xampp\php\php.exe artisan serve

2. Open Terminal 2:
   → cd c:\xampp\htdocs\banjara_matrimonial
   → npm run watch

3. Open Browser:
   → http://localhost:8000

4. Open Code Editor:
   → Open folder: c:\xampp\htdocs\banjara_matrimonial

5. Edit Files:
   → Make changes
   → Save (Ctrl+S)
   → Auto-rebuild happens
   → Auto-refresh in browser

6. Repeat:
   → Edit more files
   → See changes instantly
```

---

## 📝 TEMPLATE - Add New Vue Page

Copy this to `resources/js/Pages/NewPage.vue`:

```vue
<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto">
      <!-- Your content here -->
      <h1 class="text-4xl font-bold text-gray-900 mb-6">Page Title</h1>
      
      <button @click="handleAction" class="btn-primary">
        Click Me
      </button>
    </div>
  </MainLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const data = reactive({
  field: 'value',
});

const handleAction = () => {
  console.log('Action triggered!');
};
</script>
```

Then:
1. Add to `resources/js/app.js`
2. Create route in `routes/web.php`
3. Save → Done!

---

## 🎓 LEARNING RESOURCES

```
Vue 3 Docs:        https://vuejs.org
Tailwind CSS:      https://tailwindcss.com
Laravel:           https://laravel.com
Inertia.js:        https://inertiajs.com
MDN Web Docs:      https://developer.mozilla.org
```

---

## 💾 BACKUP & VERSION CONTROL

```powershell
# Initialize git
git init

# Stage all changes
git add .

# Commit
git commit -m "Initial setup"

# Check status
git status

# View logs
git log --oneline
```

---

## 🎯 NEXT STEPS (After Setup)

1. **Build Authentication**
   - Implement login/register
   - User sessions

2. **Create Database Models**
   - Profile model
   - Match model
   - Message model

3. **Build API Endpoints**
   - GET /api/profiles
   - POST /api/login
   - etc.

4. **Connect Frontend to Backend**
   - Axios API calls
   - Form submissions
   - Data display

5. **Add Features**
   - Search/filter
   - Messaging
   - Video calls
   - Admin panel

---

## ✅ DONE!

You now have:
- ✅ Complete setup
- ✅ All commands
- ✅ File locations
- ✅ Quick fixes
- ✅ Development workflow

**Start Building!** 🚀💍

---

**Questions? Check other markdown files in the project:**
- QUICK_START.md
- FRONTEND_DEVELOPMENT.md
- EXECUTION_GUIDE.md
- VISUAL_WORKFLOW.md
