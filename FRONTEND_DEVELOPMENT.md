# 🚀 Frontend Execution & Development Guide

## Step 1: Start Your Project (CHOOSE ONE METHOD)

### Method 1️⃣: XAMPP Apache (Best for beginners)

**Terminal 1 - Open XAMPP:**
```
1. Click XAMPP Control Panel icon
2. Click "Start" for Apache (green light aa jayegi)
3. Click "Start" for MySQL (green light aa jayegi)
4. Done!
```

**Browser mein kholo:**
```
http://localhost/banjara_matrimonial
```

---

### Method 2️⃣: Laravel Development Server (Better for development)

**Terminal 1 - Laravel Server:**
```powershell
cd c:\xampp\htdocs\banjara_matrimonial
c:\xampp\php\php.exe artisan serve
```

**Output hoga:**
```
Laravel development server started: http://127.0.0.1:8000
```

**Browser mein kholo:**
```
http://localhost:8000
```

---

## Step 2: Frontend Development Mode (IMPORTANT!)

### Hot Reload Setup (Auto-Refresh karne ke liye)

**Terminal 2 - NEW TERMINAL KHOLO:**
```powershell
cd c:\xampp\htdocs\banjara_matrimonial
npm run watch
```

**Output hoga:**
```
webpack is watching the files…
```

Ab ye terminal **khula rakho**! Ye automatically rebuild karega jab tum code change karoge.

---

## 🔄 Complete Workflow:

```
┌─────────────────────────────────────────────┐
│          TERMINAL 1 (Server)                │
│                                             │
│  c:\xampp\php\php.exe artisan serve        │
│  → Laravel Dev Server at :8000             │
└─────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────┐
│          TERMINAL 2 (Watcher)               │
│                                             │
│  npm run watch                              │
│  → Auto-rebuilds Vue + Tailwind            │
└─────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────┐
│          BROWSER                            │
│                                             │
│  http://localhost:8000                     │
│  → Your running site                       │
└─────────────────────────────────────────────┘
```

---

## 📝 File Editing & Development

### Kya Change Karoge, Kaha Change Karoge:

#### 1️⃣ **Vue Pages** (JavaScript + HTML)
```
Location: resources/js/Pages/FileName.vue

Example: resources/js/Pages/Welcome.vue
```

#### 2️⃣ **Reusable Components** (Buttons, Cards, etc.)
```
Location: resources/js/Components/FileName.vue

Example: resources/js/Components/Navigation.vue
```

#### 3️⃣ **Styling** (CSS/Tailwind)
```
Location: resources/css/app.css
```

#### 4️⃣ **JavaScript Logic** (API calls, data)
```
Location: resources/js/app.js
```

#### 5️⃣ **Backend Routes** (URLs)
```
Location: routes/web.php
```

---

## 🔧 Development Workflow Example

### Example 1: Welcome Page Change Karna

**File:** `resources/js/Pages/Welcome.vue`

1. **Edit kar do:**
   ```vue
   <h2 class="text-4xl font-bold text-gray-900 mb-4">
     MERA NAY TEXT 💍
   </h2>
   ```

2. **Save karo** (Ctrl+S)

3. **Terminal 2 mein dekho:**
   ```
   webpack is compiling...
   ✔ Compiled Successfully
   ```

4. **Browser mein F5 press karo** (Ya automatic refresh ho jayega)

5. **Change dekho!** ✅

---

### Example 2: Naya Button Add Karna

**file:** `resources/js/Pages/Welcome.vue`

```vue
<template>
  <MainLayout>
    <!-- ... existing code ... -->
    
    <!-- NEW BUTTON -->
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
      Mera Naya Button
    </button>
  </MainLayout>
</template>
```

Save karo → Browser refresh karo → Dekhega naya button!

---

### Example 3: Browse Page mein Filter Add Karna

**File:** `resources/js/Pages/Browse.vue`

Change script section:
```javascript
const filters = reactive({
  ageRange: '',
  religion: '',
  location: '',
  caste: '',
  education: '', // ← NEW FILTER
});
```

Add HTML:
```html
<div>
  <label>Education</label>
  <select v-model="filters.education">
    <option>All</option>
    <option>BE/BTech</option>
    <option>MBA</option>
  </select>
</div>
```

---

## 🎨 Tailwind CSS Classes (Styling ke liye)

### Most Used Classes:

```html
<!-- Text Size -->
<p class="text-sm">Small</p>
<p class="text-base">Normal</p>
<p class="text-lg">Large</p>
<p class="text-2xl">Extra Large</p>

<!-- Colors -->
<div class="bg-primary">Background Red</div>
<div class="bg-blue-600">Background Blue</div>
<div class="text-white">White Text</div>

<!-- Spacing -->
<div class="p-4">Padding 4</div>
<div class="m-4">Margin 4</div>
<div class="mt-6">Margin Top</div>

<!-- Grid/Flex -->
<div class="grid grid-cols-3">3 Columns</div>
<div class="grid grid-cols-1 md:grid-cols-2">1 on mobile, 2 on desktop</div>
<div class="flex gap-4">Flex with gap</div>

<!-- Buttons -->
<button class="btn-primary">Primary Button</button>
<button class="btn-secondary">Secondary Button</button>

<!-- Hover Effects -->
<button class="hover:bg-red-700 transition">Hover me</button>

<!-- Rounded Corners -->
<div class="rounded">Normal</div>
<div class="rounded-lg">Large rounded</div>
<div class="rounded-full">Full circle</div>
```

---

## 🐛 Debugging & Testing

### Console Errors Dekho:
```
Browser → F12 → Console Tab
```

### Laravel Errors Dekho:
```
Terminal mein jaha artisan serve chal raha hai
```

### Add Debugging:
```javascript
// Vue file mein
console.log('Debug:', variable);
console.log('Form data:', form);
```

Then browser Console (F12) mein dekho output.

---

## 🔄 Different Build Modes

### Development (Watch Mode - RECOMMENDED)
```powershell
npm run watch
# Auto-rebuilds, smaller files, slower performance
# USE THIS while developing
```

### Development One-Time Build
```powershell
npm run dev
# Build once, no watching
```

### Production Build (Optimized)
```powershell
npm run production
# Optimized files, minified, ready for deployment
# USE THIS before uploading to server
```

---

## 📁 Project File Structure (Development):

```
banjara_matrimonial/
├── resources/          ← EDIT THESE FILES
│   ├── js/
│   │   ├── Pages/      ← Page components
│   │   │   ├── Welcome.vue
│   │   │   ├── Browse.vue
│   │   │   ├── Login.vue
│   │   │   ├── Register.vue
│   │   │   ├── Dashboard.vue
│   │   │   └── Profiles.vue
│   │   ├── Components/ ← Reusable components
│   │   │   ├── Navigation.vue
│   │   │   └── Footer.vue
│   │   ├── Layouts/   ← Layout components
│   │   │   └── MainLayout.vue
│   │   └── app.js     ← Main JS file
│   └── css/
│       └── app.css    ← Tailwind CSS
│
├── routes/            ← EDIT web.php for new routes
│   └── web.php
│
├── public/            ← DON'T EDIT (auto-generated)
│   ├── js/
│   │   └── app.js     ← Compiled JS
│   └── css/
│       └── app.css    ← Compiled CSS
│
└── app/               ← Backend code (models, controllers)
    ├── Http/
    ├── Models/
    └── ...
```

---

## ⚡ Quick Commands Cheatsheet

```powershell
# Start Laravel Server
c:\xampp\php\php.exe artisan serve

# Start watch mode (auto-rebuild)
npm run watch

# One-time dev build
npm run dev

# Production build (optimized)
npm run production

# Clear Laravel cache
c:\xampp\php\php.exe artisan cache:clear

# Run migrations
c:\xampp\php\php.exe artisan migrate

# View Laravel logs
c:\xampp\php\php.exe artisan tinker
```

---

## 🎯 Typical Development Session:

### Start:
```
Terminal 1: c:\xampp\php\php.exe artisan serve
Terminal 2: npm run watch
Browser:   http://localhost:8000
```

### While Developing:
```
1. Edit file (e.g., Welcome.vue)
2. Save (Ctrl+S)
3. Watch terminal mein "Compiled Successfully" likhega
4. Browser auto-refresh (ya manual F5)
5. Changes visible!
```

### End:
```
Terminal 1: Ctrl+C (stop server)
Terminal 2: Ctrl+C (stop watcher)
```

---

## 🚨 Common Issues & Fixes

### Issue: Changes nahi dikha
```
✅ Solution: 
- npm run watch chal raha hai?
- Browser cache clear karo (Ctrl+Shift+Delete)
- F5 press karo
```

### Issue: Build error
```
✅ Solution:
- Terminal 2 ka error message dekho
- Syntax error fix karo (missing comma, bracket etc)
- npm run dev try karo
```

### Issue: Port 8000 already in use
```powershell
✅ Solution:
c:\xampp\php\php.exe artisan serve --port=8001
# Ab :8001 par chal jayega
```

### Issue: npm packages missing
```powershell
✅ Solution:
npm install
npm run watch
```

---

## 💡 Pro Tips for Development

1. **Browser DevTools Use Karo** (F12)
   - Elements tab → HTML structure dekho
   - Console tab → JavaScript errors
   - Network tab → API calls

2. **Vue DevTools Extension Install Karo**
   - Better debugging for Vue components
   - Data monitoring

3. **Editor Extensions**
   - Vetur (Vue support)
   - Tailwind CSS IntelliSense
   - Thunder Client (API testing)

4. **Hot Reload Benefits**
   - npm run watch use karo
   - CSS/JS/Vue auto-refresh
   - Time save hota hai

---

## 🎬 COMPLETE STEP-BY-STEP START:

```
Step 1: PowerShell kholo
Step 2: Terminal 1 mein:
  cd c:\xampp\htdocs\banjara_matrimonial
  c:\xampp\php\php.exe artisan serve

Step 3: WAIT for "(✓) Application ready on: http://127.0.0.1:8000"

Step 4: NEW PowerShell kholo

Step 5: Terminal 2 mein:
  cd c:\xampp\htdocs\banjara_matrimonial
  npm run watch

Step 6: WAIT for "webpack is watching..."

Step 7: Chrome mein kholo:
  http://localhost:8000

Step 8: Ab karo development!
```

---

## ✅ You're Ready!

Ab ekhi baar mein:
- ✅ Frontend run hoga
- ✅ Hot reload active hoga
- ✅ Code changes auto-rebuild hoga
- ✅ Happy coding! 🎉

**Questions? Check PROJECT_STARTUP.md for more details!**
